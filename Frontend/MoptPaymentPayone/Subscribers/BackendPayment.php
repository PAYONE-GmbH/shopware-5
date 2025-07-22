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
            'Enlight_Controller_Action_PostDispatch_Backend_Payment' => 'moptExtendController_Backend_Payment',
            'Enlight_Controller_Action_PostDispatch_Backend_Index' => 'onPostDispatchBackendIndex',
        );
    }

    public function moptExtendController_Backend_Payment(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->extendsTemplate('backend/mopt_payone_payment/controller/payment.js');
        $view->extendsTemplate('backend/mopt_payone_payment/view/main/window.js');
    }

    /**
     * Extends Backend header with CSS to display PAYONE-Icon in Menu
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchBackendIndex(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Index $subject */
        $subject = $args->get('subject');
        $request = $subject->Request();
        $response = $subject->Response();
        $view = $subject->View();

        $view->addTemplateDir(__DIR__ . '/../Views');

        if ( ! $request->isDispatched()
            || $response->isException()
            || $request->getModuleName() !== 'backend'
            || ! $view->hasTemplate()
        ) {
            return;
        }

        $view->extendsTemplate('backend/payone.tpl');
    }
}
