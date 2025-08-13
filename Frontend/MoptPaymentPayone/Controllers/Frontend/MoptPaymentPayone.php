<?php

use Shopware\Components\CSRFWhitelistAware;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneEnums;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneRequest;

/**
 * mopt payone payment controller
 */
class Shopware_Controllers_Frontend_MoptPaymentPayone extends Shopware_Controllers_Frontend_Payment implements
    CSRFWhitelistAware
{

    /**
     * Reference to sAdmin object (core/class/sAdmin.php)
     *
     * @var sAdmin
     */
    protected $admin;

    /**
     * PayoneMain
     * @var Mopt_PayoneMain
     */
    protected $moptPayoneMain = null;

    /**
     * PayoneMain
     * @var Mopt_PayonePaymentHelper
     */
    protected $moptPayonePaymentHelper = null;
    /** @var Enlight_Components_Session_Namespace $session */
    protected $session = null;

    /**
     * init payment controller
     */
    public function init()
    {
        $this->admin = Shopware()->Modules()->Admin();
        $this->moptPayoneMain = $this->Plugin()->get('MoptPayoneMain');
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $this->session = Shopware()->Session();
    }

    /**
     * whitelists Actions for CSRF checks
     *
     * it only used here to whitelist iDeal redirects, since
     * these use Post Requests
     *
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        if ($this->session->isIdealredirect) {
            return [
                'success',
                'failure',
                'cancel',
                'finishOrder',
            ];
        } else {
            return [];
        }
    }

    /**
     * check if everything is ok and proceed with payment
     *
     * @return void redirect to payment action or checkout controller
     */
    public function indexAction()
    {
        // check Basket Quantities before redirect
        $basket = Shopware()->Modules()->Basket();
        $checkQuantities = $basket->sCheckBasketQuantities();
        if (!empty($checkQuantities['hideBasket'])) {
            $this->redirect(array('controller' => 'checkout'));
            return;
        }

        if ($this->session->moptConsumerScoreCheckNeedsUserAgreement) {
            $this->redirect(array('controller' => 'checkout'));
            return;
        }

        if ($this->session->moptFormSubmitted) {
            unset($this->session->moptFormSubmitted);
        }

        $action = $this->moptPayonePaymentHelper->getActionFromPaymentName($this->getPaymentShortName());

        if ($action === 'debitnote') {
            if ($this->session->moptMandateAgreement === 'on') {
                $this->session->moptMandateAgreement = 1;
            }

            if ($this->session->moptMandateData['mopt_payone__showMandateText'] == true && (int)$this->session->moptMandateAgreement !== 1) {
                $this->session->moptMandateAgreementError = true;
                $this->session->moptFormSubmitted = true;
                $this->redirect(array('controller' => 'checkout', 'action' => 'confirm'));
                return;
            }
        }

        if ($action) {
            $this->redirect(array('action' => $action, 'forceSecure' => true));
        } else {
            $this->redirect(array('controller' => 'checkout'));
        }
    }

    public function creditcardAction()
    {
        $response = $this->mopt_payone__creditcard();
        if ($response->isRedirect()) {
            $this->mopt_payone__handleRedirectFeedback($response);
        } else {
            $this->mopt_payone__handleDirectFeedback($response);
        }
    }

    public function instanttransferAction()
    {
        $response = $this->mopt_payone__instanttransfer();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function paypalexpressAction()
    {
        $response = $this->mopt_payone__paypal(true);
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function paypalexpressv2Action()
    {
        $response = $this->mopt_payone__paypalv2(true);
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function paypalAction()
    {
        $response = $this->mopt_payone__paypal(false);

        if (Shopware()->Session()->moptPaypalEcsWorkerId) {
            $this->mopt_payone__handleDirectFeedback($response);
        } else {
            $this->mopt_payone__handleRedirectFeedback($response);
        }
    }

    public function paypalv2Action()
    {
        $response = $this->mopt_payone__paypalv2(false);

        if (Shopware()->Session()->moptPaypalv2EcsWorkerId) {
            $this->mopt_payone__handleDirectFeedback($response);
        } else {
            $this->mopt_payone__handleRedirectFeedback($response);
        }
    }

    public function debitnoteAction()
    {
        $response = $this->mopt_payone__debitnote();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function standardAction()
    {
        $response = $this->mopt_payone__standard();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function cashondelAction()
    {
        $response = $this->mopt_payone__cashondel();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function klarnaoldAction()
    {
        $response = $this->mopt_payone__klarna_old();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function klarnainstallmentsAction()
    {
        $response = $this->mopt_payone__klarna_installments();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function klarnainvoiceAction()
    {
        $response = $this->mopt_payone__klarna_invoice();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function klarnadirectdebitAction()
    {
        $response = $this->mopt_payone__klarna_direct_debit();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function applepayAction()
    {
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $token = $this->Request()->getParam('token');
        $response = $this->mopt_payone__applepay($token);
        $return = $this->mopt_payone__handleApplePayFeedback($response);
        echo $return;
    }

    public function googlepayAction()
    {
        $token = $this->Request()->getParam('token');
        $response = $this->mopt_payone__google_pay($token);
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function financeAction()
    {
        $response = $this->mopt_payone__finance();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function payolutioninvoiceAction()
    {
        $response = $this->mopt_payone__fin_payolution();
        $this->mopt_payone__handlePayolutionFeedback($response);
    }

    public function payolutiondebitAction()
    {
        $response = $this->mopt_payone__fin_payolution();
        $this->mopt_payone__handlePayolutionFeedback($response);
    }

    public function payolutioninstallmentAction()
    {
        $response = $this->mopt_payone__fin_payolution();
        $this->mopt_payone__handlePayolutionFeedback($response);
    }

    public function ratepayinvoiceAction()
    {
        $response = $this->mopt_payone__fin_ratepayinvoice();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function payonesecuredinvoiceAction()
    {
        $response = $this->mopt_payone__payonesecuredinvoice();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function payonesecuredinstallmentsAction()
    {
        $response = $this->mopt_payone__payonesecuredinstallments();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function payonesecureddirectdebitAction()
    {
        $response = $this->mopt_payone__payonesecureddirectdebit();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function ratepayinstallmentAction()
    {
        $response = $this->mopt_payone__fin_ratepayinstallment();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function ratepaydirectdebitAction()
    {
        $response = $this->mopt_payone__fin_ratepaydirectdebit();
        $this->mopt_payone__handleDirectFeedback($response);
    }

    public function alipayAction()
    {
        $response = $this->mopt_payone__alipay();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    public function wechatpayAction()
    {
        $response = $this->mopt_payone__wechatpay();
        $this->mopt_payone__handleRedirectFeedback($response);
    }

    /**
     * @return $response
     */
    protected function mopt_payone__creditcard()
    {
        $paymentData = $this->getPaymentData();
        $paymendId = $paymentData['mopt_payone__cc_paymentid'];
        $config = $this->moptPayoneMain->getPayoneConfig($paymendId);
        $payment = $this->moptPayoneMain->getParamBuilder()
            ->getPaymentCreditCard($this->Front()->Router(), $paymentData);
        $response = $this->buildAndCallPayment($config, 'cc', $payment);

        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__instanttransfer()
    {
        $paymentData = $this->getPaymentData();
        $paymentShortName = $this->getPaymentShortName();

        $paymentData['mopt_payone__onlinebanktransfertype'] = $this->moptPayonePaymentHelper
            ->getOnlineBankTransferTypeFromPaymentName($paymentShortName);

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $payment = $this->moptPayoneMain->getParamBuilder()
            ->getPaymentInstantBankTransfer($this->Front()->Router(), $paymentData);
        $response = $this->buildAndCallPayment($config, 'sb', $payment);

        return $response;
    }

    /**
     *
     * @param bool $isPaypalExpress
     *
     * @return $response
     */
    protected function mopt_payone__paypal($isPaypalExpress = false)
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $recurringOrder = false;
        $isInitialRecurringRequest = false;
        $forceAuthorize = false;

        if ($this->isRecurringOrder() || $this->moptPayoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            $recurringOrder = true;
            $forceAuthorize = true;
        }

        if ($recurringOrder && !isset(Shopware()->Session()->moptIsPaypalRecurringOrder)) {
            $isInitialRecurringRequest = true;
            $forceAuthorize = false;
        }

        if ($isPaypalExpress) {
            $workOrderId = Shopware()->Session()->moptPaypalExpressWorkorderId;
            $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPaypalEcs($this->Front()->Router());
        } else {
            $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPaypal(
                $this->Front()->Router(),
                $isInitialRecurringRequest
            );
            $workOrderId = false;
        }

        $response = $this->buildAndCallPayment(
            $config,
            'wlt',
            $payment,
            $workOrderId,
            $recurringOrder,
            $isInitialRecurringRequest,
            $forceAuthorize
        );

        return $response;
    }

    /**
     *
     * @param bool $isPaypalExpress
     *
     * @return $response
     */
    protected function mopt_payone__paypalv2($isPaypalExpress = false)
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $recurringOrder = false;
        $isInitialRecurringRequest = false;
        $forceAuthorize = false;

        if ($this->isRecurringOrder() || $this->moptPayoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            $recurringOrder = true;
            $forceAuthorize = true;
        }

        if ($recurringOrder && !isset(Shopware()->Session()->moptIsPaypalRecurringOrder)) {
            $isInitialRecurringRequest = true;
            $forceAuthorize = false;
        }

        if ($isPaypalExpress) {
            $workOrderId = Shopware()->Session()->moptPaypalv2ExpressWorkorderId;
            $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPaypalv2Ecs($this->Front()->Router());
        } else {
            $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPaypalv2(
                $this->Front()->Router(),
                $isInitialRecurringRequest
            );
            $workOrderId = false;
        }

        $response = $this->buildAndCallPayment(
            $config,
            'wlt',
            $payment,
            $workOrderId,
            $recurringOrder,
            $isInitialRecurringRequest,
            $forceAuthorize
        );

        return $response;
    }

    public function paypalRecurringSuccessAction()
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $recurringOrder = true;
        $customerPresent = false;
        $forceAuthorize = true;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPaypal($this->Front()->Router());

        $response = $this->buildAndCallPayment(
            $config,
            'wlt',
            $payment,
            false,
            $recurringOrder,
            $customerPresent,
            $forceAuthorize
        );

        $this->mopt_payone__handleDirectFeedback($response);
    }

    /**
     * @return $response
     */
    protected function mopt_payone__debitnote()
    {
        $paymentData = Shopware()->Session()->moptPayment;

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentDebitNote($paymentData);
        $response = $this->buildAndCallPayment($config, 'elv', $payment);

        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__standard()
    {
        $paymentId = $this->getPaymentShortName();
        $clearingSubType = false;

        if ($this->moptPayonePaymentHelper->isPayoneInvoice($paymentId)) {
            $clearingType = PayoneEnums::INVOICE;
        } elseif ($this->moptPayonePaymentHelper->isPayonePayInAdvance($paymentId)) {
            $clearingType = PayoneEnums::ADVANCEPAYMENT;
        } elseif ($this->moptPayonePaymentHelper->isPayoneSafeInvoice($paymentId)) {
            $clearingType = PayoneEnums::INVOICE;
            $clearingSubType = PayoneEnums::SAFEINVOICE;
        }

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $response = $this->buildAndCallPayment(
            $config,
            $clearingType,
            null,
            false,
            false,
            false,
            false,
            $clearingSubType
        );

        return $response;
    }

    /**
     * @return  $response
     */
    protected function mopt_payone__cashondel()
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentCashOnDelivery($this->getUserData());
        $response = $this->buildAndCallPayment($config, 'cod', $payment);

        return $response;
    }

    /**
     * @return  $response
     */
    protected function mopt_payone__klarna_old()
    {
        return $this->mopt_payone__klarna('old');
    }

    /**
     * @return  $response
     */
    protected function mopt_payone__klarna_installments()
    {
        return $this->mopt_payone__klarna('installments');
    }

    /**
     * @return $response
     */
    protected function mopt_payone__klarna_invoice()
    {
        return $this->mopt_payone__klarna('invoice');
    }

    /**
     * @return  $response
     */
    protected function mopt_payone__klarna_direct_debit()
    {
        return $this->mopt_payone__klarna('direct_debit');
    }

    /**
     * @return $response
     */
    protected function mopt_payone__payonesecuredinvoice()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::PIV;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPayoneSecuredInvoice($financeType, $paymentData);
        $response = $this->buildAndCallPayment($config, 'fnc', $payment);
        return $response;
    }

    /**
     * @return  $response
     */
    protected function mopt_payone__payonesecuredinstallments()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::PIN;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPayoneSecuredInstallments(
            $financeType,
            $paymentData
        );
        $response = $this->buildAndCallPayment(
            $config,
            'fnc',
            $payment,
            Shopware()->Session()->mopt_payone__payone_secured_installment_workorderid
        );
        return $response;
    }

    /**
     * @return  $response
     */
    protected function mopt_payone__payonesecureddirectdebit()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::PDD;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPayoneSecuredDirectdebit(
            $financeType,
            $paymentData
        );
        $response = $this->buildAndCallPayment($config, 'fnc', $payment);
        return $response;
    }

    /**
     * @param $paymentShortName
     *
     * @return $response
     */
    protected function mopt_payone__klarna($paymentShortName)
    {
        $financingTypes = [
            'old' => PayoneEnums::FinancingType_KLV,
            'installments' => PayoneEnums::FinancingType_KIS,
            'invoice' => PayoneEnums::FinancingType_KIV,
            'direct_debit' => PayoneEnums::FinancingType_KDD,
        ];

        $router = $this->Front()->Router();

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentKlarna(
            $financingTypes[$paymentShortName],
            $router
        );

        $workorderid = $this->session->offsetGet('mopt_klarna_workorderid');

        unset($this->session['mopt_klarna_workorderid']);

        $response = $this->buildAndCallPayment($config, 'fnc', $payment, $workorderid);

        return $response;
    }

    /**
     *
     * @return $response
     */
    protected function mopt_payone__applepay($token)
    {
        $router = $this->Front()->Router();

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentApplepay($router, $token);

        /** @var $response */

        $response = $this->buildAndCallPayment($config, 'wlt', $payment);

        return $response;
    }

    protected function mopt_payone__google_pay($token)
    {
        $router = $this->Front()->Router();

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentGooglePay($router, $token);

        /** @var $response */
        $response = $this->buildAndCallPayment($config, 'wlt', $payment);

        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__fin_payolution()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::PYV;
        $paymentType = PayoneEnums::PYV_FULL;
        if ($this->moptPayonePaymentHelper->isPayonePayolutionInvoice($this->getPaymentShortName())) {
            $precheckresponse = $this->buildAndCallPrecheck($config, 'fnc', $financeType, $paymentType, $paymentData);
            if ($precheckresponse->getStatus() == PayoneEnums::OK) {
                $workorderId = $precheckresponse->get('workorderid');
                $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPayolutionInvoice(
                    $financeType,
                    $paymentData,
                    $workorderId
                );
            } else {
                return $precheckresponse;
            }
        }
        if ($this->moptPayonePaymentHelper->isPayonePayolutionDebitNote($this->getPaymentShortName())) {
            $financeType = PayoneEnums::PYD;
            $paymentType = PayoneEnums::PYD_FULL;
            $precheckresponse = $this->buildAndCallPrecheck($config, 'fnc', $financeType, $paymentType, $paymentData);
            if ($precheckresponse->getStatus() == PayoneEnums::OK) {
                $workorderId = $precheckresponse->get('workorderid');
                $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPayolutionDebitNote(
                    $financeType,
                    $paymentData,
                    $workorderId
                );
            } else {
                return $precheckresponse;
            }
        }

        if ($this->moptPayonePaymentHelper->isPayonePayolutionInstallment($this->getPaymentShortName())) {
            $financeType = PayoneEnums::PYS;
            $workorderId = $paymentData['mopt_payone__fin_payolution_installment_workorderid'];
            $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentPayolutionInstallment(
                $financeType,
                $paymentData
            );
        }
        $response = $this->buildAndCallPayment($config, 'fnc', $payment, $workorderId);
        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__fin_ratepayinvoice()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::RPV;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentRatepayInvoice($financeType, $paymentData);
        $response = $this->buildAndCallPayment($config, 'fnc', $payment);
        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__fin_ratepayinstallment()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::RPS;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentRatepayInstallment($financeType, $paymentData);
        $response = $this->buildAndCallPayment($config, 'fnc', $payment);
        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__fin_ratepaydirectdebit()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = PayoneEnums::RPD;

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentRatepayDirectDebit($financeType, $paymentData);
        $response = $this->buildAndCallPayment($config, 'fnc', $payment);
        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__finance()
    {
        $paymentId = $this->getPaymentShortName();

        if ($this->moptPayonePaymentHelper->isPayoneBillsafe($paymentId)) {
            $financeType = PayoneEnums::BSV;
        }

        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentFinance($financeType, $this->Front()->Router());
        $response = $this->buildAndCallPayment($config, 'fnc', $payment);

        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__alipay()
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $recurringOrder = false;
        $isInitialRecurringRequest = false;
        $forceAuthorize = false;

        if ($this->isRecurringOrder() || $this->moptPayoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            $recurringOrder = true;
            $forceAuthorize = true;
        }

        if ($recurringOrder && !isset(Shopware()->Session()->moptIsAlipayRecurringOrder)) {
            $isInitialRecurringRequest = true;
            $forceAuthorize = false;
        }

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentAlipay(
            $this->Front()->Router(),
            $isInitialRecurringRequest
        );
        $response = $this->buildAndCallPayment(
            $config,
            'wlt',
            $payment,
            false,
            $recurringOrder,
            $isInitialRecurringRequest,
            $forceAuthorize
        );

        return $response;
    }

    /**
     * @return $response
     */
    protected function mopt_payone__wechatpay()
    {
        $paymentData = Shopware()->Session()->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());

        $payment = $this->moptPayoneMain->getParamBuilder()->getPaymentWechatpay(
            $this->Front()->Router(),
            $paymentData
        );
        $response = $this->buildAndCallPayment($config, 'wlt', $payment);
        return $response;
    }

    /**
     * this action is submitted to Payone with redirect payments
     * url is called when customer payment succeeds on 3rd party site
     */
    public function successAction()
    {
        $session = Shopware()->Session();
        $this->forward(
            'finishOrder',
            'MoptPaymentPayone',
            null,
            array('txid' => $session->txId, 'hash' => $session->moptPaymentReference)
        );
    }

    /**
     * this action is submitted to Payone with redirect payments
     * url is called when customer payment fails on 3rd party site
     */
    public function failureAction()
    {
        $session = Shopware()->Session();
        $errorMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get(
            'generalErrorMessage',
            'Es ist ein Fehler aufgetreten'
        );
        $errorInfoMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment')->get(
            'PaymentErrorInfo',
            'Bitte kontaktieren Sie den Shopbetreiber.'
        );
        $failInfoMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment')->get(
            'PaymentFailInfo',
            'Bitte versuchen Sie es mit einer anderen Zahlungsart nochmal.'
        );
        $session->payoneErrorMessage = $errorMessage;
        $session->otherErrorMessages = array(
            'contactShopOwner' => $errorInfoMessage,
            'otherPaymentMethod' => $failInfoMessage
        );
        $this->forward('error');
    }

    /**
     * this action is submitted to Payone with redirect payments
     * url is called when customer cancels redirect payment on 3rd party site
     */
    public function cancelAction()
    {
        $session = Shopware()->Session();
        $errorMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/messages')->get(
            'cancelMessage',
            'Der Bezahlvorgang wurde abgebrochen'
        );
        $session->payoneErrorMessage = $errorMessage;
        $session->otherErrorMessages = false;
        $this->forward('error');
    }

    /**
     * Returns the payment plugin config data.
     *
     * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * Cancel action method
     * renders automatically error.tpl, errormessage is already assigned e.g. mopt_payone__handleDirectFeedback
     */
    public function errorAction()
    {
        $session = Shopware()->Session();
        $session->offsetUnset('moptPaymentReference');
        $session->offsetUnset('moptBasketChanged');
        $session->offsetUnset('moptPaypalExpressWorkorderId');
        $session->offsetUnset('moptPaypalv2ExpressWorkorderId');
        $this->View()->errormessage = $session->payoneErrorMessage;
        if ($session->otherErrorMessages !== false) {
            $this->View()->contactShopOwner = $session->otherErrorMessages['contactShopOwner'];
            $this->View()->otherPaymentMethod = $session->otherErrorMessages['otherPaymentMethod'];
        }
    }

    /**
     * acutally save order
     *
     * @param string $txId
     * @param string $moptPaymentReference
     * @return void redirect to finish page
     */
    public function finishOrderAction()
    {
        // exit(); // uncomment for testing
        $txId = $this->Request()->getParam('txid');
        $moptPaymentReference = $this->Request()->getParam('hash');
        $session = Shopware()->Session();
        $orderIsCorrupted = false;

        if (!$this->isOrderFinished($txId)) {
            $orderHash = md5(serialize($session['sOrderVariables']));
            if ($session->moptOrderHash !== $orderHash) {
                $orderIsCorrupted = true;
                $orderNumber = $this->saveOrder($txId, $moptPaymentReference, 21);
                $orderObj = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(
                    ['transactionId' => $txId]
                );
                $comment = Shopware()->Snippets()
                        ->getNamespace('frontend/MoptPaymentPayone/messages')
                        ->get('fraudCommentPart1', false)
                    . ' ' . $orderNumber . ' '
                    . Shopware()->Snippets()
                        ->getNamespace('frontend/MoptPaymentPayone/messages')
                        ->get('fraudCommentPart2', false)
                    . ' ' . $txId . ' '
                    . Shopware()->Snippets()
                        ->getNamespace('frontend/MoptPaymentPayone/messages')
                        ->get('fraudCommentPart3', false);
                $orderObj->setInternalComment($comment);
                Shopware()->Models()->persist($orderObj);
                Shopware()->Models()->flush();
            } else {
                $this->saveOrder($txId, $moptPaymentReference);
            }
        }

        if (!empty($session['moptPaymentReference'])) {
            $session->offsetUnset('moptPaymentReference');
        }

        if ($session->moptClearingData) {
            $clearingData = json_encode($session->moptClearingData);
            unset($session->moptClearingData);
        }

        // Check for Payolution Clearing Data
        if ($session->fcPayolutionClearingData) {
            $payolutionClearingData = $session->fcPayolutionClearingData;
            unset($session->fcPayolutionClearingData);
        }

        $sql = 'SELECT `id` FROM `s_order` WHERE transactionID = ?'; //get order id
        $orderId = Shopware()->Db()->fetchOne($sql, $txId);

        if ($clearingData) {
            $sql = 'UPDATE `s_order_attributes`' .
                'SET mopt_payone_txid=?, mopt_payone_is_authorized=?, mopt_payone_payment_reference=?, '
                . 'mopt_payone_order_hash=?, mopt_payone_clearing_data=? WHERE orderID = ?';
            Shopware()->Db()->query($sql, array(
                $txId,
                $session->moptIsAuthorized,
                $session->moptPaymentReference,
                $session->moptOrderHash,
                $clearingData,
                $orderId
            ));
        } else {
            $sql = 'UPDATE `s_order_attributes`' .
                'SET mopt_payone_txid=?, mopt_payone_is_authorized=?, mopt_payone_payment_reference=?, '
                . 'mopt_payone_order_hash=? WHERE orderID = ?';
            Shopware()->Db()->query($sql, array(
                $txId,
                $session->moptIsAuthorized,
                $session->moptPaymentReference,
                $session->moptOrderHash,
                $orderId
            ));
        }

        if ($payolutionClearingData) {
            $payolutionClearingReference = $payolutionClearingData['add_paydata[clearing_reference]'];
            $payolutionWorkOrderId = $payolutionClearingData['add_paydata[workorderid]'];
            $sql = 'UPDATE `s_order_attributes`' .
                'SET mopt_payone_payolution_clearing_reference = ?, mopt_payone_payolution_workorder_id = ? WHERE orderID = ?';
            Shopware()->Db()->query($sql, array($payolutionClearingReference, $payolutionWorkOrderId, $orderId));
        }

        if ($session->moptIsAuthorized === true && !$orderIsCorrupted) {
            $order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(
                ['transactionId' => $txId]
            );
            if ($order) {
                $this->moptPayonePaymentHelper->markOrderDetailsAsFullyCaptured($order);
            }
        }

        if (Shopware()->Session()->moptSaveCreditcardData === true) {
            $customerId = Shopware()->Session()->offsetGet('sUserId');
            $this->moptPayonePaymentHelper->updateUserCreditcardInitialPaymentSuccess($customerId, true);
        }
        if (Shopware()->Session()->moptPayment) {
            $this->saveTransactionPaymentData($orderId, Shopware()->Session()->moptPayment);
        }

        $this->removeSessionVariablesOnOrderFinish();

        $this->redirect(array('controller' => 'checkout', 'action' => 'finish', 'sUniqueID' => $moptPaymentReference));
    }

    /**
     * Removing session variables that have no use anymore
     *
     * @param void
     * @return void
     */
    protected function removeSessionVariablesOnOrderFinish()
    {
        $session = Shopware()->Session();
        $session->offsetUnset('moptIsAuthorized');
        $session->offsetUnset('moptAgbChecked');
        $session->offsetUnset('moptPaymentReference');
        $session->offsetUnset('isIdealredirect');
        $session->offsetUnset('paySafeToken');
        $session->offsetUnset('moptRatepayCountry');
        $session->offsetUnset('moptBasketChanged');
        $session->offsetUnset('moptPaypalExpressWorkorderId');
        $session->offsetUnset('moptPaypalv2ExpressWorkorderId');
        $session->offsetUnset('moptSaveCreditcardData');
        $session->offsetUnset('moptPayment');
    }

    /**
     * handle direct feedback
     * on success save order
     *
     * @param $response
     */
    protected function mopt_payone__handleDirectFeedback($response)
    {
        $session = Shopware()->Session();
        $paymentId = $this->getPaymentShortName();

        if (
            $response->getStatus() == 'REDIRECT' &&
            ( $this->moptPayoneMain->getPaymentHelper()->isPayonePaypalExpress($paymentId) ||
                $this->moptPayoneMain->getPaymentHelper()->isPayonePaypalExpressv2($paymentId)  )
        )
        {
            $session->txId = $response->get('txid');
            $session->txStatus = $response->getStatus();
            $this->redirect($response->getRedirecturl());
            return;
        }
        if ($response->getStatus() == 'ERROR' &&
            ($paymentId === 'mopt_payone__fin_ratepay_invoice' ||
                $paymentId === 'mopt_payone__fin_ratepay_installment' ||
                $paymentId === 'mopt_payone__fin_ratepay_direct_debit')
        ) {
            $session->ratepayError = $response->get('customermessage');
            $session->offsetUnset('moptPaymentReference');
            // error code 307 = declined
            if ($response->getErrorcode() == '307') {
                // customer will not be able to use ratepay payments for 24 hours
                $this->moptPayoneMain->getHelper()->saveRatepayBanDate($session->get('sUserId'));
            }
            $this->forward('ratepayError');
        } elseif ($response->getStatus() == 'ERROR') {
            if (
                $paymentId === 'mopt_payone__fin_payone_secured_invoice' ||
                $paymentId === 'mopt_payone__fin_payone_secured_installment' ||
                $paymentId === 'mopt_payone__fin_payone_secured_directdebit'
            ) {
                $session->offsetUnset('moptPaymentReference');
                // error code 307 = declined
                if ($response->getErrorcode() == '307') {
                    $session->payoneSecuredDeclined = true;
                    $session->payoneSecuredError = $this->moptPayoneMain->getPaymentHelper()
                        ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                    $this->forward('payoneSecuredError');
                } else {
                    $session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                        ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                    $this->forward('error');
                }
            } else {
                $session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                    ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error');
            }
        } else {
            //extract possible clearing data
            $payolutionClearingData = $this->moptPayoneMain->getPaymentHelper(
            )->extractPayolutionClearingDataFromResponse($response);
            if ($payolutionClearingData) {
                $session->fcPayolutionClearingData = $payolutionClearingData;
            }

            //extract possible clearing data
            $clearingData = $this->moptPayoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);

            if ($clearingData) {
                $session->moptClearingData = $clearingData;
            }

            if ($session->moptPaypalExpressWorkorderId) {
                unset($session->moptPaypalExpressWorkorderId);
            }

            if ($session->moptPaypalv2ExpressWorkorderId) {
                unset($session->moptPaypalv2ExpressWorkorderId);
            }

            if ($session->moptMandateData) {
                $session->moptMandateDataDownload = $session->moptMandateData['mopt_payone__mandateIdentification'];
                unset($session->moptMandateData);
            }

            //save order
            $this->forward('finishOrder', 'MoptPaymentPayone', null, array(
                'txid' => $response->get('txid'),
                'hash' => $session->moptPaymentReference
            ));
        }
    }

    /**
     * handle direct feedback
     * on success save order
     *
     * @param $response
     */
    protected function mopt_payone__handleApplePayFeedback($response)
    {
        $session = Shopware()->Session();

        if ($response->getStatus() == 'ERROR') {
            $errorUrl = $this->Front()->Router()->assemble(
                [
                    'controller' => 'MoptPaymentPayone',
                    'action' => 'error',
                    'forceSecure' => true,
                    'appendSession' => false
                ]
            );
            unset($session->moptPaymentReference);
            return json_encode(['success' => false, 'url' => $errorUrl]);
        }
        $finishUrl = $this->Front()->Router()->assemble(
            [
                'controller' => 'MoptPaymentPayone',
                'action' => 'finishOrder',
                'forceSecure' => true,
                'appendSession' => false,
                'txid' => $response->get('txid'),
                'hash' => $session->moptPaymentReference
            ]
        );
        return json_encode(['success' => true, 'url' => $finishUrl]);
    }

    /**
     * handles redirect feedback
     * on success redirect customer to submitted(from Pay1) redirect url
     *
     * @param type $response
     */
    protected function mopt_payone__handleRedirectFeedback($response)
    {
        $session = Shopware()->Session();
        if ($response->getStatus() == 'ERROR') {
            $session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
            $this->forward('error');
        } else {
            $session->txId = $response->get('txid');
            $session->txStatus = $response->getStatus();
            $this->redirect($response->getRedirecturl());
        }
    }

    /**
     * handle direct feedback
     * on success save order
     *
     * @param $response
     */
    protected function mopt_payone__handlePayolutionFeedback($response)
    {
        $session = Shopware()->Session();

        if ($response->getStatus() === PayoneEnums::ERROR) {
            $session->payolutionErrorCode = $response->get('errorcode');
            $session->payolutionErrorMsg = $response->get('customermessage');
            $this->forward('payolutionError');
        } else {
            //extract possible clearing data
            $payolutionClearingData = $this->moptPayoneMain->getPaymentHelper(
            )->extractPayolutionClearingDataFromResponse($response);
            if ($payolutionClearingData) {
                $session->fcPayolutionClearingData = $payolutionClearingData;
            }

            //extract possible clearing data
            $clearingData = $this->moptPayoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);

            if ($clearingData) {
                $session->moptClearingData = $clearingData;
            }

            //save order
            $this->forward('finishOrder', 'MoptPaymentPayone', null, array(
                'txid' => $response->getTxid(),
                'hash' => $session->moptPaymentReference
            ));
        }
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param $payment
     * @param bool|string $workerId
     * @param bool $isPaypalRecurring
     * @param bool $isPaypalRecurringInitialRequest
     * @param bool $forceAuthorize
     * @param bool|string $clearingSubType ;
     * @return type $response
     */
    protected function buildAndCallPayment(
        $config,
        $clearingType,
        $payment,
        $workerId = false,
        $isPaypalRecurring = false,
        $isPaypalRecurringInitialRequest = false,
        $forceAuthorize = false,
        $clearingSubType = false
    ) {
        $paramBuilder = $this->moptPayoneMain->getParamBuilder();
        $session = Shopware()->Session();

        //create hash
        $orderVariables = $session['sOrderVariables'];
        $orderHash = md5(serialize($orderVariables));
        $session->moptOrderHash = $orderHash;
        $user = $this->getUser();
        $paymentName = $user['additional']['payment']['name'];

        if (!$forceAuthorize && ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung' || $isPaypalRecurringInitialRequest)) {
            $session->moptIsAuthorized = false;
        } else {
            $session->moptIsAuthorized = true;
        }

        $request = $this->mopt_payone__prepareRequest($config['paymentId'], $session->moptIsAuthorized);

        $currency = $this->moptGetCurrency();
        $request->set('amount', $this->getAmount());
        $request->set('currency', $currency);

        //get shopware temporary order id - session id
        $shopwareTemporaryId = $this->admin->sSYSTEM->sSESSION_ID;
        if ($this->moptPayonePaymentHelper->isPayoneCreditcard($paymentName) &&
            Shopware()->Session()->moptPayment['mopt_payone__cc_save_pseudocardnum_accept'] === "1" &&
            $user['additional']['user']['mopt_payone_creditcard_initial_payment'] === "0"
            ) {
            $request->set('recurrence', 'oneclick');
            $request->set('initial_payment','true');
        } else if ($this->moptPayonePaymentHelper->isPayoneCreditcard($paymentName) &&
            Shopware()->Session()->moptPayment['mopt_payone__cc_save_pseudocardnum_accept'] === "1" &&
            $user['additional']['user']['mopt_payone_creditcard_initial_payment'] === "1"
        ) {
            $request->set('recurrence', 'oneclick');
            $request->set('initial_payment','false');
        } else if ($this->moptPayonePaymentHelper->isPayoneCreditcard($paymentName) &&
            Shopware()->Session()->moptPayment['mopt_payone__cc_save_pseudocardnum_accept'] === "0" &&
            $user['additional']['user']['mopt_payone_creditcard_initial_payment'] === "0"
        ) {
            $request->set('recurrence', 'oneclick');
        }

        if ($this->moptPayonePaymentHelper->isPayoneRatepay($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneAmazonPay($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneApplepay($paymentName) ||
            $config['sendOrdernumberAsReference'] === true
        ) {
            $paymentReference = $this->moptPayoneMain->reserveOrdernumber();
        } else {
            $paymentReference = $paramBuilder->getParamPaymentReference();
        }
        $request->set('reference', $paymentReference);
        $transactionStatusPushCustomParam = 'session-' . Shopware()->Shop()->getId()
            . '|' . $this->admin->sSYSTEM->sSESSION_ID . '|' . $orderHash;
        $request->set('param', $transactionStatusPushCustomParam);

        if ($workerId) {
            $request->set('workorderid', $workerId);
        }

        if ($isPaypalRecurring && $isPaypalRecurringInitialRequest) {
            $request->set('amount',0.01);
            $request->set('recurrence','recurring');
        }

        if ($isPaypalRecurring && !$isPaypalRecurringInitialRequest) {
            $request->set('customer_is_present', 'no');
            $request->set('recurrence','recurring');
        }

        $session->moptPaymentReference = $paymentReference;
        $session->shopwareTemporaryId = $shopwareTemporaryId;

        if (!is_null($this->getUserData())) {
            $personalData = $paramBuilder->getPersonalData($this->getUserData());
            $request->add($personalData);
            if (! ($this->moptPayonePaymentHelper->isPayoneSecuredInvoice($paymentName) && $config['allowDifferentAddresses'] === false)){
                $deliveryData = $paramBuilder->getDeliveryData($this->getUserData());
                $request->add($deliveryData);
            } else {
                $deliveryData = $paramBuilder->getDeliveryData($this->getUserData());
                // replace shipping address with billing address
                $deliveryData['shipping_firstname'] = $personalData['firstname'];
                $deliveryData['shipping_lastname'] = $personalData['lastname'];
                $deliveryData['shipping_company'] = $personalData['company'];
                $deliveryData['shipping_street'] = $personalData['street'];
                $deliveryData['shipping_city'] = $personalData['city'];
                $deliveryData['shipping_state'] = $personalData['state'];
                $deliveryData['shipping_addressaddition'] = $personalData['additionalAddressLine1'];
                $deliveryData['shipping_country'] = $personalData['country'];
                $deliveryData['shipping_zip'] = $personalData['zipcode'];
                $request->add($deliveryData);
            }
        }

        $request->set('clearingtype', $clearingType);

        if ($clearingSubType !== false) {
            $request->set('clearingsubtype', $clearingSubType);
        }

        if ($this->moptPayonePaymentHelper->isPayoneSecuredDirectdebit($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneSecuredInstallments($paymentName)
        ) {
            $iban = preg_replace('/\s+/', '', $payment['iban']);
            $request->set('iban', $iban);
            $request->set('bankaccountholder', $payment['bankaccountholder']);
        }
        if ($this->moptPayonePaymentHelper->isPayoneSecuredInstallments($paymentName)
            || $this->moptPayonePaymentHelper->isPayoneSecuredInvoice($paymentName)
            || $this->moptPayonePaymentHelper->isPayoneSecuredDirectdebit($paymentName)
            || $this->moptPayonePaymentHelper->isPayonePaypalExpressv2($paymentName)
        ) {
            $config['submitBasket'] = true;
        }

        if (!$isPaypalRecurringInitialRequest && ($config['submitBasket'] || $clearingType === 'fnc')) {
            // although payolution is clearingtype fnc respect submitBasket setting in Backend
            if (!$config['submitBasket'] && ($this->moptPayonePaymentHelper->isPayonePayolutionDebitNote(
                        $paymentName
                    ) || $this->moptPayonePaymentHelper->isPayonePayolutionInvoice($paymentName)
                    || $this->moptPayonePaymentHelper->isPayoneSecuredInvoice(
                        $paymentName
                    ) || $this->moptPayonePaymentHelper->isPayoneSecuredInstallments(
                        $paymentName
                    ) || $this->moptPayonePaymentHelper->isPayoneSecuredDirectdebit($paymentName)
                )) {
                // do nothing
            } else {
                $orderId = $this->Request()->getParam('orderId');
                if ($orderId) {
                    //request was triggered from backend (abocommerce)
                    $order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId);
                    $orderPositions = array();
                    foreach ($order->getDetails() as $position) {
                        $orderPositions[] = $position->getId();
                    }

                    $invoicing = Mopt_PayoneMain::getInstance()->getParamBuilder()
                        ->getInvoicingFromOrder($order, $orderPositions, true, false, true);
                    $request->add($invoicing);
                } else { // request was triggered from fronted checkout
                    $request->add(
                        $paramBuilder->getInvoicing($this->getBasket(), $this->getShipment(), $this->getUserData())
                    );
                }
            }
        }

        // Check if basket contains only digital articles and
        // this is currently used only for paypal v2
        // checking wether esd is enabled in payment config is not neccessary
        if ($this->moptPayonePaymentHelper->isPayonePaypalv2($paymentName)
            && $this->isBasketDigital()) {
            $params['add_paydata[shopping_cart_type]'] = 'DIGITAL';
        }

        if ($this->moptPayonePaymentHelper->isPayoneSafeInvoice($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneInvoice($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneSecuredInvoice($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneSecuredInstallments($paymentName) ||
            $this->moptPayonePaymentHelper->isPayoneSecuredDirectdebit($paymentName)
        ) {
            if (!$personalData['company']) {
                $request->set('businessrelation', PayoneEnums::B2C);
            } else {
                $request->set('businessrelation', PayoneEnums::B2B);
            }
        }

        if ($payment) {
            $request->add($payment);
        }

        if (!$forceAuthorize && ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung' || $isPaypalRecurringInitialRequest)) {
            $response = $request->request($request::PREAUTH, $params);
        } else {
            $response = $request->request($request::AUTH, $params);
        }
        return $response;
    }

    /**
     * Returns matching currency depending on order situation (e. g. recurring order)
     *
     * @param void
     * @return string
     */
    protected function moptGetCurrency()
    {
        $isRecurringOrder = $this->isRecurringOrder();
        $currency = $this->getCurrencyShortName();

        if ($isRecurringOrder) {
            $orderCurrency = $this->moptGetOrderCurrencyById($currency);
            $currency = $orderCurrency;
        }

        return $currency;
    }

    /**
     * Returns the order currency short name from given orderid
     * uses currency param as fallback
     *
     * @param string $currency
     * @return string
     */
    protected function moptGetOrderCurrencyById($currency)
    {
        $orderId = $this->Request()->getParam('orderId');
        if ($orderId) {
            $sql = 'SELECT `currency` FROM `s_order` WHERE id = ?';
            $currency = Shopware()->Db()->fetchOne($sql, $orderId);
        }

        return $currency;
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param string $financetype
     * @param string $paymenttype
     * @return type $response
     */
    protected function buildAndCallPrecheck($config, $clearingType, $financetype, $paymenttype, $paymentData)
    {
        $paramBuilder = $this->moptPayoneMain->getParamBuilder();
        $session = Shopware()->Session();
        $personalData = $paramBuilder->getPersonalData($this->getUserData());
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;
        //create hash
        $orderVariables = $session['sOrderVariables'];
        $orderHash = md5(serialize($orderVariables));
        $session->moptOrderHash = $orderHash;

        $request = new PayoneRequest(PayoneEnums::GenericpaymentAction_genericpayment, $params);
        $request->add(['add_paydata[action]' => PayoneEnums::PAYOLUTION_PRE_CHECK]);
        $request->add(['add_paydata[payment_type]' => $paymenttype]);
        $request->add(['add_paydata[analysis_session_id]' => Shopware()->Session()->get('paySafeToken')]);

        if ($paymentData['mopt_payone__fin_payolution_debitnote_b2bmode'] || $paymentData['mopt_payone__fin_payolution_invoice_b2bmode'] || $paymentData['mopt_payone__fin_payolution_installment_b2bmode']) {
            $request->add(['add_paydata[b2b]' => 'yes']);
            $request->add(['add_paydata[company_trade_registry_number]' => $paymentData['mopt_payone__company_trade_registry_number']]);
        }
        $request->set('amount', $this->getAmount());
        $request->set('currency', $this->getCurrencyShortName());
        $request->set('company', $personalData['company']);
        $request->set('firstname', $personalData['firstname']);
        $request->set('lastname',$personalData['lastname']);
        $request->set('street', $personalData['street']);
        $request->set('zip', $personalData['zip']);
        $request->set('city', $personalData['city']);
        $request->set('country', $personalData['country']);
        if ($personalData['birthday'] !== "00000000" && $personalData['birthday'] !== "" && !is_null($personalData['birthday'])) {
            $request->set('birthday', $personalData['birthday']);
        } else {
            $request->set('birthday',$paymentData['dob']);
        }

        if ($paymentData && $paymentData['mopt_payone__fin_payolution_b2bmode']) {
            $request->set('birthday', "");
        }
        $request->set('email',$personalData['email']);
        $request->set('ip', $personalData['ip']);
        $request->set('language',$personalData['language']);
        $request->set('clearingtype', $clearingType);
        $response = $request->request(PayoneEnums::GenericpaymentAction_genericpayment, $params);
        return $response;
    }

    /**
     * initialize and return request object for authorize/preauthorize api call
     *
     * @param string $paymentId
     * @param bool $isAuthorized
     * @return PayoneRequest
     */
    protected function mopt_payone__prepareRequest($paymentId = 0, $isAuthorized = false)
    {
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($paymentId);
        $user = $this->getUser();
        if ($isAuthorized) {
            $request = new PayoneRequest('authorization', $params);
        } else {
            $request = new PayoneRequest('preauthorization', $params);
        }
        return $request;
    }

    /**
     * Get complete user-data as an array to use in view
     *
     * @return array
     */
    public function getUserData()
    {
        if ($this->isRecurringOrder()) {
            $orderVars = Shopware()->Session()->sOrderVariables;
            return $orderVars['sUserData'];
        }

        $system = Shopware()->System();
        $orderVariables = Shopware()->Session()->sOrderVariables;
        $userData = $orderVariables['sUserData'];
        if (!empty($userData['additional']['countryShipping'])) {
            $sTaxFree = false;
            if (!empty($userData['additional']['countryShipping']['taxfree'])) {
                $sTaxFree = true;
            } elseif (!empty($userData['additional']['countryShipping']['taxfree_ustid']) && !empty($userData['billingaddress']['ustid'])
            ) {
                $sTaxFree = true;
            }

            $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow(
                "
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ",
                array($system->sUSERGROUP)
            );

            if (!empty($sTaxFree)) {
                $system->sUSERGROUPDATA['tax'] = 0;
                $system->sCONFIG['sARTICLESOUTPUTNETTO'] = 1; //Old template
                Shopware()->Session()->sUserGroupData = $system->sUSERGROUPDATA;
                $userData['additional']['charge_vat'] = false;
                $userData['additional']['show_net'] = false;
                Shopware()->Session()->sOutputNet = true;
            } else {
                $userData['additional']['charge_vat'] = true;
                $userData['additional']['show_net'] = !empty($system->sUSERGROUPDATA['tax']);
                Shopware()->Session()->sOutputNet = (empty($system->sUSERGROUPDATA['tax']));
            }
        }

        return $userData;
    }

    /**
     * get actual payment method id
     *
     * @return string
     */
    protected function getPaymentId()
    {
        $user = $this->getUser();
        return $user['additional']['payment']['id'];
    }

    /**
     *  this action is called when sth. goes wrong during SEPA mandate PDF download
     */
    public function downloadErrorAction()
    {
        $this->View()->errormessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
            ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten');
    }

    /**
     *  this action is called when sth. goes wrong with payolution payments
     */
    public function payolutionErrorAction()
    {
        $session = Shopware()->Session();
        $session->offsetUnset('paySafeToken');
        $session->offsetUnset('moptPaymentReference');
        $errorCode = $session->payolutionErrorCode;
        $errorMessage = $session->payolutionErrorMsg;
        $this->View()->errormessage = Shopware()->Snippets()->getNamespace(
            'frontend/MoptPaymentPayone/errorMessages'
        )->get('errorMessage' . $errorCode, $errorMessage . ' (Fehler ' . $session->payolutionErrorCode . ')', true);
    }

    /**
     *  this action is called when sth. goes wrong with ratepay payments
     */
    public function ratepayErrorAction()
    {
        $session = Shopware()->Session();
        $session->offsetUnset('moptPaymentReference');
        $errorMessage = $session->ratepayError;
        $this->View()->errormessage = $errorMessage;
    }

    /**
     *  this action is called when sth. goes wrong with payonesecured payments
     */
    public function payoneSecuredErrorAction()
    {
        $session = Shopware()->Session();
        $session->offsetUnset('moptPaymentReference');
        $errorMessage = $session->payoneSecuredError;
        $this->View()->errormessage = $errorMessage;
    }

    /**
     * retrieve payment data
     *
     * @return array
     */
    protected function getPaymentData()
    {
        $userId = $this->session->sUserId;
        $paymentId = $this->session->sPaymentID;
        $moptPayonePaymentHelper = new Mopt_PayonePaymentHelper();
        $paymentName = $moptPayonePaymentHelper->getPaymentNameFromId($paymentId);
        if ($this->isRecurringOrder()) {
            $paymentData = $this->session->moptPayment;
        } elseif ($moptPayonePaymentHelper->isPayoneCreditcard($paymentName)) {
            $sql = 'SELECT `moptCreditcardPaymentData` FROM s_plugin_mopt_payone_creditcard_payment_data WHERE userId = ?';
            $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));
            if (!$paymentData && Shopware()->Session()->moptSaveCreditcardData === false) {
                $paymentData = $this->session->moptPayment;
            } else {
                Shopware()->Session()->moptPayment = $paymentData;
            }
        } else {
            $paymentData = $this->session->moptPayment;
        }

        return $paymentData;
    }

    /**
     * Returns the full basket data as array
     *
     * @return array
     */
    public function getShipment()
    {
        $session = Shopware()->Session();

        if (!empty($session->sOrderVariables['sDispatch'])) {
            return $session->sOrderVariables['sDispatch'];
        } else {
            return null;
        }
    }

    /**
     * Recurring payment action method.
     */
    public function recurringAction()
    {
        $orderNumber = $this->getOrderNumber();
        $amount = $this->getAmount();
        $isRecurringOrder = $this->isRecurringOrder();
        $redirectToCheckoutController = (
            (!$amount || $orderNumber) &&
            !$isRecurringOrder
        );

        if ($redirectToCheckoutController) {
            $this->redirect(array(
                'controller' => 'checkout'
            ));
            return;
        }

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $orderId = $this->Request()->getParam('orderId');
        Shopware()->Session()->moptPayment = $this->getPaymentDataFromOrder($orderId);

        if ($this->moptPayonePaymentHelper->isPayoneCreditcard($this->getPaymentShortName())) {
            Shopware()->Session()->moptOverwriteEcommerceMode = PayoneEnums::INTERNET;
        }

        if ($this->moptPayonePaymentHelper->isPayonePaypal($this->getPaymentShortName())) {
            Shopware()->Session()->moptIsPaypalRecurringOrder = true;
        }

        if ($this->moptPayonePaymentHelper->isPayoneAlipay($this->getPaymentShortName())) {
            Shopware()->Session()->moptIsAlipayRecurringOrder = true;
        }

        $action = 'mopt_payone__' . $this->moptPayonePaymentHelper
                ->getActionFromPaymentName($this->getPaymentShortName());

        if ($action == 'mopt_payone__fin_payolutiondebit' || $action == 'mopt_payone__fin_payolutioninvoice' || $action == 'mopt_payone__fin_payolutioninstallment') {
            $action = 'mopt_payone__fin_payolution';
        }

        $response = $this->$action();
        $errorMessage = false;
        if ($response->isRedirect()) {
            $errorMessage = 'Tried to use redirect payment for abo order';
        }

        $session = Shopware()->Session();

        if ($response->getStatus() == 'ERROR') {
            $errorMessage = $response->getErrorcode();
        }

        if (!$errorMessage) {
            $clearingData = $this->moptPayoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);
            $orderNr = $this->saveOrder($response->getTxid(), $session->moptPaymentReference);

            $sql = 'SELECT `id` FROM `s_order` WHERE ordernumber = ?'; // get order id
            $orderId = Shopware()->Db()->fetchOne($sql, $orderNr);

            if ($clearingData) {
                $sql = 'UPDATE `s_order_attributes`' .
                    'SET mopt_payone_txid=?, mopt_payone_is_authorized=?, '
                    . 'mopt_payone_clearing_data=? WHERE orderID = ?';
                Shopware()->Db()->query($sql, array(
                    $response->getTxid(),
                    $session->moptIsAuthorized,
                    json_encode($clearingData),
                    $orderId
                ));
            } else {
                $sql = 'UPDATE `s_order_attributes`' .
                    'SET mopt_payone_txid=?, mopt_payone_is_authorized=? WHERE orderID = ?';
                Shopware()->Db()->query($sql, array($response->getTxid(), $session->moptIsAuthorized, $orderId));
            }

            if (Shopware()->Session()->moptPayment) {
                $this->saveTransactionPaymentData($orderId, Shopware()->Session()->moptPayment);
            }

            unset($session->moptPayment);
            unset($session->moptIsAuthorized);
        }

        if ($this->Request()->isXmlHttpRequest()) {
            if ($errorMessage) {
                $data = array(
                    'success' => false,
                    'message' => $errorMessage
                );
            } else {
                $data = array(
                    'success' => true,
                    'data' => array(
                        array(
                            'orderNumber' => $orderNr,
                            'transactionId' => $response->getTxid(),
                        )
                    )
                );
            }
            echo Zend_Json::encode($data);
        } else {
            if ($errorMessage) {
                $this->View()->errormessage = $this->moptPayoneMain->getPaymentHelper()
                    ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error');
            } else {
                $this->redirect(array(
                    'controller' => 'checkout',
                    'action' => 'finish',
                    'sUniqueID' => $session->moptPaymentReference
                ));
            }
        }
    }

    /**
     * save payment data from actual transaction, used for abo commerce
     *
     * @param string $orderId
     * @param array $paymentData
     */
    protected function saveTransactionPaymentData($orderId, $paymentData)
    {
        $sql = 'UPDATE `s_order_attributes` SET mopt_payone_payment_data=? WHERE orderID = ?';
        Shopware()->Db()->query($sql, array(serialize($paymentData), $orderId));
    }

    /**
     * get payment data from order, used for abo commerce
     *
     * @param string $orderId
     * @return array
     */
    protected function getPaymentDataFromOrder($orderId)
    {
        $sql = 'SELECT `mopt_payone_payment_data` FROM `s_order_attributes` WHERE orderID = ?';
        $paymentData = Shopware()->Db()->fetchOne($sql, $orderId);

        return unserialize($paymentData);
    }

    /**
     * check if a recurring order is processed, used fpr abo commerce
     *
     * @return bool
     */
    protected function isRecurringOrder()
    {
        // check 1: isRecurring value in session
        // $session = Shopware()->Session();
        $session = $this->container->get('session');
        $isRecurringAboOrder = $session->offsetGet('isRecurringAboOrder');
        if (!$isRecurringAboOrder) {
            // this isn't a recurring abo order, so we can
            // pass all other checks
            return false;
        }

        // check 2: plugin exists and is installed
        $pluginManager = $this->container->get('shopware_plugininstaller.plugin_manager');
        try {
            $plugin = $pluginManager->getPluginByName('SwagAboCommerce');
        } catch (\Exception $e) {
            return false;
        }
        if (!$plugin->getInstalled()) {
            // if plugin is not installed it cannot be a recurring order indeed
            return false;
        }

        return true;
    }

    /**
     * determine wether order is already finished
     *
     * @param string $transactionId
     * @return boolean
     */
    protected function isOrderFinished($transactionId)
    {
        $sql = '
            SELECT ordernumber FROM s_order
            WHERE transactionID=? AND status!=-1';

        $orderNumber = Shopware()->Db()->fetchOne($sql, array($transactionId));

        if (empty($orderNumber)) {
            return false;
        } else {
            return true;
        }
    }

    protected function isBasketDigital()
    {
        $isDigitalOnly = true;
        $basketArticles = Shopware()->Db()->fetchAll(
            'SELECT id, esdarticle, ordernumber
            FROM s_order_basket
            WHERE sessionID = ?
            AND ordernumber != "sw-surcharge";',
            [$this->session->get('sessionId')]
        );

        foreach ($basketArticles as $article) {
            if ($article['esdarticle'] !== "1") {
                $isDigitalOnly = false;
            }
        }

        return $isDigitalOnly;
    }

}
