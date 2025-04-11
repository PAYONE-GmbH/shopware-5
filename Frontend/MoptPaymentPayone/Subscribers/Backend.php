<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class Backend implements SubscriberInterface
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
            // extend backend documents extjs
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig',
        );
    }

    /**
     * @return void
     */
    public function onPostDispatchConfig(\Enlight_Controller_ActionEventArgs $arguments)
    {
        $view = $arguments->getSubject()->View();
        $request = $arguments->getSubject()->Request();

        $test = $request->getActionName();

        if ($request->getActionName() === 'load') {
            $view->extendsTemplate('backend/config/view/form/document_mopt_payone.js');
        }
    }
}
