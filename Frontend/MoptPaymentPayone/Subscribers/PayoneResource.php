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
   * @return \Mopt_PayoneMain
   */
    public function onInitResourcePayoneMain(\Enlight_Event_EventArgs $args)
    {
        $moptPayoneMain = \Mopt_PayoneMain::getInstance();
        return $moptPayoneMain;
    }
}
