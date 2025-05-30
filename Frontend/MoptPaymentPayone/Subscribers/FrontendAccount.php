<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class FrontendAccount implements SubscriberInterface
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
            // load payment data from db to use for payment
            'Shopware_Controllers_Frontend_Account::paymentAction::after' => 'onPaymentAction',
            'Shopware_Controllers_Frontend_Account::savePaymentAction::after' => 'onPaymentSaveAction',
        );
    }

    /**
     * assign saved paymend data to view
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onPaymentAction(\Enlight_Hook_HookArgs $arguments)
    {
        $subject = $arguments->getSubject();
        $request = $subject->Request();
        $controllerName = $request->getControllerName();
        $userId = Shopware()->Session()->sUserId;

        $sql = 'SELECT `moptCreditcardPaymentData` FROM s_plugin_mopt_payone_creditcard_payment_data WHERE userId = ?';
        $creditcardPaymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));
        $creditcardPaymentData = (empty($creditcardPaymentData)) ? [] : $creditcardPaymentData;
        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));
        $paymentData = (empty($paymentData)) ? [] : $paymentData;
        $paymentData = array_merge($creditcardPaymentData, $paymentData);

        if (!empty($paymentData)) {
            //get array of creditcard payment ids
            $sql = "SELECT `id` FROM s_core_paymentmeans WHERE name LIKE '%mopt_payone__cc_%' ";
            $paymentData['payment'] = 'mopt_payone_creditcard';
            $subject->View()->sFormData = $paymentData;
            $subject->View()->sUserData += $paymentData;
        }

        if ($controllerName == 'account') {
            $userData = Shopware()->Modules()->Admin()->sGetUserData();
            $showmoptCreditCardAgreement = $userData['additional']['user']['accountmode'] == "0" && (! isset(Shopware()->Session()->moptPayment) || Shopware()->Session()->moptPayment === false) ;
            $creditCardAgreement = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment')->get('creditCardSavePseudocardnumAgreement');
            $subject->View()->assign('moptCreditCardAgreement', str_replace('##Shopname##', Shopware()->Shop()->getTitle(), $creditCardAgreement));
            $subject->View()->assign('showMoptCreditCardAgreement', ($showmoptCreditCardAgreement === true) ? '1' : '0');
            $subject->View()->assign('showPOCCDeleteButton', true);
            $subject->View()->assign('moptIsAccountController', '1');
        }
    }

    /**
     * Check
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onPaymentSaveAction(\Enlight_Hook_HookArgs $arguments) {
        $subject = $arguments->getSubject();
        $request = $subject->Request();
        $deleteCCDataChecked = (bool) $request->getParam('mopt_payone__cc_deletedata');
        if ($deleteCCDataChecked) {
            $this->triggerDeleteCCData();
        }
    }


    /**
     * Performs deletion of previously saved CC Data
     *
     * @param void
     * @return void
     */
    protected function triggerDeleteCCData() {
        $userId = Shopware()->Session()->sUserId;
        $paymentData = [];
        $serializedPaymentData = serialize($paymentData);
        $sql = 'UPDATE s_plugin_mopt_payone_creditcard_payment_data SET `moptCreditcardPaymentData`= ? WHERE userId = ?';
        Shopware()->Db()->executeUpdate($sql,array($serializedPaymentData, $userId));
        // also remove creditcard as default payment
        $sql = "UPDATE s_user SET paymentID = ? WHERE id = ?";
        Shopware()->Db()->query($sql, array((int)Shopware()->Config()->Defaultpayment, (int)$userId));
        $sql = "UPDATE s_user_attributes SET `mopt_payone_creditcard_initial_payment` = ? WHERE id = ?";
        Shopware()->Db()->query($sql, array(0, (int)$userId));
        // also remove creditcard data in Session
        unset(Shopware()->Session()->moptPayment);
    }

}
