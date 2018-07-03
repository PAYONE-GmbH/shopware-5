<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

/**
 * add clearing data to email
 */
class EMail implements SubscriberInterface
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
            'Shopware_Modules_Order_SendMail_FilterVariables' => 'onSendMailFilterVariablesFilter'
        );
    }

    /**
     * add clearing data to email variables
     *
     * @param \Enlight_Event_EventArgs $args
     * @return void
     */
    public function onSendMailFilterVariablesFilter(\Enlight_Event_EventArgs $args)
    {
        $variables = $args->getReturn();
        $session = Shopware()->Session();
        $moptRatepayOrdernum = $session->offsetGet('moptRatepayOrdernum');
        $bsPayoneMasterpassOrdernum = $session->offsetGet('BSPayoneMasterpassOrdernum');

        if ($session->moptClearingData) {
            $variables['additional']['moptPayoneClearingData'] = $session->moptClearingData;
        }

        if ($moptRatepayOrdernum) {
            $variables['ordernumber'] = $moptRatepayOrdernum;
        }

        if ($bsPayoneMasterpassOrdernum) {
            $variables['ordernumber'] = $bsPayoneMasterpassOrdernum;
        }

        $args->setReturn($variables);
    }
}
