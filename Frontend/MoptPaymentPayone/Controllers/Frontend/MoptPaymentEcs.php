<?php

use Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog;

class Shopware_Controllers_Frontend_MoptPaymentEcs extends Shopware_Controllers_Frontend_Payment
{

    protected $moptPayone__serviceBuilder = null;
    /** @var Mopt_PayoneMain $moptPayone__main */
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
        $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
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
        $amount = $this->payoneUserHelper->getBasketAmount($userData);

        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckout(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        // Response with new workorderid and redirect-url to paypal
        $response = $service->request($request);

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::REDIRECT) {
            $session->moptPaypalExpressWorkorderId = $response->getWorkorderId();
            $this->redirect($response->getRedirecturl());
        } else if ($response->getStatus() === Payone_Api_Enum_ResponseType::ERROR) {
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

        $userData = $this->payoneUserHelper->getUserData();
        $amount = $this->payoneUserHelper->getBasketAmount($userData);

        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckoutDetails(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData,
            $session->moptPaypalExpressWorkorderId
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            MoptPayoneApiLog::class
        ));

        $response = $service->request($request);
        if ($response->getStatus() === Payone_Api_Enum_ResponseType::OK) {
            $success = $this->payoneUserHelper->createOrUpdateUser($response, $paymentId, $session);
            $session->offsetSet('moptFormSubmitted', true);
            if ($success !== false) {
                return $this->redirect(['controller' => 'checkout', 'action' => 'confirm']);
            } else {
                return $this->redirect(['controller' => 'checkout', 'action' => 'cart']);
            }
        } else {
            return $this->forward('paypalexpressError', null,null, ['errorCode' => $response->getErrorcode()]);;
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

        return $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }

    public function paypalexpressErrorAction()
    {
        $session = Shopware()->Session();
        $session->moptPayoneUserHelperError = true;
        $session->moptPayoneUserHelperErrorMessage = $this->moptPayone__paymentHelper->moptGetErrorMessageFromErrorCodeViaSnippet(false, $this->request->getParam('errorCode'));
        unset($session->moptPaypalExpressWorkorderId);
        unset($session->moptBasketChanged);
        return $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }
}
