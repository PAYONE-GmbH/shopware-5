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
        
        if (!$request->isPost()) {
            exit;
        }

        $rawPost = $_POST;
        $paymentId = $request->getParam('paymentID');
        $config = $this->moptPayone__main->getPayoneConfig($paymentId, true);
        $this->moptPayoneForwardTransactionStatus($config, $rawPost, $request->getParam('txaction'));
        exit;
    }

    /**
     * forward request to configured urls
     *
     * @param array $payoneConfig
     * @param array $rawPost
     * @param string $payoneStatus
     */
    protected function moptPayoneForwardTransactionStatus($payoneConfig, $rawPost, $payoneStatus)
    {
        $configKey = 'trans' . ucfirst($payoneStatus);
        if (isset($payoneConfig[$configKey])) {
            $forwardingUrls = explode(';', $payoneConfig[$configKey]);

            $zendClientConfig = array(
                'adapter' => 'Zend_Http_Client_Adapter_Curl',
                'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
            );

            foreach ($forwardingUrls as $url) {
                if (empty($url)) {
                    continue;
                }
                $logentry = array( "payone-status=".$payoneStatus , "txid=".$rawPost['txid'],"url=". $url );
                $client = new Zend_Http_Client($url, $zendClientConfig);
                $client->setConfig(array('timeout' => 50));
                $client->setParameterPost($rawPost);
                $requestStart = microtime(true);
                try {
                    $client->request(Zend_Http_Client::POST);
                } catch (Zend_Http_Client_Exception $e) {
                    // do nothing
                }
                $requestStop = microtime(true);
                $requestDuration = ($requestStop - $requestStart);
                $logentry []= "duration=".$requestDuration;
                $response = $client->getLastResponse();
                if ($response !== null) {
                    $logentry []= "response-status=".$response->getStatus();
                    $logentry []= "response-message=".$response->getMessage();
                } else {
                    $logentry []= "response-status=" . $e->getCode();
                    $logentry []= "response-message=". $e->getMessage();
                }
                if ($payoneConfig['transLogging'] === true) {
                    $log=implode(";",$logentry);
                    $this->rotatingLogger->debug($log);
                }
            }
        }
    }

    /**
     * get plugin bootstrap
     *
     * @return plugin
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }
}
