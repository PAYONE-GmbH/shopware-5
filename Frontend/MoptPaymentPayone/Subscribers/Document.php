<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class Document implements SubscriberInterface
{

    /**
    * path to plugin files
    *
    * @var string
    */
    private $path;
    
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
    public function __construct(\Shopware\Components\DependencyInjection\Container $container, $path)
    {
        $this->container = $container;
        $this->path = $path;
    }

    /**
     * return array with all subsribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            // add PAYONE data to pdf
            'Shopware_Components_Document::assignValues::after' => 'onBeforeRenderDocument'
        );
    }

    /**
     * add payone clearing data to document
     *
     * @param \Enlight_Hook_HookArgs $args $args
     */
    public function onBeforeRenderDocument(\Enlight_Hook_HookArgs $args)
    {
        $document = $args->getSubject();

        if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayoneBillsafe($document->_order->payment['name'])) {
            return;
        }

        // get PAYONE data from log
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $payoneData = $moptPayoneMain->getPaymentHelper()->getClearingDataFromOrderId($document->_order->order->id);

        if (empty($payoneData)) {
            return;
        }

        $payoneData['amount'] = $document->_order->order->invoice_amount;

        $view = $document->_view;
        //@TODO check if additional treatment for responsive theme is needed here
        $document->_template->addTemplateDir($this->path . '/Views/');
        $document->_template->assign('instruction', (array) $payoneData);
        $containerData = $view->getTemplateVars('Containers');
        $containerData['Footer'] = $containerData['PAYONE_Footer'];
        $containerData['Content_Info'] = $containerData['PAYONE_Content_Info'];
        $containerData['Content_Info']['value'] = $document->_template->fetch('string:'
                . $containerData['Content_Info']['value']);
        $containerData['Content_Info']['style'] = '}' . $containerData['Content_Info']['style'] . ' #info {';
        $view->assign('Containers', $containerData);
    }
}
