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
            'Shopware_Controllers_Frontend_Account::paymentAction::after' => 'onPaymentAction'
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
        $userId = Shopware()->Session()->sUserId;

        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

        if (!empty($paymentData)) {
            //get array of creditcard payment ids
            $sql = "SELECT `id` FROM s_core_paymentmeans WHERE name LIKE '%mopt_payone__cc_%' ";
            $creditcardIds = Shopware()->Db()->fetchAll($sql);

            foreach ($creditcardIds as $creditcardId) {
                // check if active id is in array
                if ($creditcardId['id'] == $subject->View()->sFormData['payment']) {
                    // set creditcard active
                    $paymentData['payment'] = 'mopt_payone_creditcard';
                    $subject->View()->sFormData = $paymentData;
                    break;
                } else {
                    $subject->View()->sFormData += $paymentData;
                }
            }
            $subject->View()->sUserData += $paymentData;
        }
    }
}
