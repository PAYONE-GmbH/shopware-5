<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * Class Shopware_Controllers_Frontend_MoptTransactionStatusForwarding
 *
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptTransactionStatusForwarding extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    /**
     * @var Mopt_PayoneMain
     */
    protected $moptPayone__main = null;

    /**
     * @var mixed
     */
    protected $payoneConfig = null;

    /**
     * @var Mopt_PayoneHelper
     */
    protected $payoneHelper = null;

    /**
     * @return void
     */
    public function init()
    {
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();

        $this->payoneHelper = new Mopt_PayoneHelper();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    /**
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
        ];
    }

    /**
     * @return void
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
            return;
        } else {
            $log_msg = [
                'Request: TransactionStatusForwarding'
            ];
            $this->payoneHelper->forwardLog($log_msg, $this->payoneConfig);
        }

        $transactionForwardingQueue = new Mopt_PayoneTransactionForwardingQueueWorker();
        $transactionForwardingQueue->processQueue();
    }

    /**
     * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }
}
