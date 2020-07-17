<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

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
        $billingCountry = $user['additional']['country']['countryiso'];

        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $ratepayconfig = $moptPayoneMain->getPaymentHelper()
            ->moptGetRatepayConfig($billingCountry);

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
            return $result;
        }

        $session = Shopware()->Session();
        $basketAmount = $session->get('sBasketAmount');

        $removeInstallment = false;
        $removeInvoice = false;
        $removeDirectDebit = false;

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
                    $removeInstallment = true;
                    $removeInvoice = true;
                    $removeDirectDebit = true;
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
        }

        // check eligibility
        if ($ratepayconfig['eligibilityRatepayInvoice'] === false){
            $removeInvoice = true;
        }
        if ($ratepayconfig['eligibilityRatepayInstallment'] === false){
            $removeInstallment = true;
        }
        if ($ratepayconfig['eligibilityRatepayElv'] === false){
            $removeDirectDebit = true;
        }

        // check basket amounts
        if ($basketAmount < $ratepayconfig['txLimitInstallmentMin'] || $basketAmount > $ratepayconfig['txLimitInstallmentMax']) {
            $removeInstallment = true;
        }
        if ($basketAmount < $ratepayconfig['txLimitInvoiceMin'] || $basketAmount > $ratepayconfig['txLimitInvoiceMax']) {
            $removeInvoice = true;
        }
        if ($basketAmount < $ratepayconfig['txLimitElvMin'] || $basketAmount > $ratepayconfig['txLimitElvMax']) {
            $removeDirectDebit = true;
        }
        if ($removeInstallment) {
            unset ($result[$installmentIndex]);
        }
        if ($removeInvoice) {
            unset ($result[$invoiceIndex]);
        }
        if ($removeDirectDebit) {
            unset ($result[$directdebitIndex]);
        }

        return $result;
    }
}
