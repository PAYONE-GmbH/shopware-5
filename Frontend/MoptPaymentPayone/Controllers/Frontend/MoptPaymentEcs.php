<?php

use Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneEnums;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneRequest;

class Shopware_Controllers_Frontend_MoptPaymentEcs extends Shopware_Controllers_Frontend_Payment
{
    protected $moptPayone__main = null;
    /** @var Mopt_PayoneHelper $moptPayone__helper */
    protected $moptPayone__helper = null;
    /** @var Mopt_PayoneUserHelper $moptPayone__paymentHelper */
    protected $moptPayone__paymentHelper = null;
    /** @var Mopt_PayoneUserHelper $payoneUserHelper */
    protected $payoneUserHelper = null;
    protected $admin;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();
        $this->admin = Shopware()->Modules()->Admin();
        $this->payoneUserHelper = $this->moptPayone__main->getUserHelper();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    public function initPaymentAction()
    {
        $session = Shopware()->Session();
        $paymentId = $session->moptPaypayEcsPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();
        $userData = $this->payoneUserHelper->getUserData();
        // Determinate tax automatically
        $taxAutoMode = Shopware()->Config()->get('sTAXAUTOMODE');
        $paymentConfig = $this->moptPayone__main->getPayoneConfig($paymentId);

        if (!empty($taxAutoMode)) {
            $discount_tax = Shopware()->Modules()->Basket()->getMaxTax() / 100;
        } else {
            $discount_tax = Shopware()->Config()->get('sDISCOUNTTAX');
            $discount_tax = empty($discount_tax) ? 0 : (float) str_replace(',', '.', $discount_tax) / 100;
        }
        $shippingCosts = $userData['additional']['show_net'] === true ? $this->request->getParam('shipping') : $this->request->getParam('shipping') * (1 + $discount_tax) ;
        // save to session for later use in PaypalExpressAction
        $session->moptPaypalExpressShipping = $shippingCosts;

        if ($paymentConfig['paypalExpressUseDefaultShipping']) {
            $amount = $this->payoneUserHelper->getBasketAmount($userData) + $shippingCosts;
        } else {
            $amount = $this->payoneUserHelper->getBasketAmount($userData);
        }
        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckout(
        $paymentId,
        $this->Front()->Router(),
        $amount,
        $this->getCurrencyShortName(),
        $userData
        );

        $request = new PayoneRequest(PayoneEnums::GenericpaymentAction_genericpayment, $expressCheckoutRequestData);
        $response = $request->request(PayoneEnums::GenericpaymentAction_genericpayment, $expressCheckoutRequestData);

        if ($response->getStatus() === PayoneEnums::REDIRECT) {
            $session->moptPaypalExpressWorkorderId = $response->get('workorderid');
            $this->redirect($response->getRedirecturl());
        } else if ($response->getStatus() === PayoneEnums::ERROR) {
            return $this->forward('paypalexpressError', null,null, ['errorCode' => $response->getErrorcode()]);
        } else {
            return $this->forward('paypalexpressAbort');
        }
    }

    /**
     * get plugin bootstrap
     *
     * @return plugin
     */
    protected function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * user returns succesfully from paypal
     * retrieve userdata now
     */
    public function paypalexpressAction()
    {
        $session = Shopware()->Session();
        $paymentId = (int)$session->moptPaypayEcsPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();
        $shippingCosts = $session->moptPaypalExpressShipping;
        $userData = $this->payoneUserHelper->getUserData();
        $paymentConfig = $this->moptPayone__main->getPayoneConfig($paymentId);

        if ($paymentConfig['paypalExpressUseDefaultShipping']) {
            $amount = $this->payoneUserHelper->getBasketAmount($userData) + $shippingCosts;
        } else {
            $amount = $this->payoneUserHelper->getBasketAmount($userData);
        }

        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckoutDetails(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData,
            $session->moptPaypalExpressWorkorderId
        );

        $request = new PayoneRequest(PayoneEnums::GenericpaymentAction_genericpayment, $expressCheckoutRequestData);
        $response = $request->request(PayoneEnums::GenericpaymentAction_genericpayment, $request);
        if ($response->getStatus() === PayoneEnums::OK) {
            $success = $this->payoneUserHelper->createOrUpdateUser($response, $paymentId, $session);
            $session->offsetSet('moptFormSubmitted', true);
            if ($success !== false) {
                $this->redirect(['controller' => 'checkout', 'action' => 'confirm']);
            } else {
                $this->redirect(['controller' => 'checkout', 'action' => 'cart']);
            }
        } else {
            $this->forward('paypalexpressError', null,null, ['errorCode' => $response->getErrorcode()]);
        }
    }

    public function paypalexpressabortAction()
    {
        $session = Shopware()->Session();
        $session->moptPayoneUserHelperError = true;
        $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
            ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
            ->get('errorMessageUserAbort');
        unset($session->moptPaypalExpressWorkorderId);
        unset($session->moptBasketChanged);

        $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }

    public function paypalexpressErrorAction()
    {
        $session = Shopware()->Session();
        $session->moptPayoneUserHelperError = true;
        $session->moptPayoneUserHelperErrorMessage = $this->moptPayone__paymentHelper->moptGetErrorMessageFromErrorCodeViaSnippet(false, $this->request->getParam('errorCode'));
        unset($session->moptPaypalExpressWorkorderId);
        unset($session->moptBasketChanged);
        $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }
}
