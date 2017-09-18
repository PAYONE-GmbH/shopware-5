<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class BackendRiskManagement implements SubscriberInterface
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
            //risk management:Backend options
            'Enlight_Controller_Action_PostDispatch_Backend_RiskManagement' => 'onBackendRiskManagementPostDispatch'
        );
    }

    public function onBackendRiskManagementPostDispatch(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->extendsTemplate('backend/mopt_risk_management/controller/main.js');
        $view->extendsTemplate('backend/mopt_risk_management/controller/risk_management.js');
        $view->extendsTemplate('backend/mopt_risk_management/store/risks.js');
        $view->extendsTemplate('backend/mopt_risk_management/store/trafficLights.js');
        $view->extendsTemplate('backend/mopt_risk_management/view/risk_management/container.js');
    }
}
