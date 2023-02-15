<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Shopware\Plugins\MoptPaymentPayone\Bootstrap\RiskRules;
use Mopt_PayoneMain;

class Paymentfilter implements SubscriberInterface
{
    /**
     * di container
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    /**
     * inject di container
     *
     * @param \Shopware\Components\DependencyInjection\Container $container
     */
    public function __construct(\Shopware\Components\DependencyInjection\Container $container)
    {
        $this->container = $container;
    }

    /**
     * return array with all subsribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Shopware_Modules_Admin_GetPaymentMeans_DataFilter' => 'onGetPaymentsDataFilter',
        );
    }
    
    public function onGetPaymentsDataFilter(\Enlight_Event_EventArgs $args)
    {
        $result = $args->getReturn();
        $user = Shopware()->Modules()->Admin()->sGetUserData();
        // $amount = $this->payoneUserHelper->getBasketAmount($user);
        $billingCountry = $user['additional']['country']['countryiso'];

        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $ratepayconfig = $moptPayoneMain->getPaymentHelper()
            ->moptGetRatepayConfig($billingCountry);

        // only show "Payone Secure Invoice" when billing and shipping addresses are the same
        if (!empty($user['billingaddress']['id']) && $user['billingaddress']['id'] !== $user['shippingaddress']['id']) {
            foreach ($result as $index=>$payment) {
                if (strpos($payment['name'], 'mopt_payone__acc_payone_safe_invoice') === 0) {
                    unset($result[$index]);
                }
            }
        }

        $session = Shopware()->Session();
        $basketAmount = $session->get('sBasketAmount');
        $paymentId = $session['sPaymentID'];
        $dispatch = $session['sDispatch'];
        $shippingCountryID = $user['additional']['countryShipping']['id'];
        $test = Shopware()->Modules()->Admin()->sGetPremiumDispatches($shippingCountryID, $paymentId, false);

        $removeRatepayInstallment = false;
        $removeRatepayInvoice = false;
        $removeRatepayDirectDebit = false;

        $removePayoneSecuredInstallment = false;
        $removePayoneSecuredInvoice = false;
        $removePayoneSecuredDirectDebit = false;

        if (!$ratepayconfig) {
            foreach ($result as $index=>$payment) {
                if ($payment['name'] === 'mopt_payone__fin_ratepay_installment') {
                    $installmentIndex = $index;
                }
                if ($payment['name'] === 'mopt_payone__fin_ratepay_invoice') {
                    $invoiceIndex = $index;
                }
                if ($payment['name'] === 'mopt_payone__fin_ratepay_direct_debit') {
                    $directdebitIndex = $index;
                }
            }
            unset ($result[$installmentIndex]);
            unset ($result[$invoiceIndex]);
            unset ($result[$directdebitIndex]);
        }

        // check if ratepay ban date is set in customer attribute
        $moptHelper = $moptPayoneMain->getHelper();
        $userId = $session->get('sUserId');
        if ($userId){
            $banDate = $moptHelper->getRatepayBanDateFromUserId($userId);
            if ($banDate){
                $untilDate = $banDate->modify("+1 day");
                $now = date('Y-m-d');
                $nowDate = \DateTime::createFromFormat(
                    'Y-m-d',
                    $now
                );
                if ($nowDate < $untilDate ){
                    $removeRatepayInstallment = true;
                    $removeRatepayInvoice = true;
                    $removeRatepayDirectDebit = true;
                }
            }
        }

        foreach ($result as $index=>$payment) {
            if ($payment['name'] === 'mopt_payone__fin_ratepay_installment') {
                $installmentIndex = $index;
            }
            if ($payment['name'] === 'mopt_payone__fin_ratepay_invoice') {
                $invoiceIndex = $index;
            }
            if ($payment['name'] === 'mopt_payone__fin_ratepay_direct_debit') {
                $directdebitIndex = $index;
            }
            if ($payment['name'] === 'mopt_payone__fin_payone_secured_installment') {
                $payoneSecuredInstallmentIndex = $index;
            }
            if ($payment['name'] === 'mopt_payone__fin_payone_secured_invoice') {
                $payoneSecuredInvoiceIndex = $index;
            }
            if ($payment['name'] === 'mopt_payone__fin_payone_secured_directdebit') {
                $payoneSecuredDirectdebitIndex = $index;
            }
        }

        // check eligibility
        if ($ratepayconfig['eligibilityRatepayInvoice'] === false){
            $removeRatepayInvoice = true;
        }
        if ($ratepayconfig['eligibilityRatepayInstallment'] === false){
            $removeRatepayInstallment = true;
        }
        if ($ratepayconfig['eligibilityRatepayElv'] === false){
            $removeRatepayDirectDebit = true;
        }

        // check basket amounts
        if ($basketAmount < $ratepayconfig['txLimitInstallmentMin'] || $basketAmount > $ratepayconfig['txLimitInstallmentMax']) {
            $removeRatepayInstallment = true;
        }
        if ($basketAmount < $ratepayconfig['txLimitInvoiceMin'] || $basketAmount > $ratepayconfig['txLimitInvoiceMax']) {
            $removeRatepayInvoice = true;
        }
        if ($basketAmount < $ratepayconfig['txLimitElvMin'] || $basketAmount > $ratepayconfig['txLimitElvMax']) {
            $removeRatepayDirectDebit = true;
        }
        if ($basketAmount <= RiskRules::payoneSecuredInstallmentMinBasketAmount || $basketAmount >= RiskRules::payoneSecuredInstallmentMaxBasketAmount) {
            $removePayoneSecuredInstallment = true;
        }
        if ($basketAmount <= RiskRules::payoneSecuredInvoiceMinBasketAmount || $basketAmount >= RiskRules::payoneSecuredInvoiceMaxBasketAmount) {
            $removePayoneSecuredInvoice = true;
        }
        if ($basketAmount <= RiskRules::payoneSecuredDirectdebitMinBasketAmount || $basketAmount >= RiskRules::payoneSecuredDirectdebitMaxBasketAmount) {
            $removePayoneSecuredDirectDebit = true;
        }
        if ($removeRatepayInstallment) {
            unset ($result[$installmentIndex]);
        }
        if ($removeRatepayInvoice) {
            unset ($result[$invoiceIndex]);
        }
        if ($removeRatepayDirectDebit) {
            unset ($result[$directdebitIndex]);
        }
        if ($removePayoneSecuredInstallment) {
            unset ($result[$payoneSecuredInstallmentIndex]);
        }
        if ($removePayoneSecuredInvoice) {
            unset ($result[$payoneSecuredInvoiceIndex]);
        }
        if ($removePayoneSecuredDirectDebit) {
            unset ($result[$payoneSecuredDirectdebitIndex]);
        }

        return $result;
    }
}
