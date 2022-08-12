<?php

/**
 * This class handles:
 * form submits for all PAYONE payment methods
 *
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone Payment Plugin for Shopware 5
 * @subpackage      Subscribers
 * @copyright       Copyright (c) 2016 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Stefan Müller <stefan.mueller@fatchip.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.fatchip.com
 */

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Mopt_PayonePaymentHelper;
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element;

class FrontendPostDispatch implements SubscriberInterface
{

    /**
     * di container
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    /**
     * path to plugin files
     *
     * @var string
     */
    private $path;

    /**
     * inject di container
     *
     * @param \Shopware\Components\DependencyInjection\Container $container
     */
    public function __construct(\Shopware\Components\DependencyInjection\Container $container, $path)
    {
        $this->container = $container;
        $this->path = $path;
    }

    /**
     * return array with all subsribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatchFrontend',
            'Enlight_Controller_Action_PostDispatch_Backend' => 'onPostDispatchBackend'
        );
    }

    /**
     * choose correct tpl folder
     *
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchBackend(\Enlight_Controller_ActionEventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();

        if (!$request->isDispatched() || $response->isException()) {
            return;
        }
        $this->container->get('template')->addTemplateDir($this->path . 'Views/');
    }

    /**
     * choose correct tpl folder and extend shopware templates
     *
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchFrontend(\Enlight_Controller_ActionEventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();
        $view = $args->getSubject()->View();
        /* @var $action \Enlight_Controller_Action */
        $action = $args->getSubject();


        if (!$request->isDispatched() || $response->isException()) {
            return;
        }

        // ignore ajax editor
        if ($action === 'ajaxEditor') {
            return;
        }

        $controllerName = $request->getControllerName();

        $this->setCorrectViewsFolder();

        $session = Shopware()->Session();

        // get paymentId from view instead of sGetUserData
         $paymentId = $view->sPayment['id'];
        // fallback to session if above does not work
        if (empty($paymentId)){
            $paymentId = (int)$this->container->get('session')->offsetGet('sPaymentID');
        }
        /** @var Mopt_PayonePaymentHelper $moptPaymentHelper */
        $moptPaymentHelper = $this->container->get('MoptPayoneMain')->getPaymentHelper();
        $moptPaymentName = $moptPaymentHelper->getPaymentNameFromId($paymentId);

        if ($session->moptMandateData) {
            $view->assign('moptMandateData', $session->moptMandateData);
        }

        $templateSuffix = '';
        if ($this->container->get('MoptPayoneMain')->getHelper()->isResponsive()) {
            $templateSuffix = '_responsive';
        }

        $view->extendsTemplate('frontend/checkout/mopt_confirm_payment' . $templateSuffix . '.tpl');
        $view->extendsTemplate('frontend/checkout/mopt_confirm' . $templateSuffix . '.tpl');
        $view->extendsTemplate('frontend/index/mopt_index' . $templateSuffix . '.tpl');
        if ($request->getActionName() == 'finish') {
            $view->extendsTemplate('frontend/checkout/mopt_finish' . $templateSuffix . '.tpl');
        }

        unset($session->moptMandateAgreement);
        if ($request->getParam('mandate_status')) {
            $session->moptMandateAgreement = $request->getParam('mandate_status');
        }
        if ($request->getParam('moptMandateConfirm')) {
            $session->moptMandateAgreement = $request->getParam('moptMandateConfirm');
        }

        if (in_array($controllerName, array('account', 'checkout', 'register'))) {
            $moptPayoneData = $this->moptPayoneCheckEnvironment($controllerName);
            // replace Ratepay Snippet ID with  value from global setting
            /** @var \Shopware\CustomModels\MoptPayoneConfig\Repository $repository */
            $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig');
            $global_config = $repository->getConfigByPaymentId(0, true);
            $moptPayoneData['moptRatepayConfig']['deviceFingerprintSnippetId'] = $global_config['ratepaySnippetId'];
            $view->assign('moptCreditCardCheckEnvironment', $moptPayoneData);
            $view->assign('fcPayolutionConfigDebitnote', $moptPayoneData['payolutionConfigDebitnote']);
            $view->assign('fcPayolutionConfigInvoice', $moptPayoneData['payolutionConfigInvoice']);
            $view->assign('fcPayolutionConfigInstallment', $moptPayoneData['payolutionConfigInstallment']);
            $view->assign('moptRatepayConfig', $moptPayoneData['moptRatepayConfig']);

            $moptPayoneFormData = array_merge((array)$view->sFormData, $moptPayoneData['sFormData']);
            $paymentName = $moptPaymentHelper->getPaymentNameFromId($moptPayoneFormData['payment']);
            if ($moptPaymentHelper->isPayoneCreditcardNotGrouped($paymentName)) {
                $moptPayoneFormData['payment'] = 'mopt_payone_creditcard';
            }
            if ($moptPaymentHelper->isPayoneKlarna($paymentName) && $moptPaymentHelper->isPayoneKlarnaGrouped($moptPayoneData['mopt_payone_klarna']['name'])) {
                $moptPayoneFormData['payment'] = 'mopt_payone_klarna';
            }

            if (!isset($moptPayoneFormData['mopt_payone__klarna_paymentname'])) {
                $moptPayoneFormData['mopt_payone__klarna_paymentname'] = $moptPaymentHelper->getPaymentNameFromId($session['sPaymentID']);
            }
            $view->assign('moptPayoneKlarnaGrouped', $moptPaymentHelper->isPayoneKlarnaGrouped($moptPayoneData['mopt_payone_klarna']['name']));
            $view->assign('sFormData', $moptPayoneFormData);
            $view->assign('moptPaymentConfigParams', $this->moptPaymentConfigParams($session->moptMandateDataDownload));
            $view->assign('moptMandateAgreementError', $session->moptMandateAgreementError);
            unset($session->moptMandateAgreementError);
        }

