<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Mopt_PayoneMain;

class OrderNumber implements SubscriberInterface
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
        return [
            'sOrder::sGetOrderNumber::replace' => 'onGetOrderNumber',
        ];
    }

    public function onGetOrderNumber(\Enlight_Hook_HookArgs $args)
    {
        // $paymentId = Shopware()->Container()->get('plugins')->Frontend()->get('MoptPaymentPayone')->getPaymentId();
        $userId = Shopware()->Session()->sUserId;
        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentId = unserialize(Shopware()->Db()->fetchOne($sql, $userId))['mopt_payone__cc_paymentid'];
        $config = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);

        if ($this->container->has('shop')) {
            $session = Shopware()->Session();
            $moptPaymentReference = $session->offsetGet('moptPaymentReference');
        }
        if ($moptPaymentReference && $config['sendOrdernumberAsReference']) {
            $args->setReturn($moptPaymentReference);
        } else {
            // standard behaviour
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));
        }
    }
}
