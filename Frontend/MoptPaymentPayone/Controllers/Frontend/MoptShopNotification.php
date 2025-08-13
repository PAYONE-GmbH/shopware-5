<?php

use Shopware\Components\CSRFWhitelistAware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Shopware\Models\Order\Order;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneRequest;

/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptShopNotification extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{
    protected $moptPayone__main = null;

    /** @var $moptPayone__helper Mopt_PayoneHelper */
    protected $moptPayone__helper = null;
    protected $moptPayone__paymentHelper = null;
    /** @var Logger $logger */
    protected $logger = null;
    protected $rotatingLogger = null;
    protected $payoneConfig = null;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();

        $this->initForwardRotatingLogger();
        $this->logger = new Logger('moptPayone');
        $streamHandler = new StreamHandler(
            $this->buildPayoneTransactionLogPath(),
            Logger::DEBUG
        );
        $this->logger->pushHandler($streamHandler);
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    /**
     * Initializes rotating logger for tracing forward requests
     *
     * @param void
     * @return void
     */
    protected function initForwardRotatingLogger()
    {
        $logPath = Shopware()->Container()->get('kernel')->getLogDir();
        $logFile = $logPath . '/MoptPaymentPayone_txredirect_production.log';

        $rfh = new RotatingFileHandler($logFile, 14);
        $this->rotatingLogger = new Logger('MoptPaymentPayone');
        $this->rotatingLogger->pushHandler($rfh);
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
        // exit(); // uncomment for testing
        $request = $this->Request();

        $this->logger->debug('notification controller called');

        if (!$request->isPost()) {
            $this->redirect(array('controller' => 'index', 'action' => 'error'));
            return;
        }

        $this->logger->debug('received $_POST:' . PHP_EOL .  var_export($_POST, true) . PHP_EOL);

        $rawPost = $_POST;
        $orderIsCorrupted = false;

        $_POST = $this->utf8_encode_array($_POST);
        $this->logger->debug('successfully converted $_POST to utf-8:' . PHP_EOL .  var_export($_POST, true) . PHP_EOL);
        $request->setParamSources(array('_POST')); // only retrieve data from POST

        $this->CheckAndFixActiveShopIfNeeded($request->get('param'));

        $transactionId = $request->getParam('txid');
        $this->logger->debug('push received for tx ' . $transactionId);
        $isOrderFinished = $this->isOrderFinished($transactionId, $request->getParam('reference'));

        if ($isOrderFinished) {
            $order = $this->loadOrderByTransactionId($transactionId);
            $paymentId = $order['paymentID'];
            if ($order['cleared'] === 21) {
                $orderIsCorrupted = true;
            }
        } else {
            $this->restoreSession($request->get('param'));
            $session = Shopware()->Session();
            if (is_null($session) || !$session->offsetExists('sOrderVariables')) {
                $message = 'The session could not be restored. It might help to configure the server\'s gc_probability:'
                    . '\n\n   https://developers.shopware.com/sysadmins-guide/sessions/#blocking-transactions'
                    . '\n   https://www.php.net/manual/de/session.configuration.php#ini.session.gc-probability';
                $this->logger->error($message);
            } elseif (!method_exists($orderVariables = $session['sOrderVariables'], 'getArrayCopy')) {
                $message = 'Method \'getArrayCopy\' does not exist, this might depend on malformed Object'
                    . 'sOrderVariables because of existence of Plugin \'SwagAdvancedCart\'. See also'
                    . '\n   https://github.com/PAYONE-GmbH/shopware-5/issues/37#issuecomment-399108861';
                $this->logger->error($message);
            }
            $orderVariables = $session['sOrderVariables']->getArrayCopy();
            $paymentId = $orderVariables['sUserData']['additional']['user']['paymentID'];
        }

        $config = $this->moptPayone__main->getPayoneConfig($paymentId, true);
        $this->payoneConfig = $config;
        Shopware()->Config()->mopt_payone__paymentId = $paymentId; //store in config for log
        $key = $config['apiKey'];
        $hashedKey = md5($key);
        $moptConfig = new Mopt_PayoneConfig();
        $validIps = $moptConfig->getValidIPs();
        if (!$this->validateRequest($validIps)) {
            return;
        }
        if ($hashedKey !== $_POST['key']) {
            return;
        }

        try {
            $response = new PayoneRequest(null, $request->getPost());
            $response->setStatus( 'TSOK');
        } catch (Exception $exc) {
            $this->logger->error('error processing request', array($exc->getTraceAsString()));
            echo 'error processing request';
            return;
        }

        $payoneRequest = new PayoneRequest(null, $request->getPost());
        $clearingData = $this->moptPayone__paymentHelper->extractClearingDataFromResponse($payoneRequest);
        if ($clearingData && !$isOrderFinished) {
            $session->offsetSet('moptClearingData', $clearingData);
        }

        if (!$isOrderFinished) {
            if ($payoneRequest->getParam('txaction') !== 'failed') {
                $orderIsCorrupted = $this->validateBasketSignature($session, $payoneRequest);
                if ($orderIsCorrupted) {
                    $this->logger->error('order corrupted - order hash mismatch');
                    $orderIsCorrupted = true;
                    $paymentStatus = 21;
                    $orderNumber = $this->saveOrder($transactionId, $payoneRequest->getParam('reference'), $paymentStatus);
                    $orderObj = Shopware()->Models()->getRepository(Order::class)->findOneBy(['number' => $orderNumber ]);
                    $comment = Shopware()->Snippets()
                        ->getNamespace('frontend/MoptPaymentPayone/messages')
                        ->get('fraudCommentPart1', false)
                        . ' (' . $orderNumber . ') '
                        .  Shopware()->Snippets()
                            ->getNamespace('frontend/MoptPaymentPayone/messages')
                            ->get('fraudCommentPart2', false)
                        . ' ' . $transactionId . ' '
                        . Shopware()->Snippets()
                            ->getNamespace('frontend/MoptPaymentPayone/messages')
                            ->get('fraudCommentPart3', false);
                    $orderObj->setInternalComment($comment);
                    Shopware()->Models()->persist($orderObj);
                    Shopware()->Models()->flush();
                } else {
                    $orderNumber = $this->saveOrder($transactionId, $payoneRequest->getParam('reference'));
                }
                $order = $this->loadOrderByOrderNumber($orderNumber);

            } else {
                $this->logger->debug('finished, output TSOK');
                echo $response->getStatus();
                $this->logger->debug('starting tx forwards');
                $this->moptPayoneForwardTransactionStatus($_POST, $paymentId);
                $this->logger->debug('finished all tasks, exit');
                return;
            }
        }

        $attributeData = array();
        $saveOrderHash = false;
        $saveClearingData = false;
        $attributeData['mopt_payone_status'] = $payoneRequest->getParam('txaction');
        $attributeData['mopt_payone_sequencenumber'] = $payoneRequest->getParam('sequencenumber');
        $attributeData['mopt_payone_payment_reference'] = $payoneRequest->getParam('reference');
        $customParam = explode('|', $payoneRequest->getParam('param'));
        if (isset($customParam[2])) {
            $attributeData['mopt_payone_order_hash'] = $customParam[2];
            $saveOrderHash = true;
        }

        if ($clearingData && !$this->clearingDataExists($order)) {
            $attributeData['mopt_payone_clearing_data'] = json_encode($clearingData);
            $saveClearingData = true;
        }

        if (!$orderIsCorrupted) {
            $mappedShopwareState = $this->moptPayone__helper->getMappedShopwarePaymentStatusId(
                $config,
                $payoneRequest->getParam('txaction'),
                $payoneRequest->getParam('reminderlevel')
            );

            $transaction_status = $payoneRequest->getParam('transaction_status');
            $failedcause = $payoneRequest->getParam('reasoncode');
            //$transaction_status = 'pending';
            //$failedcause = '-981';

            $paymentName = $this->moptPayone__paymentHelper->getPaymentNameFromId($order['paymentID']);
            if (strpos($paymentName, 'mopt_payone__ewallet_amazon_pay') === 0) {
                if ($payoneRequest->getParam('txaction') == 'failed') {
                    // save failed status with mail notification
                    $this->savePaymentStatus($transactionId, $order['temporaryID'], $mappedShopwareState, true);
                } elseif ($payoneRequest->getParam('txaction') == 'appointed' && $transaction_status == 'pending' && $failedcause == '981') {
                    // InvalidPayment Method: update Order Status to "amazon_delayed" (119) and send mail notification
                    $this->savePaymentStatus($transactionId, $order['temporaryID'], 119, true);
                    $attributeData['mopt_payone_status'] = 'pending';
                } elseif ($payoneRequest->getParam('txaction') == 'appointed' && $transaction_status == 'pending' && $failedcause != '981') {
                    // InvalidPayment Method: update Order Status to "amazon_delayed" (119)
                    $this->savePaymentStatus($transactionId, $order['temporaryID'], 119);
                    $attributeData['mopt_payone_status'] = 'pending';
                } else {
                    $this->savePaymentStatus($transactionId, $order['temporaryID'], $mappedShopwareState);
                }

            } elseif ($payoneRequest->getParam('txaction') === 'reminder' && $payoneRequest->getParam('reminderlevel') === '0') {
                // ignore txaction reminder with reminderlevel 0 since this only marks the end of dunning process
            } else {
                // ! Amazonpay
                // do not update payment status for corrupted/problematic orders
                if (!$orderIsCorrupted) {
                    $this->savePaymentStatus($transactionId, $order['temporaryID'], $mappedShopwareState);
                }
            }
        }

        $this->logger->debug('save attribute data', $attributeData);
        $this->saveOrderAttributeData($order, $attributeData, $saveOrderHash, $saveClearingData);

        if ($config['changeOrderOnTXS'] && (version_compare(Shopware()->Config()->get('version'), '5.5.0', '>=') || Shopware()->Config()->get('version') == '___VERSION___')) {
            $orderObj = Shopware()->Models()->getRepository(Order::class)->findOneBy(['number' => $order['ordernumber']]);
            $orderObj->updateChangedTimestamp();
            Shopware()->Models()->persist($orderObj);
            Shopware()->Models()->flush();
        }
        $this->logger->debug('finished, output TSOK');
        echo $response->getStatus();
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog');
        $repository->save($payoneRequest, $payoneRequest);
        $this->logger->debug('starting tx forwards');
        $this->moptPayoneForwardTransactionStatus($_POST, $paymentId);

        // fire event to do some custom stuff, e.g. synchronising with merchandise management software
        // please note that processing this event has to be fast because Payone will mark this request as
        // failed if it is not processed within 10 seconds
        $this->container->get('events')->notify(
            'Payone_Controller_MoptShopNotification',
            [
                'txaction'        => $payoneRequest->getParam('txaction'),
                'reference'       => $payoneRequest->getParam('reference'),
                'ordernumber'     => $order['ordernumber'],
                'sequencenumber'  => $payoneRequest->getParam('sequencenumber'),
                'paymentId'       => $order['paymentID'],
            ]
        );

        $this->logger->debug('finished all tasks, exit');
    }

    /**
     * forward request to configured urls
     *
     * @param array $post
     * @param $paymentID
     */
    protected function moptPayoneForwardTransactionStatus($post, $paymentID)
    {
        $post['paymentID'] = $paymentID;

        $queueWorker = new Mopt_PayoneTransactionForwardingQueueWorker();

        $configKey = 'trans' . ucfirst($post['txaction']);
        $forwardingUrls = explode(';', $this->payoneConfig[$configKey]);

        foreach ($forwardingUrls as $url) {
            if (trim($url !== '')) {
                $queueWorker->queuePush(
                    '',
                    '',
                    $post['txid'],
                    $post,
                    trim($url),
                    $this->payoneConfig
                );
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

    /**
     * try to load order via transaction id
     *
     * @param string $transactionId
     * @return order
     */
    protected function loadOrderByTransactionId($transactionId)
    {
        $sql = '
            SELECT id, ordernumber, paymentID, temporaryID, transactionID, cleared  FROM s_order
            WHERE transactionID=?';

        $order = Shopware()->Db()->fetchRow($sql, array($transactionId));

        return $order;
    }

    /**
     * try to load order via transaction id
     *
     * @param string $orderNumber
     * @return order
     */
    protected function loadOrderByOrderNumber($orderNumber)
    {
        $sql = '
            SELECT id, ordernumber, paymentID, temporaryID, transactionID  FROM s_order
            WHERE ordernumber=?';

        $order = Shopware()->Db()->fetchRow($sql, array($orderNumber));

        return $order;
    }

    /**
     * restore session from Id
     *
     * @param string $customParam
     */
    protected function restoreSession($customParam)
    {
        $sessionParam = explode('|', $customParam);

        if (version_compare(Shopware()->Config()->get('version'), '5.7.0', '>=')) {
            Shopware()->Session()->save();
            Shopware()->Session()->setId($sessionParam[1]);
            Shopware()->Session()->start();
        } else {
            \Enlight_Components_Session::writeClose();  /** @phpstan-ignore-line */
            \Enlight_Components_Session::setId($sessionParam[1]); /** @phpstan-ignore-line */
            \Enlight_Components_Session::start(); /** @phpstan-ignore-line */
        }
    }

    /**
     * determine wether order is already finished
     *
     * @param string $transactionId
     * @param string $paymentReference
     * @return boolean
     */
    protected function isOrderFinished($transactionId, $paymentReference)
    {
        $sql = '
            SELECT ordernumber FROM s_order
            WHERE transactionID=? AND status!=-1';

        $orderNumber = Shopware()->Db()->fetchOne($sql, array($transactionId));

        if (empty($orderNumber) && !$this->isFinishedWithReference($paymentReference, $transactionId)) {
            return false;
        }

        return true;
    }

    /**
     * determine wether order is already finished
     * additional check for frontend api creditcard payments
     *
     * @param string $paymentReference
     * @param string $transactionId
     * @return boolean
     */
    protected function isFinishedWithReference($paymentReference, $transactionId)
    {
        $sql = '
            SELECT ordernumber FROM s_order
            WHERE transactionID=? AND status!=-1';

        $orderNumber = Shopware()->Db()->fetchOne($sql, array($paymentReference));

        if (empty($orderNumber)) {
            return false;
        }

        $this->setTransactionId($orderNumber, $transactionId);
        return true;
    }

    /**
     * update transaction id, needed for frontend api creditcard payments
     *
     * @param string $orderNumber
     * @param string $transactionId
     * @return void
     */
    protected function setTransactionId($orderNumber, $transactionId)
    {
        $sql = '
            UPDATE s_order SET transactionID=?
            WHERE ordernumber=?';

        Shopware()->Db()->query($sql, array($transactionId, $orderNumber));
    }

    /**
     * persist tx-status information
     *
     * @param array $order
     * @param array $attributeData
     * @param bool $saveOrderHash
     * @param bool $saveClearingData
     */
    protected function saveOrderAttributeData($order, $attributeData, $saveOrderHash, $saveClearingData)
    {
        $params = array(
            $attributeData['mopt_payone_status'],
            $attributeData['mopt_payone_sequencenumber'],
            $attributeData['mopt_payone_payment_reference'],
        );

        $sql = 'UPDATE s_order_attributes SET mopt_payone_status=?, mopt_payone_sequencenumber=?, '
            . 'mopt_payone_payment_reference=? ';

        if ($saveOrderHash) {
            $sql = $sql . ' , mopt_payone_order_hash=? ';
            $params[] = $attributeData['mopt_payone_order_hash'];
        }

        if ($saveClearingData) {
            $sql = $sql . ' , mopt_payone_clearing_data=? ';
            $params[] = $attributeData['mopt_payone_clearing_data'];
        }
        $sql = $sql . ' WHERE orderID=?';
        $params[] = $order['id'];

        Shopware()->Db()->query($sql, $params);
    }

    /**
     * check if correct active shop is loaded
     * load and register correct shop and resources
     *
     * @param array $customParam
     */
    protected function CheckAndFixActiveShopIfNeeded($customParam)
    {
        $sessionParam = explode('|', $customParam);

        $cookieName = explode('-', $sessionParam[0]);
        $shopId = (int)$cookieName[1];
        $activeShopId = Shopware()->Shop()->getId();

        if ($activeShopId !== $shopId) {
            $shopRepository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
            $shop = $shopRepository->getActiveById($shopId);
            $shop->registerResources(Shopware()->Bootstrap());

            $this->logger->info(
                'different shop active, submitted id, new shopid',
                array($activeShopId, $shopId, Shopware()->Shop()->getId())
            );
        }
    }

    /**
     * check if clearingData already exists in DB
     *
     * @param array $order
     * @return boolean
     */
    protected function clearingDataExists($order)
    {
        $sql = 'SELECT mopt_payone_clearing_data FROM s_order_attributes WHERE orderID=?';
        $params[] = $order['id'];

        $clearingData = Shopware()->Db()->query($sql, $params);

        if (empty($clearingData)) {
            $this->logger->debug('clearingdata is empty');
            return false;
        }

        $this->logger->debug('clearingdata already exists');
        return true;
    }

    /**
     * builds the transaction log path
     * @return string
     */
    protected function buildPayoneTransactionLogPath()
    {
        $logDir = Shopware()->Container()->get('kernel')->getLogDir();
        return $logDir . '/moptPayoneTransactionStatus.log';
    }

    /**
     * converts multi dimensional arrays to utf8
     * @param $array
     * @return mixed
     */
    private function utf8_encode_array($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->utf8_encode_array($value);
            } else {
                $array[$key] = utf8_encode($value);
            }
        }
        return $array;
    }

    /**
     * checks the basket signature send to payone against
     * the current basket signature for fraud detection
     *
     * @param $session
     * @param $request
     * @return bool
     */
    private function validateBasketSignature($session, $request) {
        $orderHash = md5(serialize($session['sOrderVariables']));
        $customParam = explode('|', $request->getParam('param'));
        return ($orderHash !== $customParam[2]);
    }

    /**
     * Returns the Remote IP supporting
     * load balancer and proxy setups
     *
     * @return string
     */
    public static function getRemoteAddress()
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $proxy = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (!empty($proxy)) {
                $proxyIps = explode(',', $proxy);
                $relevantIp = array_shift($proxyIps);
                $relevantIp = trim($relevantIp);
                if (!empty($relevantIp)) {
                    return $relevantIp;
                }
            }
        }
        // Cloudflare sends a special Proxy Header, see:
        // https://support.cloudflare.com/hc/en-us/articles/200170986-How-does-Cloudflare-handle-HTTP-Request-headers-
        // In theory, CF should respect X-Forwarded-For, but in some instances this failed
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        return $remoteAddr;
    }

    public function validateRequest($validIps)
    {
        $remoteAddress = $this->getRemoteAddress();

        if (in_array($remoteAddress, $validIps)) {
            // this is for exact matches
            return true;
        }

        foreach ($validIps as $ip) {
            $ip = $this->checkForDelimiter($ip);
            if (preg_match($ip, $remoteAddress)) {
                return true;
            }
        }
        return false;
    }

    protected function checkForDelimiter($ip)
    {
        if (substr($ip, 0, 1) !== '/') {
            $ip = '/' . $ip;
        }
        if (substr($ip, -1, 1) !== '/') {
            $ip = $ip . '/';
        }
        return $ip;
    }

}
