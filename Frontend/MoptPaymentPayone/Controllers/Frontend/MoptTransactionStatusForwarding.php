<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptTransactionStatusForwarding extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    protected $moptPayone__main = null;
    protected $payoneConfig = null;
    protected $rawPost = null;
    protected $payoneAction = null;
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
     * index action called by Payone platform to transmit transaction status updates
     *
     * @return mixed
     */
    public function indexAction()
    {
        $transactionForwardingQueue = new Mopt_PayoneTransactionForwardingQueueWorker();
        $transactionForwardingQueue->processQueue();
        exit();

        $request = $this->Request();
        $paymentId = $request->getParam('paymentID');
        $this->payoneConfig = $this->moptPayone__main->getPayoneConfig($paymentId, true);

        if (!$request->isPost()) {
            $log_msg = [
                'ERROR',
                'Request is not of type post',
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

        $rawPost = $_POST;
        $this->moptPayoneForwardTransactionStatus($rawPost, $request->getParam('txaction'));
        exit;
    }

    /**
     * forward request to configured urls
     *
     * @param array $rawPost
     * @param string $payoneAction
     * @return void
     */
    protected function moptPayoneForwardTransactionStatus($rawPost, $payoneAction)
    {
        $this->rawPost = $rawPost;
        $this->payoneAction = $payoneAction;

        $configKey = 'trans' . ucfirst($payoneAction);
        $valid = isset($this->payoneConfig[$configKey]);

        if (!$valid) {
            $logentry = [
                'ERROR',
                'configKey: '. $configKey .
                ' does not exist in payoneConfig array!',
                'payoneConfig: ' . print_r($this->payoneConfig, true),
            ];
            $this->payoneHelper->forwardLog($logentry, $this->payoneConfig);
            return;
        }

        $forwardingUrls = explode(';', $this->payoneConfig[$configKey]);
        $this->handleForwardingUrls($forwardingUrls);
    }

    /**
     * Takes care on the list of urls that shall be forwarded
     *
     * @param $forwardingUrls
     * @return void
     */
    protected function handleForwardingUrls($forwardingUrls)
    {
        $zendHttpClient = $this->payoneHelper->initRequestClient();

        foreach ($forwardingUrls as $url) {
            if (empty($url)) {
                $logentry = [
                    "ERROR",
                    "URL is empty",
                    "txid=".$this->rawPost['txid'],
                    "url=". $url,
                ];
                $this->payoneHelper->forwardLog($logentry, $this->payoneConfig);
                continue;
            }

            $this->payoneHelper->forwardTransactionStatus(
                $zendHttpClient,
                $url,
                $this->payoneAction,
                $this->rawPost['txid'],
                $this->payoneConfig
            );

            // TODO: move to catch block
            $transactionForwardingQueue = new Mopt_PayoneTransactionForwardingQueueWorker();

            $transactionForwardingQueue->queuePush(
                (string)$zendHttpClient->getLastRequest(),
                (string)$zendHttpClient->getLastResponse(),
                $this->rawPost['txid'],
                json_encode($this->rawPost),
                $url
            );
        }

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
