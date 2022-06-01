<?php

use Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog;

class Shopware_Controllers_Frontend_moptPaymentPayDirekt extends Shopware_Controllers_Frontend_Payment
{

    protected $moptPayone__serviceBuilder = null;
    /** @var Mopt_PayoneMain $moptPayone__main */
    protected $moptPayone__main = null;
    /** @var Mopt_PayoneHelper $moptPayone__helper */
    protected $moptPayone__helper = null;
    /** @var Mopt_PayonePaymentHelper $moptPayone__paymentHelper */
    protected $moptPayone__paymentHelper = null;
    /** @var Mopt_PayoneUserHelper $payoneUserHelper */
    protected $payoneUserHelper = null;
    protected $admin;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();
        $this->payoneUserHelper = $this->moptPayone__main->getUserHelper();
        $this->admin = Shopware()->Modules()->Admin();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    public function initPaymentAction()
    {
        $session = Shopware()->Session();
        $paymentId = $session->moptPaydirektExpressPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();
        $basket = $this->moptPayone__main->sGetBasket();

        // set Dispatch
        $session['sDispatch'] = $this->getPaydirektExpressDispatchId();
        $shippingCosts = $this->getShippingCosts();

        $basket['sShippingcosts'] = $shippingCosts['brutto'];
        $basket['sShippingcostsWithTax'] = $shippingCosts['brutto'];
        $basket['sShippingcostsNet'] = $shippingCosts['netto'];
        $basket['sShippingcostsTax'] = $shippingCosts['tax'];

        $userData = $this->payoneUserHelper->getUserData();
        $amount =  $this->payoneUserHelper->getBasketAmount($userData);

        $amountWithShipping = $amount + $shippingCosts['brutto'];

        $expressCheckoutRequestData = $paramBuilder->buildPaydirektExpressCheckout(
            $paymentId,
            $this->Front()->Router(),
            $amountWithShipping,
            $this->getCurrencyShortName(),
            $userData
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $basketParams = $paramBuilder->getInvoicing($basket, true, $userData);
        $request->setInvoicing($basketParams);
        // Response with new workorderid and redirect-url to paydirekt
        $response = $service->request($request);

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::REDIRECT) {
            $session->moptPaydirektExpressWorkerId = $response->getWorkorderId();
            $this->redirect($response->getRedirecturl());
        } else {
            return $this->forward('paydirektexpressAbort');
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
    public function paydirektexpressAction()
    {
        $session = Shopware()->Session();
        $paymentId = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentPaydirektExpress()->getId();
        $paramBuilder = $this->moptPayone__main->getParamBuilder();

        $userData = $this->payoneUserHelper->getUserData();
        $amount = $this->payoneUserHelper->getBasketAmount($userData);

        $expressCheckoutRequestData = $paramBuilder->buildPaydirektExpressGetStatus(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData,
            $session->moptPaydirektExpressWorkerId
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            MoptPayoneApiLog::class
        ));

        $response = $service->request($request);

        $session['sPaymentID'] = $paymentId;

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::OK) {
            $session->offsetSet('moptFormSubmitted', true);
            $success = $this->payoneUserHelper->createOrUpdateUser($response, $paymentId, $session);
            if ($success === false) {
                return $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
            } else {
                return $this->redirect(array('controller' => 'checkout', 'action' => 'confirm'));
            }
        } else {
            return $this->forward('paydirektexpressAbort');
        }
    }

    public function paydirektexpressAbortAction()
    {
        $session = Shopware()->Session();
        $session->moptPayoneUserHelperError = true;
        $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
            ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
            ->get('errorMessageUserAbort');
        unset($session->moptPaydirektExpressWorkerId);
        unset($session->moptBasketChanged);

        return $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }

    /**
     * @param $payData
     * validate all important keys
     * @return integer
     */
    private function getPaydirektExpressDispatchId()
    {
        $config = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayDirektExpressConfig(Shopware()->Shop()->getId());
        return $config->getDispatchId();
    }

    /**
     * Get shipping costs as an array (brutto / netto) depending on selected country / payment
     *
     * @return array
     */
    public function getShippingCosts()
    {
        $session = Shopware()->Session();
        $country = [ 'id' =>  $session['sCountry'] ];
        $payment = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentPaydirektExpress();
        if (empty($country) || empty($payment)) {
            return ['brutto' => 0, 'netto' => 0];
        }
        $shippingcosts = Shopware()->Modules()->Admin()->sGetPremiumShippingcosts($country);
        return empty($shippingcosts) ? ['brutto' => 0, 'netto' => 0] : $shippingcosts;
    }

}
