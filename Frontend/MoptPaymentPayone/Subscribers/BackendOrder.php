<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class BackendOrder implements SubscriberInterface
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
                // extend backend order-overview
                'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'moptExtendController_Backend_Order',
                //add payone fields to list results
                'Shopware_Controllers_Backend_Order::getList::after' => 'Order__getList__after'
        );
    }
    
    public function moptExtendController_Backend_Order(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->extendsTemplate('backend/mopt_payone_order/controller/detail.js');
        $view->extendsTemplate('backend/mopt_payone_order/model/position.js');
        $view->extendsTemplate('backend/mopt_payone_order/view/detail/overview.js');
        $view->extendsTemplate('backend/mopt_payone_order/view/detail/position.js');
    }
    
    /**
    * add attribute data to detail-data
    * @parent fnc head: protected function getList($filter, $sort, $offset, $limit)
    *
    * @param \Enlight_Hook_HookArgs  $args
    */
    public function Order__getList__after(\Enlight_Hook_HookArgs  $args)
    {
        $return = $args->getReturn();
        $helper = $this->container->get('MoptPayoneMain')->getHelper();
      
        if (empty($return['success']) || empty($return['data'])) {
            return;
        }

        foreach ($return['data'] as &$order) {
            foreach ($order["details"] as &$orderDetail) {
                $blTaxFree = $order['taxFree'];
                $blNet = $order['net'];
                  // check here if netto is set and it corresponds with taxfree flag
                  // if order is netto and taxfree is not set add taxes to all positions
                $blDebitBrutto = (!$blTaxFree && $blNet);
                  //get detail attribute
                $detailObj                         = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail')
                  ->find($orderDetail['id']);
                $attribute                         = $helper->getOrCreateAttribute($detailObj);
                if (!$blDebitBrutto) {
                    $orderDetail['moptPayoneCaptured'] = $attribute->getMoptPayoneCaptured();
                    $orderDetail['moptPayoneDebit']    = $attribute->getMoptPayoneDebit();
            
                } else {
                    $orderDetail['moptPayoneCaptured'] = round($attribute->getMoptPayoneCaptured() * ( 1 + ($orderDetail['taxRate'] /100)), 2);
                    $orderDetail['moptPayoneDebit'] = round($attribute->getMoptPayoneDebit() * ( 1 + ($orderDetail['taxRate'] /100)), 2);
                }
            }
        }

        $args->setReturn($return);
    }
}
