<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptTransactionStatusForwarding extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    protected $moptPayone__main = null;
    protected $payoneConfig = null;
    /** @var $payoneHelper Mopt_PayoneHelper */
    protected $payoneHelper = null;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();

        $this->payoneHelper = new Mopt_PayoneHelper();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    /**
     * whitelists indexAction for SW 5.2 compatibility
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
        ];
    }

    /**
     * index action called by Payone platform to triger the transaction status queue
     *
     * @return mixed
     */
    public function indexAction()
    {
        $request = $this->Request();
        $paymentId = $request->getParam('paymentID');
        $this->payoneConfig = $this->moptPayone__main->getPayoneConfig($paymentId, true);

        if (!$paymentId) {
            $log_msg = [
                'ERROR',
                'Request has no parameter paymentID',
                'Request was: ' . print_r($request, true),
            ];
            $this->payoneHelper->forwardLog($log_msg, $this->payoneConfig);
            exit;
        } else {
            $log_msg = [
                'Request: TransactionStatusForwarding'
            ];
            $this->payoneHelper->forwardLog($log_msg, $this->payoneConfig);
        }

        $transactionForwardingQueue = new Mopt_PayoneTransactionForwardingQueueWorker();
        $transactionForwardingQueue->processQueue();

        exit;
    }

    /**
     * get plugin bootstrap
     *
     * @param void
     * @return plugin
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }
}
