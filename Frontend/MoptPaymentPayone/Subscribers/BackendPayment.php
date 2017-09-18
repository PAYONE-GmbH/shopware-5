<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class BackendPayment implements SubscriberInterface
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
            // extend backend payment configuration
            'Enlight_Controller_Action_PostDispatch_Backend_Payment' => 'moptExtendController_Backend_Payment'
        );
    }

    public function moptExtendController_Backend_Payment(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->extendsTemplate('backend/mopt_payone_payment/controller/payment.js');
        $view->extendsTemplate('backend/mopt_payone_payment/view/main/window.js');
    }
}
