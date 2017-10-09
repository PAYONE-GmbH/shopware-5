<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class PayoneResource implements SubscriberInterface
{

    /**
     * return array with all subsribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Bootstrap_InitResource_MoptPayoneMain' => 'onInitResourcePayoneMain',
            'Enlight_Bootstrap_InitResource_MoptPayoneBuilder' => 'onInitResourcePayoneBuilder',
            'Enlight_Bootstrap_InitResource_payone_service' => 'onInitResourcePayoneService',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @return \Mopt_PayoneService
     */
    public function onInitResourcePayoneService(\Enlight_Event_EventArgs $args)
    {
        return new \Mopt_PayoneService();
    }
    
  /**
   * Creates and returns the payone builder for an event.
   *
   * @param \Enlight_Event_EventArgs $args
   * @return \Payone_Builder
   */
    public function onInitResourcePayoneBuilder(\Enlight_Event_EventArgs $args)
    {
        $payoneConfig = new \Payone_Config();
        $logger = ['Payone_Protocol_Logger_Log4php' => null];
    
        $payoneConfig->setValue('api/default/protocol/loggers', $logger);
        $payoneConfig->setValue('transaction_status/default/protocol/loggers', $logger);
        $payoneConfig->setValue('session_status/default/protocol/loggers', $logger);
       
        //$payoneConfig->setValue('api/default/protocol/filter/mask_value/percent', 50);
        
        $builder = new \Payone_Builder($payoneConfig);
        return $builder;
    }

  /**
   * Creates and returns the payone builder for an event.
   *
   * @param \Enlight_Event_EventArgs $args
   * @return \Mopt_PayoneMain
   */
    public function onInitResourcePayoneMain(\Enlight_Event_EventArgs $args)
    {
        $moptPayoneMain = \Mopt_PayoneMain::getInstance();
        return $moptPayoneMain;
    }
}