        if ($controllerName == 'account' && $request->getActionName() == 'index') {
            if ($session->moptAddressCheckNeedsUserVerification) {
                $view->assign('moptAddressCheckNeedsUserVerification', $session->moptAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/account/mopt_billing' . $templateSuffix . '.tpl');
            }
            if ($session->moptShippingAddressCheckNeedsUserVerification) {
                $view->assign('moptShippingAddressCheckNeedsUserVerification', $session->moptShippingAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/account/mopt_shipping' . $templateSuffix . '.tpl');
            }
        }

        if ($controllerName == 'account' && $request->getActionName() == 'payment') {
            if ($session->moptConsumerScoreCheckNeedsUserAgreement) {
                $view->assign('moptConsumerScoreCheckNeedsUserAgreement', $session->moptConsumerScoreCheckNeedsUserAgreement);
            } else {
                $view->assign('moptConsumerScoreCheckNeedsUserAgreement', false);
            }
            $view->extendsTemplate('frontend/account/mopt_consumescore' . $templateSuffix . '.tpl');
        }

        if (($controllerName == 'checkout' && $request->getActionName() == 'confirm')) {
            if ($session->moptAddressCheckNeedsUserVerification) {
                $view->assign('moptAddressCheckNeedsUserVerification', $session->moptAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/checkout/mopt_confirm' . $templateSuffix . '.tpl');
            }
            if ($session->moptShippingAddressCheckNeedsUserVerification) {
                $view->assign('moptShippingAddressCheckNeedsUserVerification', $session->moptShippingAddressCheckNeedsUserVerification);
                $view->extendsTemplate('frontend/checkout/mopt_shipping_confirm' . $templateSuffix . '.tpl');
            }
            $request = $args->getSubject()->Request();

            if ($request->getParam('moptAddressCheckNeedsUserVerification')) {
                $view->assign('moptAddressCheckNeedsUserVerification', $request->getParam('moptAddressCheckNeedsUserVerification'));
                $session->moptAddressCheckOriginalAddress = $request->getParam('moptAddressCheckOriginalAddress');
                $session->moptAddressCheckCorrectedAddress = $request->getParam('moptAddressCheckCorrectedAddress');
                $session->moptAddressCheckTarget = $request->getParam('moptAddressCheckTarget');
                $view->extendsTemplate('frontend/checkout/mopt_confirm' . $templateSuffix . '.tpl');
            }

            $moptPayoneFormData = array_merge($view->sFormData, $moptPayoneData['sFormData']);
            if ( $session->offsetGet('mopt_klarna_finalize_required') === "true" && (strpos($moptPayoneFormData['mopt_payone__klarna_paymentname'],'mopt_payone__fin_kdd_klarna_direct_debit') === 0 || strpos($moptPaymentName, 'mopt_payone__fin_kdd_klarna_direct_debit') === 0)) {
                $view->assign('mopt_klarna_client_token', $session->offsetGet('mopt_klarna_client_token'));
                $view->extendsTemplate('frontend/checkout/mopt_klarna_confirm' . $templateSuffix . '.tpl');
            }

            if ($request->getParam('moptShippingAddressCheckNeedsUserVerification')) {
                $view->assign('moptShippingAddressCheckNeedsUserVerification', $request->getParam('moptShippingAddressCheckNeedsUserVerification'));
                $session->moptShippingAddressCheckOriginalAddress = $request->getParam('moptShippingAddressCheckOriginalAddress');
                $session->moptShippingAddressCheckCorrectedAddress = $request->getParam('moptShippingAddressCheckCorrectedAddress');
                $session->moptShippingAddressCheckTarget = $request->getParam('moptShippingAddressCheckTarget');
                $view->extendsTemplate('frontend/checkout/mopt_shipping_confirm' . $templateSuffix . '.tpl');
            }

            if ($session->moptConsumerScoreCheckNeedsUserAgreement) {
                $view->assign('moptConsumerScoreCheckNeedsUserAgreement', $session->moptConsumerScoreCheckNeedsUserAgreement);
                $view->extendsTemplate('frontend/account/mopt_consumescore' . $templateSuffix . '.tpl');
            }
        }

        if (($controllerName == 'checkout' && $request->getActionName() == 'confirm')) {
            unset($session->moptBarzahlenCode);
        }

        // for amazon Pay redirect directly to finish instead of confirm

        if (($controllerName == 'checkout' && $request->getActionName() == 'confirm' && strpos($moptPaymentName, 'mopt_payone__ewallet_amazon_pay') === 0 && $session->offsetGet('moptFormSubmitted') === true)) {
            $action->forward('finish', 'moptPaymentAmazon', null, array('sAGB' => 'on'));
        }

        if ($controllerName == 'checkout' && $request->getActionName() == 'confirm' && $moptPaymentName === 'mopt_payone__ewallet_applepay') {

                $moptPayoneHelper = $this->container->get('MoptPayoneMain')->getInstance()->getHelper();
                $userData = Shopware()->Modules()->Admin()->sGetUserData();
                $debug = $moptPayoneData['moptApplepayConfig']['applepayDebug'] === true ? 1 : 0;
                $view->assign('mopt_applepay_country',  $moptPayoneHelper->getCountryIsoFromId($userData['billingaddress']['countryID']));
                $view->assign('mopt_applepay_currency', Shopware()->Config()->get('currency'));
                $view->assign('mopt_applepay_supportedNetworks', $this->getApplePayCreditcards($moptPayoneData['moptApplepayConfig']));
                $view->assign('mopt_applepay_merchantCapabilities', "['supports3DS', 'supportsDebit', 'supportsCredit']");
                $view->assign('mopt_applepay_label', Shopware()->Config()->get('shopname'));
                $view->assign('mopt_applepay_merchantIdentifier', $moptPayoneData['moptApplepayConfig']['applepayMerchantId']);
                $view->assign('mopt_applepay_debug', $debug);
                $view->extendsTemplate('frontend/checkout/mopt_confirm_applepay.tpl');
        }
        if ($controllerName == 'checkout' && $request->getActionName() == 'shippingPayment' && $moptPaymentName === 'mopt_payone__ewallet_applepay') {
            $view->assign('applepayNotConfiguredError', ! $this->isApplepayConfigured($moptPayoneData['moptApplepayConfig']));
        }

        // set flag to remove all address change buttons on confirm page
        $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $moptPaymentName);
        if (($controllerName == 'checkout' && $request->getActionName() == 'confirm' && in_array($cleanedPaymentName, \Mopt_PayoneConfig::PAYMENTS_EXCLUDED_FROM_SHIPPINGPAYMENTPAGE))) {
            $view->assign('moptDenyAddressChanges', true);
        }

        if (($controllerName == 'checkout' && $request->getActionName() == 'finish')) {
            if ($session->moptBarzahlenCode) {
                $view->assign('moptBarzahlenCode', $session->moptBarzahlenCode);
            }
            // cleanup sComment see #SW-151
            if (isset($session['sComment'])) {
                unset($session['sComment']);
            }

            // Klarna PayNow
            if ($session->offsetGet('mopt_klarna_client_token')) {
                unset($session['mopt_klarna_client_token']);
            }
        }

        if (($controllerName == 'checkout' && $request->getActionName() == 'confirm')) {
            if ($moptPaymentHelper->isPayoneRatepayInvoice($moptPaymentName) ||
                $moptPaymentHelper->isPayoneRatepayDirectDebit($moptPaymentName) ||
                $moptPaymentHelper->isPayoneRatepayInstallment($moptPaymentName)
            ) {
                if ($session->moptBillingCountryChanged) {
                    $action->redirect(
                        array(
                            'controller' => 'checkout',
                            'action' => 'shippingPayment',
                        )
                    );
                }
            }
            if ($moptPaymentHelper->isPayoneKlarnaInvoice($moptPaymentName) ||
                $moptPaymentHelper->isPayoneKlarnaDirectDebit($moptPaymentName) ||
                $moptPaymentHelper->isPayoneKlarnaInstallments($moptPaymentName)
            ) {
                if ($session->moptKlarnaAddressChanged) {
                    $action->redirect(
                        array(
                            'controller' => 'checkout',
                            'action' => 'shippingPayment',
                        )
                    );
                }
            }
        }

        if ($controllerName == 'checkout' && $request->getActionName() == 'cart' ) {
            $this->redirectExpressPaymentsOnBasketChange($moptPaymentName, $view);
            $this->redirectInstallmentPaymentsOnBasketChange($moptPaymentName, $view);
        }

        if (($controllerName == 'checkout' && $request->getActionName() == 'shippingPayment')) {
            $this->redirectExpressPaymentsOnBasketChange($moptPaymentName, $view);
            $this->redirectInstallmentPaymentsOnBasketChange($moptPaymentName, $view);
        }

        // used by ratepay installments
        // used by paypal installments
        // paypal express
        // redirect express payments to checkout/cart
        // installment payments to shipping/payment on Address or basket changes
        if (($controllerName == 'checkout' && $request->getActionName() == 'shippingPayment')) {
            if ($session->moptBillingCountryChanged) {
                unset($session->moptBillingCountryChanged);
                $redirectnotice =
                    Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                        ->get('ratepayCountryChanged',"<div style='text-align: center'><br><b>Sie haben Ihr Rechungsland geändert.<br>Bitte wiederholen Sie Ihre Zahlung oder wählen Sie eine andere Zahlart.<br></b></div>");

                $view->assign('moptBillingCountryChanged', true);
                $view->assign('moptOverlayRedirectNotice', $redirectnotice);
            }
            if ($session->moptKlarnaAddressChanged) {
                unset($session->moptKlarnaAddressChanged);
                $redirectnotice =
                    Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                        ->get('klarnaAddressChanged',"<div style='text-align: center'><br><b>Sie haben Ihre Adresse nachträglich geändert.<br>Bitte wiederholen Sie Ihre Zahlung oder wählen Sie eine andere Zahlart.<br></b></div>");

                $session->offsetUnset('mopt_klarna_client_token');
                $view->assign('moptKlarnaAddressChanged', true);
                $view->assign('moptOverlayRedirectNotice', $redirectnotice);
            }

            $payments = $view->getAssign('sPayments');
            foreach ($payments as $index => $payment) {
                // remove paypal for countries which need a state
                // in case no state for the country is supplied
                if ($moptPaymentHelper->isPayonePaypal($payment['name'])) {
                    if ($this->isStateNeeded()) {
                        $paypalIndex = $index;
                        unset ($payments[$paypalIndex]);
                    }
                }
                // remove Klarna payments according to supported country and currency combination
                if ($payment['name'] === 'mopt_payone_klarna' && !$this->isCountryCurrencySupportedFromKlarna()
                ) {
                    $klarnaIndex = $index;
                    unset ($payments[$klarnaIndex]);
                }
            }
            // remove other express payments
            $view->assign('sPayments', $moptPaymentHelper->filterExpressPayments($payments, $session));

        }

        if ($controllerName === 'address' &&
            $request->getActionName() === 'handleExtra' &&
            $session->offsetGet('mopt_klarna_client_token')
        ){
            $session->offsetSet('moptKlarnaAddressChanged', true);
        }

        if (($controllerName == 'account' && $request->getActionName() == 'payment')) {

            // remove express and installment payments from payment List
            $payments = $view->getAssign('sPaymentMeans');
            $filteredPayments = $moptPaymentHelper->filterPaymentsInAccount($payments);
            $view->assign('sPaymentMeans', $filteredPayments);
            // fallback if current payment is now exluded from payment list to make sure a payment is selected
            if (!array_key_exists($view->sUserData['additional']['user']['paymentID'], $filteredPayments)) {
                $view->assign('sFormData', ['payment' => Shopware()->Config()->get('paymentdefault')]);
            }
        }

        if (($controllerName == 'checkout' && $request->getActionName() == 'confirm')) {
            if ($session->get('moptAddressCorrected')) {
                unset($session->moptAddressCorrected);
                // refresh View Vars after automatic address correction (currently only used by SW 5.3)
                $view->sUserData = array_replace_recursive($view->sUserData, Shopware()->Modules()->Admin()->sGetUserData());
                // and update order variables in session
                $session['sOrderVariables'] = new \ArrayObject($view->getAssign(), \ArrayObject::ARRAY_AS_PROPS);
            }
        }

        if ($controllerName == 'moptAjaxPayone') {
            // add var to view Guest Users are prohibited from account controller in SW 5.3 so we use our own
            if (version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=') || Shopware()->Config()->get('version') == '___VERSION___'
            ) {
                $view->assign('useMoptAccountController', true);
            }

        }
    }

    /**
     * call responsive check method and set views folder according to result
     */
    public function setCorrectViewsFolder()
    {
        /** @var $shopContext \Shopware\Models\Shop\Shop */
        $shopContext = $this->container->get('bootstrap')->getResource('shop');
        $templateVersion = $shopContext->getTemplate()->getVersion();

        $this->container->get('template')->addTemplateDir($this->path . 'Views/');
    }

    protected function moptPayoneCheckEnvironment($controllerName = false)
    {
        $data = array();
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $userId = Shopware()->Session()->sUserId;
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $shopLanguage = explode('_', Shopware()->Shop()->getLocale()->getLocale());

        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

        $paymentMeans = Shopware()->Modules()->Admin()->sGetPaymentMeans();
        $groupedPaymentMeans = false;
        $data['moptPayolutionInformation'] = null;
        $data['moptRatepayConfig'] = null;

        /** @var Mopt_PayonePaymentHelper $paymentHelper */
        $paymentHelper = $this->container->get('MoptPayoneMain')->getPaymentHelper();

        if ($controllerName && $controllerName === 'checkout') {
            $paymentMeansWithGroupedCreditcard = $paymentHelper->groupCreditcards($paymentMeans);
            if ($paymentMeansWithGroupedCreditcard) {
                $groupedPaymentMeans = $paymentHelper->groupKlarnaPayments($paymentMeansWithGroupedCreditcard);
            } else {
                $groupedPaymentMeans = $paymentHelper->groupKlarnaPayments($paymentMeans);
            }

        }

        if ($groupedPaymentMeans) {
            $paymentMeans = $groupedPaymentMeans;
        }

        foreach ($paymentMeans as $paymentMean) {
            if ($paymentMean['id'] == 'mopt_payone_creditcard') {
                $paymentMean['mopt_payone_credit_cards'] = $moptPayoneMain->getPaymentHelper()
                    ->mapCardLetter($paymentMean['mopt_payone_credit_cards']);
                $data['mopt_payone_creditcard'] = $paymentMean;
            }

            if ($paymentHelper->isPayoneKlarnaGrouped($paymentMean['name'])) {
                foreach ($paymentMean['mopt_payone_klarna_payments'] as &$klarna_payment) {
                    $klarna_payment['financingtype'] = $paymentHelper->getKlarnaFinancingtypeByName($klarna_payment['name']);
                }

                $data['mopt_payone_klarna'] = $paymentMean;
            }

            if ($moptPayoneMain->getPaymentHelper()->isPayoneSofortuerberweisung($paymentMean['name'])) {
                $sofortConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptShowSofortIbanBic'] = $sofortConfig['showSofortIbanBic'];
            }

            if ($moptPayoneMain->getPaymentHelper()->isPayoneTrustly($paymentMean['name'])) {
                $trustlyConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptTrustlyShowIbanBic'] = (int) $trustlyConfig['trustlyShowIbanBic'];
            }

            if ($moptPayoneMain->getPaymentHelper()->isPayoneApplepay($paymentMean['name'])) {
                $data['moptApplepayConfig'] = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
            }

            //prepare additional Klarna information and retrieve birthday and phone nr from user data
            if (
                $moptPayoneMain->getPaymentHelper()->isPayoneKlarna_old($paymentMean['name'])
                || $moptPayoneMain->getPaymentHelper()->isPayoneKlarnaGrouped($paymentMean['name'])
            ) {
                $klarnaConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptKlarnaInformation'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetKlarnaAdditionalInformation($shopLanguage[1], $klarnaConfig['klarnaStoreId']);

                if (is_null($userData['additional']['user']['birthday'])) {
                    $birthday = '0000-00-00';

                } else {
                    $birthday = explode('-', $userData['additional']['user']['birthday']);
                }
                $data['mopt_payone__klarna_birthday'] = $birthday[2];
                $data['mopt_payone__klarna_birthmonth'] = $birthday[1];
                $data['mopt_payone__klarna_birthyear'] = $birthday[0];
                $data['mopt_payone__klarna_telephone'] = $userData['billingaddress']['phone'];
                $data['mopt_payone__klarna_personalId'] = $userData['additional']['user']['mopt_payone_klarna_personalid'];
            } elseif ($moptPayoneMain->getPaymentHelper()->isPayoneKlarna($paymentMean['name'])) {
                $klarnaConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptKlarnaInformation'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetKlarnaAdditionalInformation($shopLanguage[1], $klarnaConfig['klarnaStoreId']);
                $data['mopt_payone_klarna_financingtype'] = $paymentHelper->getKlarnaFinancingtypeByName($paymentMean['name']);
                $data['mopt_payone__klarna_paymentname'] = $paymentMean['name'];
                $data['mopt_payone_klarna_paymentid'] = $paymentMean['id'];
            }


            //prepare additional Payolution information and retrieve birthday from user data
            if ($moptPayoneMain->getPaymentHelper()->isPayonePayolutionDebitNote($paymentMean['name'])
            ) {
                $data['payolutionConfigDebitnote'] = $moptPayoneMain->getPayoneConfig($paymentMean['id']);

                $data['moptPayolutionInformation'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetPayolutionAdditionalInformation($shopLanguage[1], $data['payolutionConfigDebitnote']['payolutionCompanyName']);

                if (!isset($userData['additional']['user']['birthday'])) {
                    $userData['billingaddress']['birthday'] = "0000-00-00";
                } else {
                    $userData['billingaddress']['birthday'] = $userData['additional']['user']['birthday'];
                }

                $data['birthday'] = $userData['billingaddress']['birthday'];
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__payolution_debitnote_birthday'] = $birthday[2];
                $data['mopt_payone__payolution_debitnote_birthmonth'] = $birthday[1];
                $data['mopt_payone__payolution_debitnote_birthyear'] = $birthday[0];
                // Check if customer is older than 18 Years
                if (time() < strtotime('+18 years', strtotime($userData['billingaddress']['birthday']))) {
                    $data['birthdayunderage'] = "1";
                } else {
                    $data['birthdayunderage'] = "0";
                }
            }

            //prepare additional Payolution information and retrieve birthday from user data
            if ($moptPayoneMain->getPaymentHelper()->isPayonePayolutionInvoice($paymentMean['name'])
            ) {
                $data['payolutionConfigInvoice'] = $moptPayoneMain->getPayoneConfig($paymentMean['id']);

                $data['moptPayolutionInformation'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetPayolutionAdditionalInformation($shopLanguage[1], $data['payolutionConfigInvoice']['payolutionCompanyName']);

                if (!isset($userData['additional']['user']['birthday'])) {
                    $userData['billingaddress']['birthday'] = "0000-00-00";
                } else {
                    $userData['billingaddress']['birthday'] = $userData['additional']['user']['birthday'];
                }

                $data['birthday'] = $userData['billingaddress']['birthday'];
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__payolution_invoice_birthday'] = $birthday[2];
                $data['mopt_payone__payolution_invoice_birthmonth'] = $birthday[1];
                $data['mopt_payone__payolution_invoice_birthyear'] = $birthday[0];
                // Check if customer is older than 18 Years
                if (time() < strtotime('+18 years', strtotime($userData['billingaddress']['birthday']))) {
                    $data['birthdayunderage'] = "1";
                } else {
                    $data['birthdayunderage'] = "0";
                }
            }

            //prepare additional Payolution information and retrieve birthday from user data
            if ($moptPayoneMain->getPaymentHelper()->isPayonePayolutionInstallment($paymentMean['name'])
            ) {
                $data['payolutionConfigInstallment'] = $moptPayoneMain->getPayoneConfig($paymentMean['id']);

                $data['moptPayolutionInformation'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetPayolutionAdditionalInformation($shopLanguage[1], $data['payolutionConfigInstallment']['payolutionCompanyName']);

                if (!isset($userData['additional']['user']['birthday'])) {
                    $userData['billingaddress']['birthday'] = "0000-00-00";
                } else {
                    $userData['billingaddress']['birthday'] = $userData['additional']['user']['birthday'];
                }

                $data['birthday'] = $userData['billingaddress']['birthday'];
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__payolution_installment_birthday'] = $birthday[2];
                $data['mopt_payone__payolution_installment_birthmonth'] = $birthday[1];
                $data['mopt_payone__payolution_installment_birthday'] = $birthday[0];
                // Check if customer is older than 18 Years
                if (time() < strtotime('+18 years', strtotime($userData['billingaddress']['birthday']))) {
                    $data['birthdayunderage'] = "1";
                } else {
                    $data['birthdayunderage'] = "0";
                }
            }

            //prepare additional Ratepay information and retrieve birthday from user data
            if ($moptPayoneMain->getPaymentHelper()->isPayoneRatepayInvoice($paymentMean['name'])
                || $moptPayoneMain->getPaymentHelper()->isPayoneRatepayInstallment($paymentMean['name'])
                || $moptPayoneMain->getPaymentHelper()->isPayoneRatepayDirectDebit($paymentMean['name'])) {
                $data['moptRatepayConfig'] = $moptPayoneMain->getPayoneConfig($paymentMean['id']);

                $data['moptRatepayConfig'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetRatepayConfig($userData['additional']['country']['countryiso']);

                $data['moptRatepayConfig']['deviceFingerPrint'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetRatepayDeviceFingerprint();

                if (!isset($userData['additional']['user']['birthday'])) {
                    $userData['billingaddress']['birthday'] = "0000-00-00";
                } else {
                    $userData['billingaddress']['birthday'] = $userData['additional']['user']['birthday'];
                }

                $data['birthday'] = $userData['billingaddress']['birthday'];
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__ratepay_invoice_birthday'] = $birthday[2];
                $data['mopt_payone__ratepay_invoice_birthmonth'] = $birthday[1];
                $data['mopt_payone__ratepay_invoice_birthyear'] = $birthday[0];
                $data['mopt_payone__ratepay_invoice_telephone'] = $userData['billingaddress']['phone'];
                $data['mopt_payone__ratepay_installment_birthday'] = $birthday[2];
                $data['mopt_payone__ratepay_installment_birthmonth'] = $birthday[1];
                $data['mopt_payone__ratepay_installment_birthyear'] = $birthday[0];
                $data['mopt_payone__ratepay_installment_telephone'] = $userData['billingaddress']['phone'];
                $data['mopt_payone__ratepay_direct_debit_birthday'] = $birthday[2];
                $data['mopt_payone__ratepay_direct_debit_birthmonth'] = $birthday[1];
                $data['mopt_payone__ratepay_direct_debit_birthyear'] = $birthday[0];
                $data['mopt_payone__ratepay_direct_debit_telephone'] = $userData['billingaddress']['phone'];
            }

            if ($moptPayoneMain->getPaymentHelper()->isPayoneSafeInvoice($paymentMean['name'])
            ) {

                if (!isset($userData['additional']['user']['birthday'])) {
                    $userData['billingaddress']['birthday'] = "0000-00-00";
                } else {
                    $userData['billingaddress']['birthday'] = $userData['additional']['user']['birthday'];
                }

                $data['birthday'] = $userData['billingaddress']['birthday'];
                $birthday = explode('-', $userData['billingaddress']['birthday']);
                $data['mopt_payone__payone_safe_invoice_birthday'] = $birthday[2];
                $data['mopt_payone__payone_safe_invoice__birthmonth'] = $birthday[1];
                $data['mopt_payone__payone_safe_invoice__birthyear'] = $birthday[0];
            }
        }

        $payoneParams = $moptPayoneMain->getParamBuilder()->getBasicParameters();
        $creditCardConfig = $this->getCreditcardConfig(); //retrieve additional creditcardconfig

        $payoneParams['mid'] = $creditCardConfig['merchant_id'];
        $payoneParams['portalid'] = $creditCardConfig['portal_id'];
        $payoneParams['key'] = $creditCardConfig['api_key'];
        $payoneParams['aid'] = $creditCardConfig['subaccount_id'];

        if ($creditCardConfig['live_mode']) {
            $payoneParams['mode'] = 'live';
        } else {
            $payoneParams['mode'] = 'test';
        }
        $payoneParams['language'] = $shopLanguage[0];
        $payoneParams['errorMessages'] = json_encode($moptPayoneMain->getPaymentHelper()
            ->getCreditCardCheckErrorMessages());

        $generateHashService = $this->container->get('MoptPayoneBuilder')->buildServiceClientApiGenerateHash();

        $request = new \Payone_ClientApi_Request_CreditCardCheck();
        $params = array(
            'aid' => $payoneParams['aid'],
            'mid' => $payoneParams['mid'],
            'portalid' => $payoneParams['portalid'],
            'mode' => $payoneParams['mode'],
            'encoding' => 'UTF-8',
            'language' => $payoneParams['language'],
            'solution_version' => Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->getVersion(),
            'solution_name' => Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->getSolutionName(),
            'integrator_version' => Shopware()->Config()->Version,
            'integrator_name' => 'Shopware',
            'storecarddata' => 'yes',
        );
        $request->init($params);
        $request->setResponsetype('JSON');

        $payoneParams['hash'] = $generateHashService->generate($request, $creditCardConfig['api_key']);

        $data['moptPayoneCheckCc'] = $creditCardConfig['check_cc'];
        $data['moptCreditcardMinValid'] = (int)$creditCardConfig['creditcard_min_valid'];

        // remove the api key; only ['hash'] ist used
        $creditCardConfig['api_key'] = "";
        // to be safe also remove key in $payoneParams
        $payoneParams['key'] = "";
        // also remove key from array [jsonconfig]
        $json_tmp = json_decode($creditCardConfig['jsonConfig'], true);
        unset($json_tmp['api_key']);
        $creditCardConfig['jsonConfig'] = json_encode($json_tmp);
        $data['moptCreditcardConfig'] = $creditCardConfig;
        $data['moptPayoneParams'] = $payoneParams;

        if ($paymentData) {
            $data['sFormData'] = $paymentData;
        } else {
            $data['sFormData'] = array();
        }

        $data['moptAsyncAjax'] = $this->isAsnycAjax();

        return $data;
    }

    protected function moptPaymentConfigParams($mandateData)
    {
        $data = array();
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config = $moptPayoneMain->getPayoneConfig();


        $paymentMeans = Shopware()->Modules()->Admin()->sGetPaymentMeans();
        foreach ($paymentMeans as $paymentMean) {
            if ($moptPayoneMain->getPaymentHelper()->isPayoneDebitnote($paymentMean['name'])) {
                $data['moptDebitCountries'] = $moptPayoneMain->getPaymentHelper()
                    ->moptGetCountriesAssignedToPayment($paymentMean['id']);

                $debitConfig = $moptPayoneMain->getPayoneConfig($paymentMean['id']);
                $data['moptShowBic'] = $debitConfig['showBic'];

                break;
            }
        }

        //get country via user object
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        $data['moptShowAccountnumber'] = (bool)($debitConfig['showAccountnumber'] && $userData['additional']['country']['countryiso'] === 'DE');
        if (Shopware()->Config()->currency === 'CHF' && $userData['additional']['country']['countryiso'] === 'CH') {
            $data['moptIsSwiss'] = true;
        } else {
            $data['moptIsSwiss'] = false;
        }

        if ($mandateData) {
            $data['moptMandateDownloadEnabled'] = (bool)($config['mandateDownloadEnabled']);
        } else {
            $data['moptMandateDownloadEnabled'] = false;
        }

        return $data;
    }

    protected function getCreditcardConfig()
    {
        $shopId = $this->container->get('shop')->getId();

        $sql = 'SELECT * FROM s_plugin_mopt_payone_creditcard_config WHERE shop_id = ?';
        $configData = Shopware()->Db()->fetchRow($sql, $shopId);

        if (!$configData) {
            $sql = 'SELECT * FROM s_plugin_mopt_payone_creditcard_config WHERE is_default = ?';
            $configData = Shopware()->Db()->fetchRow($sql, true);
        }

        if (!$configData) {
            $configData = array('integration_type' => '1');
        }

        if ($configData['show_errors']) {
            $langSql = 'SELECT locale FROM s_core_locales WHERE id = ?';
            $locale = Shopware()->Db()->fetchOne($langSql, $configData['error_locale_id']);
            $locale = explode('_', $locale);
            $configData['error_locale_id'] = $locale[0];
        }

        $configData['jsonConfig'] = json_encode($configData);

        return $configData;
    }

    /** checks if state information is required for
     * certain countries
     * return true if state is mandatory for paypal and is available in shopware
     *
     * return bool
     */
    protected function isStateNeeded()
    {
        $return = false;
        $moptPayoneHelper = $this->container->get('MoptPayoneMain')->getInstance()->getHelper();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $countriesNeedState = array('JP', 'US', 'CA', 'MX', 'AR', 'BR', 'CN', 'ID', 'IN', 'TH');
        $billingCountryIso = $moptPayoneHelper->getCountryIsoFromId($userData['billingaddress']['countryID']);
        $shippingCountryIso = $moptPayoneHelper->getCountryIsoFromId($userData['shippingaddress']['countryID']);
        $billingStateIso = $moptPayoneHelper->getStateShortcodeFromId($userData['billingaddress']['countryID'], $userData['billingaddress']['stateID']);
        $shippingStateIso = $moptPayoneHelper->getStateShortcodeFromId($userData['shippingaddress']['countryID'], $userData['shippingaddress']['stateID']);

        if (!in_array($billingCountryIso, $countriesNeedState) && !in_array($shippingCountryIso, $countriesNeedState)) {
            return false;
        }
        if (in_array($billingCountryIso, $countriesNeedState) && empty($billingStateIso)) {
            $return = true;
        } else if (in_array($shippingCountryIso, $countriesNeedState) && empty($shippingStateIso)) {
            $return = true;
        }

        return $return;
    }

    /**
     * @return bool
     */
    protected function isAsnycAjax()
    {
        $shop = $this->container->get('shop');
        /** @var Template $template */
        $template = $shop->getTemplate();

        /** @var Element $templateElement */
        $templateElements = $template->getElements()->toArray();
        foreach ($templateElements as $templateElement) {
            $configName = $templateElement->getName();

            if ($configName === 'asyncJavascriptLoading') {
                $configVal = $templateElement->getValues()->getValues();
            }
        }

        // empty means default is set (>SW5.3 -> true)
        // so if there is an element in array it should be false
        if (Shopware()->Config()->get('version') === '___VERSION___' ||
            version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=')
        ) {
            if (!empty($configVal) && !$configVal[0]->getValue()) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Checks if klarna supports the user's billing country in combination
     * with currency
     * @see https://developers.klarna.com/documentation/klarna-payments/in-depth-knowledge/puchase-countries-currencies-locales/
     * @return bool
     */
    protected function isCountryCurrencySupportedFromKlarna()
    {
        $moptPayoneHelper = $this->container->get('MoptPayoneMain')->getInstance()->getHelper();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $billingCountryIso = $moptPayoneHelper->getCountryIsoFromId($userData['billingaddress']['countryID']);
        $currency = Shopware()->Config()->currency;

        $map = array (
            'DE' => 'EUR',
            'AT' => 'EUR',
            'CH' => 'CHF',
            'NL' => 'EUR',
            'DK' => 'DKK',
            'NO' => 'NOK',
            'SE' => 'SEK',
            'FI' => 'EUR',
            'GB' => 'GBP',
            'US' => 'USD',
            'AU' => 'AUD',
        );

        return ($map[$billingCountryIso] === $currency);
    }

    /**
     * @param $applepayConfig
     * @return string
     */
    protected function getApplePayCreditcards($applepayConfig)
    {
        //"['visa', 'masterCard', 'amex', 'discover', 'girocard']"
        $cards = "[";
        if ($applepayConfig['applepayVisa']){
            $cards .= "'visa',";
        }
        if ($applepayConfig['applepayMastercard']){
            $cards .= "'masterCard',";
        }
        if ($applepayConfig['applepayAmex']){
            $cards .= "'amex',";
        }
        if ($applepayConfig['applepayDiscover']){
            $cards .= "'discover',";
        }
        if ($applepayConfig['applepayGirocard']){
            $cards .= "'girocard',";
        }
        $cards .= "]";
        return $cards;
    }

    /**
     * @param $applepayConfig
     * @return int
     */
    protected function isApplepayConfigured($applepayConfig)
    {
        $return = 1;
        if (empty($applepayConfig['applepayMerchantId']) || empty($applepayConfig['applepayPrivateKey']) || empty($applepayConfig['applepayCertificate'])) {
            $return = 0;
        }
        return $return;
    }

    /**
     * @return void
     */
    protected function unsetExpressPaymentSessionVars() {
        $session = Shopware()->Session();
        unset($session->moptBasketChanged);
        unset($session->moptFormSubmitted);
    }

    /**
     * @return void
     */
    protected function unsetInstallmentPaymentSessionVars() {
        $session = Shopware()->Session();
        unset($session->moptBasketChanged);
        unset($session->moptPaypalInstallmentWorkerId);
        unset($session->moptPaypalInstallmentData);
    }

    /**
     * @param $paymentName
     * @param $view
     * @return void
     */
    protected function  redirectInstallmentPaymentsOnBasketChange($paymentName, $view) {
        $session = Shopware()->Session();
        $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);
        if (in_array( $cleanedPaymentName,\Mopt_PayoneConfig::PAYMENTS_INSTALLMENTS) && $session->moptBasketChanged === true) {
            $this->unsetInstallmentPaymentSessionVars();
            $redirectnotice =                     Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('installmentsBasketChanged',"<div style='text-align: center'><b>Ratenzahlung<br>Sie haben die Zusammenstellung Ihres Warenkobs geändert.<br>Bitte rufen Sie Ihre aktuellen Ratenzahlungskonditionen ab und wählen Sie den gewünschten Zahlplan aus.<br></b></div>");

            $view->assign('moptBasketChanged', true);
            $view->assign('moptOverlayRedirectNotice', $redirectnotice);
        }
    }

    /**
     * @param $paymentName
     * @param $view
     * @return void
     */
    protected function redirectExpressPaymentsOnBasketChange($paymentName, $view)
    {
        $session = Shopware()->Session();
        $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);
        if (in_array( $cleanedPaymentName,\Mopt_PayoneConfig::PAYMENTS_EXPRESS) && $session->moptBasketChanged === true) {
                $this->unsetExpressPaymentSessionVars();
                $redirectnotice =                     Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('expressBasketChanged',"<div style='text-align: center'><b>Express Checkout<br>Sie haben die Zusammenstellung Ihres Warenkobs geändert.<br>Bitte wiederholen Sie den Express Checkout oder wählen Sie eine andere Zahlart<br></b></div>");
                $view->assign('moptBasketChanged', true);
                $view->assign('moptOverlayRedirectNotice', $redirectnotice);
        }
    }
}
