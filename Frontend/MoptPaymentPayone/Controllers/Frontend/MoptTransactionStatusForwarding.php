<?php

use Shopware\Components\CSRFWhitelistAware;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptTransactionStatusForwarding extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    protected $moptPayone__main = null;
    protected $rotatingLogger = null;
    protected $payoneConfig = null;
    /** @var $zendHttpClient Zend_Http_Client */
    protected $zendHttpClient = null;
    protected $rawPost = null;
    protected $payoneAction = null;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();
        $logPath = Shopware()->Application()->Kernel()->getLogDir();
        $logFile = $logPath . '/MoptPaymentPayone_txredirect_production.log';

        $rfh = new RotatingFileHandler($logFile, 14);
        $this->rotatingLogger = new Logger('MoptPaymentPayone');
        $this->rotatingLogger->pushHandler($rfh);

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
        $request = $this->Request();
        $paymentId = $request->getParam('paymentID');
        $this->payoneConfig = $this->moptPayone__main->getPayoneConfig($paymentId, true);

        if (!$request->isPost()) {
            $log_msg = [
                'ERROR',
                'Request is not of type post',
                'Request was: ' . print_r($request, true),
            ];
            $this->forwardLog($log_msg);
            exit;
        } else {
            $log_msg = [
                'Request: TransactionStatusForwarding'
            ];
            $this->forwardLog($log_msg);
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
            $this->forwardLog($logentry);
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
        $this->initRequestClient();

        foreach ($forwardingUrls as $url) {
            if (empty($url)) {
                $logentry = [
                    "ERROR",
                    "URL is empty",
                    "txid=".$this->rawPost['txid'],
                    "url=". $url,
                ];
                $this->forwardLog($logentry);
                continue;
            }

            $this->zendHttpClient->setUri($url);
            $logentry = [
                "payone-status=".$this->payoneAction,
                "txid=".$this->rawPost['txid'],
                "url=". $url,
            ];

            try {
                $requestStart = microtime(true);
                $this->zendHttpClient->request(Zend_Http_Client::POST);
                $requestStop = microtime(true);
            } catch (Zend_Http_Client_Exception $e) {
                $logentry []= "response-status=" . $e->getCode();
                $logentry []= "response-message=". $e->getMessage();
                $this->forwardLog($logentry);
                continue;
            }

            $requestDuration = ($requestStop - $requestStart);
            $logentry [] = "duration=".$requestDuration;

            $response = $this->zendHttpClient->getLastResponse();

            $logentry []= "response-status=".$response->getStatus();
            $logentry []= "response-message=".$response->getMessage();

            // TODO: move to catch block
            $transactionForwardingQueue = new Mopt_PayoneTransactionForwardingQueueWorker();

            $transactionForwardingQueue->queuePush(
                (string)$this->zendHttpClient->getLastRequest(),
                (string)$this->zendHttpClient->getLastResponse(),
                $this->rawPost['txid'],
                json_encode($this->rawPost),
                $url
            );

            $this->forwardLog($logentry);
        }

    }

    /**
     * Initializes request client
     *
     * @param void
     * @return void
     */
    protected function initRequestClient()
    {
        $zendClientConfig = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ),
            'timeout' => 50,
        );
        $this->zendHttpClient = new Zend_Http_Client();
        $this->zendHttpClient->setConfig($zendClientConfig);
        $this->zendHttpClient->setParameterPost($this->rawPost);
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

    /**
     * Logs an entry of transaction forward controller
     *
     * @param array $logentry
     * @return void
     */
    protected function forwardLog($logentry)
    {
        $logAllowed = (
            $this->payoneConfig['transLogging'] === true
        );

        if ($logAllowed) {
            $log=implode(";",$logentry);
            $this->rotatingLogger->debug($log);
        }
    }
}
