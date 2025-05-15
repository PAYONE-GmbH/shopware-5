<?php

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Models\Order\Order;


require_once 'MoptConfigPayone.php';

/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Shopware_Controllers_Backend_FcPayone extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * PayoneMain
     * @var Mopt_PayoneMain
     */
    protected $moptPayoneMain = null;

    /**
     * PayoneMain
     * @var Mopt_PayonePaymentHelper
     */
    protected $moptPayonePaymentHelper = null;

    /**
     * PayOne Builder
     * @var PayoneBuilder
     */
    protected $payoneServiceBuilder = null;

    protected $service = null;

    protected $mid = '';
    protected $aid = '';
    protected $pid = '';
    protected $apikey = '';
    protected $testfailed = false;
    protected $aLogcontext = array(" ", " ", " ");
    protected $aMinimumParams = null;
    protected $logging;

    public function init()
    {
        $this->logging = new Logging();
        $this->logging->lfile(Shopware()->Container()->get('kernel')->getLogDir() . '/moptPayoneConnectionTest.log');
    }

    public function getWhitelistedCSRFActions()
    {
        $returnArray = array(
            'index',
            'connectionconfig',
            'connectiontest',
            'ajaxgetTestResults',
            'generalconfig',
            'generalconfigData',
            'transactionstatusconfig',
            'paymentstatusconfig',
            'paymentstatusconfigData',
            'amazonpay',
            'applepay',
            'ratepay',
            'riskcheck',
            'addresscheck',
            'creditcard',
            'creditcard_iframe',
            'ajaxapilog',
            'apilog',
            'debit',
            'ajaxgetPaymentStatusConfig',
            'ajaxgetRiskCheckConfig',
            'transactionlog',
            'ajaxcreditcard',
            'ajaxgetCreditCardConfig',
            'ajaxgetIframeConfig',
            'ajaxgetPaypalConfig',
            'ajaxfinance',
            'ajaxgetFinanceConfig',
            'ajaxonlinetransfer',
            'ajaxgetOnlineTransferConfig',
            'ajaxwallet',
            'ajaxgetWalletConfig',
            'ajaxtransactionstatusconfig',
            'ajaxgetTransactionStatusConfig',
            'ajaxriskcheck',
            'ajaxtextblocks',
            'ajaxgettextblocks',
            'ajaxsavetextblocks',
            'ajaxSavePaymentConfig',
            'ajaxSavePayoneConfig',
            'ajaxSaveIframeConfig',
            'ajaxSavePaypalConfig',
            'ajaxgetRatepayConfig',
            'ajaxgetAmazonConfig',
            'ajaxsaveApplepayCert',
            'ajaxsaveApplepayKey',
            'paypalexpress',
            'paypalexpressv2',
        );
        return $returnArray;
    }

    /**
     * Returns the payment plugin config data.
     *
     * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    public function indexAction()
    {
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");

        $filename = __DIR__ . '/../../dashboardconfig.txt';

        $config = file_get_contents($filename);
        $aConfig = json_decode($config);
        $datas = array();
        $title = $aConfig->Titel;
        $sql = $aConfig->SQL;
        $i = 0;
        foreach ($sql as $statement) {
            $datas[$i] = Shopware()->Db()->fetchall($statement);
            $i++;
        }
        $i = 0;
        foreach ($datas as $data) {
            $ret[$i]['data'] = $data[0]['platzhalter'];
            $ret[$i]['title'] = $title[$i];
            $i++;
        }

        $this->View()->assign(array(
            "data" => $ret,
            "title" => $title,
            "params" => $params,
        ));
    }

    /**
     * @return void
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function connectionconfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    /**
     * @return void
     * @throws Enlight_Exception
     */
    public function connectiontestDataAction()
    {
        sleep(2);
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $filename = Shopware()->Container()->get('kernel')->getLogDir() . '/moptPayoneConnectionTest.log';
        $resultfile = fopen($filename, "r");
        $aLines = '';
        while (!feof($resultfile)) {
            $aLines .= fgets($resultfile) . "<br>";
        }
        fclose($resultfile);
        echo $aLines;
    }

    /**
     * @return void
     * @throws Enlight_Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function connectiontestAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $this->mid = $_GET['mid'];
        $this->aid = $_GET['aid'];
        $this->pid = $_GET['pid'];
        $this->apikey = $_GET['apikey'];

        unlink(Shopware()->Container()->get('kernel')->getLogDir() . '/moptPayoneConnectionTest.log');
        $this->logging->lwrite('<span style="color: green;">Starte Verbindungstest</span>', $this->aLogcontext);
        if (!$this->testfailed) {
            $this->creditcardCheck('V', '4111111111111111', 'cc');
        }
        if (!$this->testfailed) {
            $this->creditcardCheck('M', '5500000000000004', 'cc');
        }
        if (!$this->testfailed) {
            $this->vorkasseCheck();
        }
        if (!$this->testfailed) {
            $this->rechnungCheck();
        }
        if (!$this->testfailed) {
            $this->lastschriftCheck();
        }
    }

    /**
     * @return string
     */
    private function getFutureExpiredate()
    {
        $future = new DateTime();
        $future->modify("+2 years");
        return $future->format("ym");
    }

    /**
     * @param $payment
     * @return Payone_Api_Request_Preauthorization
     */
    private function setTestRequestParams($payment)
    {
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($payment);
        $request = new Payone_Api_Request_Preauthorization($params);
        $request->setAid($this->aid);
        $request->setMid($this->mid);
        $request->setPortalid($this->pid);
        $request->setKey($this->apikey);
        $request->setAmount($this->aMinimumParams['amount']);
        $request->setCurrency($this->aMinimumParams['currency']);
        $request->setReference(rand(10000000, 99999999));
        $request->setClearingtype($this->aMinimumParams['clearingtype']);
        $pData = new Payone_Api_Request_Parameter_Authorization_PersonalData();
        $pData->setFirstname($this->aMinimumParams['firstname']);
        $pData->setLastname($this->aMinimumParams['lastname']);
        $pData->setCountry($this->aMinimumParams['country']);
        $request->setPersonalData($pData);
        $request->setMode('test');
        if ($payment === 'mopt_payone_creditcard') {
            $paymentData = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard;
            $paymentData->setCardexpiredate($this->aMinimumParams['cardexpiredate']);
            $paymentData->setCardpan($this->aMinimumParams['cardpan']);
            $paymentData->setCardtype($this->aMinimumParams['cardtype']);
            $paymentData->setEcommercemode('internet');
            $request->setPayment($paymentData);
        }
        if ($payment === 'mopt_payone__acc_debitnote') {
            $paymentData->setBankaccount($this->aMinimumParams['bankaccount']);
            $paymentData->setBankcountry($this->aMinimumParams['country']);
            $request->setPayment($paymentData);
        }
        $request->setSuccessurl('https://payone.com');
        $request->setMode('test');
        $generateHashService = $this->container->get('MoptPayoneBuilder')->buildServiceClientApiGenerateHash();
        $request->set('hash', $generateHashService->generate($request, $this->apikey));
        return $request;
    }

    /**
     * @param $request
     * @param $paymentFull
     * @return void
     */
    private function doTestrequest($request, $paymentFull)
    {
        $testRequestString = Shopware()->Snippets()->getNamespace('backend/mopt_config_payone/main')
            ->get('testRequest', 'teste Request Preauthorisierung im Modus Test mit Zahlart', false);
        $testSuccessString = Shopware()->Snippets()->getNamespace('backend/mopt_config_payone/main')
            ->get('testSuccess', 'Test erfolgreich', false);
        $testFailedString = Shopware()->Snippets()->getNamespace('backend/mopt_config_payone/main')
            ->get('testFailed', 'Test fehlgeschlagen', false);
        $testErrorString = Shopware()->Snippets()->getNamespace('backend/mopt_config_payone/main')
            ->get('testError', 'Fehlermeldung', false);
        $this->logging->lwrite('<span style="color: yellow;">' . $testRequestString . ' ' . $paymentFull . '</span>');
        $response = $this->service->preauthorize($request);
        if ($response->getStatus() == "APPROVED") {
            $this->logging->lwrite('<span style="color: green;">' . $testSuccessString . '</span>');
        } else {
            $this->logging->lwrite('<span style="color: red;">' . $testFailedString . '</span>');
            $this->logging->lwrite('<span style="color: red;">' . $testErrorString . ':' . $response->getErrorMessage() . '</span>');

            $this->testfailed = true;
        }
    }

    /**
     * @param $cardType
     * @param $cardPan
     * @param $clearingType
     * @return void
     */
    public function creditcardCheck($cardType, $cardPan, $clearingType)
    {
        $this->aMinimumParams = array('clearingtype' => $clearingType,
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
            'cardpan' => $cardPan, 'cardtype' => $cardType,
            'pseudocardpan' => '5500000000099999', 'cardexpiredate' => $this->getFutureExpiredate()
        );

        $cardTypeFull = $cardType === 'V' ? 'Visa' : 'Mastercard';
        $paymentFull = 'mopt_payone_creditcard' . '(' . $cardTypeFull . ')';
        $request = $this->setTestRequestParams('mopt_payone_creditcard');
        $this->doTestrequest($request, $paymentFull);
    }

    /**
     * @return void
     */
    public function vorkasseCheck()
    {
        $this->aMinimumParams = array('clearingtype' => 'vor',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE'
        );
        $paymentFull = 'mopt_payone__acc_payinadvance';
        $request = $this->setTestRequestParams('mopt_payone__acc_payinadvance');
        $this->doTestrequest($request, $paymentFull);
    }

    /**
     * @return void
     */
    public function rechnungCheck()
    {
        $this->aMinimumParams = array('clearingtype' => 'rec',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
        );

        $paymentFull = 'mopt_payone__acc_invoice';
        $request = $this->setTestRequestParams('mopt_payone__acc_invoice');
        $this->doTestrequest($request, $paymentFull);
    }

    /**
     * @return void
     */
    public function lastschriftCheck()
    {

        $this->aMinimumParams = array('clearingtype' => 'elv',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
            'bankaccount' => '2599100003',
        );
        $paymentFull = 'mopt_payone__acc_debitnote';
        $request = $this->setTestRequestParams('mopt_payone__acc_debitnote');
        $this->doTestrequest($request, $paymentFull);
    }

    public function apilogAction()
    {
        // API Log Entries
        //creates an empty query builder object
        $builder = Shopware()->Models()->createQueryBuilder();

        //add the select and from path for the query
        $builder->select(array('log'))
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

        $apilogentries = $builder->getQuery()->getArrayResult();
        $apilogentries = $this->addArrayRequestResponse($apilogentries);

        $this->View()->assign(array(
            "data" => $apilogentries,
            "locale" => Shopware()->Container()->get('locale')
        ));
    }

    public function ajaxapilogAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $search = $this->Request()->get('search');

        $offset = $this->Request()->get('offset');
        $limit = $this->Request()->get('limit');

        // API Log Entries
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(array('log'))
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

        if (!empty($search)) {
            $builder->where($builder->expr()->orx(
                $builder->expr()->like('log.requestDetails', '?1'),
                $builder->expr()->like('log.responseDetails', '?1'),
                $builder->expr()->like('log.merchantId', '?1'),
                $builder->expr()->like('log.portalId', '?1'),
                $builder->expr()->like('log.request', '?1'),
                $builder->expr()->like('log.response', '?1'),
                $builder->expr()->like('log.responseDetails', '?1'),
                $builder->expr()->like('log.creationDate', '?1')
            ));
            $builder->setParameter('1', '%' . $search . '%');
        }

        $builder->setFirstResult($offset)->setMaxResults($limit);
        $apilogentries = $builder->getQuery()->getArrayResult();
        $total = Shopware()->Models()->getQueryCount($builder->getQuery());
        $apilogentries = $this->addArrayRequestResponse($apilogentries);
        $ret = array('total' => $total, 'rows' => $apilogentries);
        $encoded = json_encode($ret);
        echo $encoded;
    }

    public function ajaxtransactionstatusAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $search = $this->Request()->get('search');

        $start = $this->Request()->get('start');
        $limit = $this->Request()->get('limit');

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('log')
            ->from('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog', 'log');

        $order = (array)$this->Request()->getParam('sort', array());

        if ($order) {
            foreach ($order as $ord) {
                $builder->addOrderBy('log.' . $ord['property'], $ord['direction']);
            }
        } else {
            $builder->addOrderBy('log.creationDate', 'DESC');
        }

        $builder->addOrderBy('log.creationDate', 'DESC');

        if (!empty($search)) {
            $builder->where($builder->expr()->orx(
                $builder->expr()->like('log.creationDate', '?1'),
                $builder->expr()->like('log.transactionId', '?1'),
                $builder->expr()->like('log.portalId', '?1'),
                $builder->expr()->like('log.status', '?1'),
                $builder->expr()->like('log.transactionDate', '?1'),
                $builder->expr()->like('log.updateDate', '?1'),
                $builder->expr()->like('log.balance', '?1'),
                $builder->expr()->like('log.details', '?1'),
                $builder->expr()->like('log.orderNr', '?1'),
                $builder->expr()->like('log.paymentId', '?1'),
                $builder->expr()->like('log.creationDate', '?1')
            ));
            $builder->setParameter('1', '%' . $search . '%');
        }

        $builder->setFirstResult($start)->setMaxResults($limit);

        $result = $builder->getQuery()->getArrayResult();
        $total = Shopware()->Models()->getQueryCount($builder->getQuery());
        $result = $this->addArrayOrderDetails($result);

        $ret = array('total' => $total, 'rows' => $result);
        $encoded = json_encode($ret);
        echo $encoded;
    }

    public function paymentstatusconfigAction()
    {
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        if (version_compare(Shopware()->Config()->version, '5.5', '>=') || Shopware()->Config()->get('version') === '___VERSION___') {
            $orderRepository = Shopware()->Models()->getRepository(Order::class);
            $data = $orderRepository->getPaymentStatusQuery()->getArrayResult();
            $data = array_map([$this, 'getPaymentStatusTranslation'], $data);
        } else {
            $builder = Shopware()->Models()->createQueryBuilder();
            $data = $builder->select('a.id, a.description')
                ->from('Shopware\Models\Order\Status', 'a')
                ->where('a.group = \'payment\'')
                ->getQuery()->getArrayResult();
        }

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "payonepaymentstates" => $data,
            "data" => $data,
        ));
    }

    public function paymentstatusconfigDataAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $data['data'] = $this->get('MoptPayoneMain')->getPayoneConfig($paymentid, true);
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function transactionlogAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $this->View()->assign(array
        (
            "data" => $data,
            "locale" => Shopware()->Container()->get('locale')
        ));
    }

    public function creditcardAction()
    {
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shops = $shopRepo->findAll();
        $this->View()->assign(array(
            "shops" => $shops,
        ));
    }

    public function ajaxgetIframeConfigAction()
    {
        $shopId = $this->Request()->getParam('shopId');
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig');
        if ($shopId) {
            $query = $this->getAllPaymentsQuery(array('shopId' => $shopId), null, $repository);
        } else {
            $query = $this->getAllPaymentsQuery(array('isDefault' => true), null, $repository);
        }
        $query = $this->getAllPaymentsQuery(array('shopId' => true), null, $repository);
        $configData = $query->getArrayResult();
        $data['data'] = $configData[0];
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxgetPaypalConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal');
        $query = $this->getAllPaymentsQuery(null, null, $repository);
        $iframedata = $query->getArrayResult();
        $data['iframedata'] = $iframedata;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxgetRatepayConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay');
        $query = $this->getAllPaymentsQuery(null, null, $repository);
        $ratepaydata = $query->getArrayResult();
        // replace currencyId field currency->name Field for Display
        foreach ($ratepaydata as $key => $ratepayconfig) {
            $currencies = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency');
            $currencyId = $ratepayconfig['currencyId'];
            $currency = $currencies->findOneBy(array('id' => $currencyId));
            $ratepaydata[$key]['currency'] = $currency->getName();
        }
        $data['ratepaydata'] = $ratepaydata;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxgetAmazonConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay');
        $query = $this->getAllPaymentsQuery(null, null, $repository);
        $amazondata = $query->getArrayResult();
        $data['amazondata'] = $amazondata;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function debitAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%debit%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function klarnaAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%klarna%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function ajaxwalletAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__ewallet%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();
        // remove paypal v2 and paypal express v2
        $payonepaymentmethods = array_filter($payonepaymentmethods, function ($item) {
            return $item['name'] !== 'mopt_payone__ewallet_paypalv2' && $item['name'] !== 'mopt_payone__ewallet_paypal_expressv2';
        });
        $amazonpayRepo = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay');
        $amazonpayConfigs = $amazonpayRepo->findAll();
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shops = $shopRepo->findAll();
        $paypalExpressRepo = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal');
        $paypalConfigs = $paypalExpressRepo->findAll();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
            "amazonpayconfigs" => $amazonpayConfigs,
            "shops" => $shops,
            "paypalconfigs" => $paypalConfigs,
        ));
    }

    public function amazonpayAction()
    {
        // $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $amazonpayRepo = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay');
        $amazonpayConfigs = $amazonpayRepo->findAll();
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shops = $shopRepo->findAll();
        $this->View()->assign(array(
            "amazonpayconfigs" => $amazonpayConfigs,
            "shops" => $shops,
        ));
    }

    public function ajaxgetWalletConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('id' => $paymentid), null, $repository);
        $paymentdata = $query->getArrayResult();
        $data['data'] = $paymentdata[0];
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function transactionstatusconfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function generalconfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function generalconfigDataAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentid = $this->Request()->getParam('paymentid');
        $data['data'] = $this->get('MoptPayoneMain')->getPayoneConfig($paymentid, true);
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function riskcheckAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function addresscheckAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function applepayAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__ewallet_apple%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function googlepayAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__ewallet_googlepay%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function unzerAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__fin_payolution%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function ajaxSavePayoneConfigAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->Request()->getPost();
        $data['status'] = 'success';
        $data['message'] = 'Zahlungsart erfolgreich gespeichert!';
        $this->createPayoneConfig($paymentData);
        $this->createPayoneCreditcardConfig($paymentData);
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxSaveIframeConfigAction()
    {
        $shopId = $this->Request()->getParam('shopId');
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->Request()->getPost();
        $data['status'] = 'success';
        $data['message'] = 'Konfiguration erfolgreich gespeichert!';
        $paymentData['shopId'] = $shopId;
        $this->createIframeConfig($paymentData);
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxSavePaypalConfigAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->Request()->getPost();
        $data['status'] = 'success';
        $data['message'] = 'Konfiguration erfolgreich gespeichert!';

        $paypalRepo = Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal'
        );
        $paypalConfigs = $paypalRepo->findAll();

        // Remove all configs that don't exist in the form data
        foreach ($paypalConfigs as $paypalConfig) {
            $configStillExists = false;
            foreach ($paymentData['row'] as $config) {
                if ($paypalConfig->getId() == $config['id']) {
                    $configStillExists = true;
                }
            }
            if ($configStillExists === false) {
                Shopware()->Models()->remove($paypalConfig);
            }
        }
        foreach ($paymentData['row'] as $config) {
            $this->createPaypalConfig($config);
        }
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxSaveApplepayCertAction()
    {
        $shoproot = Shopware()->Container()->getParameter('kernel.root_dir');
        $folder = '/var/cert/';
        if (!is_dir($shoproot . $folder)) {
            mkdir($shoproot . $folder, 0700);
        }
        $response = 0;
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (isset($_FILES['file']) && !empty($_FILES['file']['name'] && $_FILES['file']['size'] > 0)) {
            $fileData = $_FILES['file'];
            $filename = $fileData['name'];
            /* Location */
            $location = $shoproot . $folder . $filename;
            $fileType = pathinfo($location, PATHINFO_EXTENSION);
            $fileType = strtolower($fileType);
            /* Valid extensions */
            $valid_extensions = array("pem");

            $response = 0;
            /* Check file extension */
            if (in_array(strtolower($fileType), $valid_extensions)) {
                /* Upload file */
                // $test = move_uploaded_file($_FILES['file']['tmp_name'],$location);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                    chmod($location, 0644);
                    $response = $location;
                }
            }

        }
        echo $response;
    }

    public function ajaxSaveApplepayKeyAction()
    {
        $shoproot = Shopware()->Container()->getParameter('kernel.root_dir');
        $folder = '/var/cert/';
        if (!is_dir($shoproot . $folder)) {
            mkdir($shoproot . $folder, 0700);
        }

        $response = 0;
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (isset($_FILES['file']) && !empty($_FILES['file']['name'] && $_FILES['file']['size'] > 0)) {
            $fileData = $_FILES['file'];
            $filename = $fileData['name'];
            /* Location */
            $location = $shoproot . $folder . $filename;
            $fileType = pathinfo($location, PATHINFO_EXTENSION);
            $fileType = strtolower($fileType);
            /* Valid extensions */
            $valid_extensions = array("key");

            $response = 0;
            /* Check file extension */
            if (in_array(strtolower($fileType), $valid_extensions)) {
                /* Upload file */
                // $test = move_uploaded_file($_FILES['file']['tmp_name'],$location);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                    chmod($location, 0644);
                    $response = $location;
                }
            }

        }
        echo $response;
    }

    public function createIframeConfig($options)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig');
        $payment = $repository->findOneBy(
            array(
                'id' => '1'
            )
        );

        $payment->fromArray($options);
        Shopware()->Models()->persist($payment);
        Shopware()->Models()->flush($payment);

        return $payment;
    }

    public function createPaypalConfig($data)
    {
        // if new image was uploaded $data['image'] contains the image base64 encoded
        if (!empty($data['filename'])) {
            $mediaService = $this->container->get('shopware_media.media_service');
            $image = explode(';base64,', $data['image']);
            $imageDecoded = base64_decode($image[1]);
            $mediaService->write("media/image/{$data['filename']}", $imageDecoded);
            $url = $mediaService->getUrl("media/image/{$data['filename']}");
            $data['image'] = $url;
        }
        unset($data['filename']);

        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal');
        $payment = $repository->findOneBy(
            array(
                'id' => $data['id']
            )
        );
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shop = $shopRepo->findOneBy(
            array(
                'id' => $data['shop']
            )
        );
        $data['shop'] = $shop;

        if (!$payment) {
            $payment = new \Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal();
            $payment->setPackStationMode('deny');
            $payment->fromArray($data);
            Shopware()->Models()->persist($payment);
        } else {
            $payment->fromArray($data);
        }
        Shopware()->Models()->flush($payment);

        return $payment;
    }

    public function createPayoneConfig($options)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig');
        $data = $repository->findOneBy(
            array(
                'paymentId' => $options['paymentId']
            )
        );
        if ($data === null) {
            $olddata = $repository->findOneBy(
                array(
                    'paymentId' => 0
                )
            );
            $data = clone($olddata);
            $data->setPaymentId($options['paymentId']);
            $data->setId(null);
            Shopware()->Models()->persist($data);
        };

        $data->fromArray($options);
        if ($options['adresscheckActive'] == "false") {
            $data->setAdresscheckActive(0);
        }
        if ($options['adresscheckActive'] == "true") {
            $data->setAdresscheckActive(1);
        }
        if ($options['adresscheckLiveMode'] == "false") {
            $data->setAdresscheckLiveMode(0);
        }
        if ($options['adresscheckLiveMode'] == "true") {
            $data->setAdresscheckLiveMode(1);
        }
        if ($options['consumerscoreActive'] == "false") {
            $data->setConsumerscoreActive(0);
        }
        if ($options['consumerscoreActive'] == "true") {
            $data->setConsumerscoreActive(1);
        }
        if ($options['consumerscoreLiveMode'] == "false") {
            $data->setConsumerscoreLiveMode(0);
        }
        if ($options['consumerscoreLiveMode'] == "true") {
            $data->setConsumerscoreLiveMode(1);
        }
        if ($options['submitBasket'] == "false") {
            $data->setSubmitBasket(0);
        }
        if ($options['submitBasket'] == "true") {
            $data->setSubmitBasket(1);
        }
        if ($options['liveMode'] == "false") {
            $data->setLiveMode(0);
        }
        if ($options['liveMode'] == "true") {
            $data->setLiveMode(1);
        }
        if ($options['checkAccount'] == "false") {
            $data->setCheckAccount(0);
        }
        if ($options['checkAccount'] == "true") {
            $data->setCheckAccount(1);
        }
        if ($options['showAccountnumber'] == "false") {
            $data->setShowAccountnumber(0);
        }
        if ($options['showAccountnumber'] == "true") {
            $data->setShowAccountnumber(1);
        }
        if ($options['showBIC'] == "false") {
            $data->setShowBic(0);
        }
        if ($options['showBIC'] == "true") {
            $data->setShowBic(1);
        }
        if ($options['mandateActive'] == "false") {
            $data->setMandateActive(0);
        }
        if ($options['mandateActive'] == "true") {
            $data->setMandateActive(1);
        }
        if ($options['mandateDownloadEnabled'] == "false") {
            $data->setMandateDownloadEnabled(0);
        }
        if ($options['mandateDownloadEnabled'] == "true") {
            $data->setMandateDownloadEnabled(1);
        }
        if ($options['applepayVisa'] == "false") {
            $data->setApplepayVisa(0);
        }
        if ($options['applepayVisa'] == "true") {
            $data->setApplepayVisa(1);
        }
        if ($options['applepayMastercard'] == "false") {
            $data->setApplepayMastercard(0);
        }
        if ($options['applepayMastercard'] == "true") {
            $data->setApplepayMastercard(1);
        }
        if ($options['applepayGirocard'] == "false") {
            $data->setApplepayGirocard(0);
        }
        if ($options['applepayGirocard'] == "true") {
            $data->setApplepayGirocard(1);
        }
        if ($options['applepayAmex'] == "false") {
            $data->setApplepayAmex(0);
        }
        if ($options['applepayAmex'] == "true") {
            $data->setApplepayAmex(1);
        }
        if ($options['applepayDiscover'] == "false") {
            $data->setApplepayDiscover(0);
        }
        if ($options['applepayDiscover'] == "true") {
            $data->setApplepayDiscover(1);
        }
        if ($options['applepayDebug'] == "false") {
            $data->setApplepayDebug(0);
        }
        if ($options['applepayDebug'] == "true") {
            $data->setApplepayDebug(1);
        }
        if ($options['allowDifferentAddresses'] == "false") {
            $data->setAllowDifferentAddresses(0);
        }
        if ($options['allowDifferentAddresses'] == "true") {
            $data->setAllowDifferentAddresses(1);
        }
        if ($options['paypalExpressUseDefaultShipping'] == "false") {
            $data->setPaypalExpressUseDefaultShipping(0);
        }
        if ($options['paypalExpressUseDefaultShipping'] == "true") {
            $data->setPaypalExpressUseDefaultShipping(1);
        }
        if ($options['paydirektOrderSecured'] == "false") {
            $data->setPaydirektOrderSecured(0);
        }
        if ($options['paydirektOrderSecured'] == "true") {
            $data->setPaydirektOrderSecured(1);
        }
        if ($options['googlepayAllowVisa'] == "true") {
            $data->setGooglepayAllowVisa(1);
        }
        if ($options['googlepayAllowVisa'] == "false") {
            $data->setGooglepayAllowVisa(0);
        }
        if ($options['googlepayAllowMasterCard'] == "true") {
            $data->setGooglepayAllowMasterCard(1);
        }
        if ($options['googlepayAllowMasterCard'] == "false") {
            $data->setGooglepayAllowMasterCard(0);
        }
        if ($options['googlepayAllowPrepaidCards'] == "true") {
            $data->setGooglepayAllowPrepaidCards(1);
        }
        if ($options['googlepayAllowPrepaidCards'] == "false") {
            $data->setGooglepayAllowPrepaidCards(0);
        }
        if ($options['googlepayAllowCreditCards'] == "true") {
            $data->setGooglepayAllowCreditCards(1);
        }
        if ($options['googlepayAllowCreditCards'] == "false") {
            $data->setGooglepayAllowCreditCards(0);
        }
        if ($options['paypalV2ShowButton'] == "true") {
            $data->setPaypalV2ShowButton(1);
        }
        if ($options['paypalV2ShowButton'] == "false") {
            $data->setPaypalV2ShowButton(0);
        }
        if ($options['payolutionB2bmode'] == "true") {
            $data->setPayolutionB2bMode(1);
        }
        if ($options['payolutionB2bmode'] == "false") {
            $data->setPayolutionB2bMode(0);
        }
        if ($options['paypalExpressUseDefaultShipping'] == "true") {
            $data->setPaypalExpressUseDefaultShipping(1);
        }
        if ($options['paypalExpressUseDefaultShipping'] == "false") {
            $data->setPaypalExpressUseDefaultShipping(0);
        }
        if ($options['paypalEcsActive'] == "true") {
            $data->setPaypalEcsActive(1);
        }
        if ($options['paypalEcsActive'] == "false") {
            $data->setPaypalEcsActive(0);
        }
        Shopware()->Models()->flush($data);

        return $data;
    }

    public function createPayoneCreditcardConfig($options)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig');
        /** @var $creditcardConfig \Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig */
        $creditcardConfig = $repository->findOneBy(
            array(
                'shopId' => $options['paymentId']
            )
        );
        $creditcardConfig->fromArray($options);

        Shopware()->Models()->flush($creditcardConfig);
    }

    public function getAllIframeConfigQuery($filter, $order, $repository)
    {
        $builder = $repository->createQueryBuilder('p');
        $builder->select(
            array('p')
        );
        if ($filter !== null) {
            $builder->addFilter($filter);
        }
        if ($order !== null) {
            $builder->addOrderBy($order);
        }

        return $builder->getQuery();
    }

    public function getAllPaymentsQuery($filter = null, $order = null, $repository = null)
    {
        $builder = $repository->createQueryBuilder('p');
        $builder->select(
            array('p')
        );
        if ($filter !== null) {
            $builder->addFilter($filter);
        }
        if ($order !== null) {
            $builder->addOrderBy($order);
        }

        return $builder->getQuery();
    }

    protected function addArrayRequestResponse($result)
    {
        if (!empty($result)) {
            foreach ($result as $key => $entry) {
                $request = '';
                $response = '';
                $dataRequest = explode('|', $entry['requestDetails']);

                foreach ($dataRequest as $value) {
                    $tmp = explode('=', $value);
                    if ($tmp[1]) {
                        $request .= $tmp[0] . "=" . $tmp[1] . '<BR>';
                    }
                }
                $dataResponse = explode('|', $entry['responseDetails']);
                foreach ($dataResponse as $value) {
                    $tmp = explode('=', $value);
                    if ($tmp[0] == 'rawResponse') {
                        unset($tmp[1]);
                    }
                    if ($tmp[1]) {
                        $response .= $tmp[0] . "=" . $tmp[1] . '<BR>';
                    }
                }
                $result[$key]['requestArray'] = $request;
                $result[$key]['responseArray'] = $response;
            }
        }
        return $result;
    }

    protected function addArrayOrderDetails($result)
    {

        if (!empty($result)) {
            foreach ($result as $key => $entry) {
                $request = '';
                $dataResponse = $entry['details'];
                foreach ($dataResponse as $subkey => $value) {
                    if ($value) {
                        $request .= $subkey . "=" . $value . '<BR>';
                    }
                }
                $result[$key]['details'] = $request;
            }
        }
        return $result;
    }

    private function getPaymentStatusTranslation($dataArray)
    {
        switch ($dataArray['name']) {
            case 'amazon_failed':
                $dataArray['description'] = 'Amazon Failed';
                break;
            case 'amazon_delayed':
                $dataArray['description'] = 'Amazon Delayed';
                break;
            default:
                $dataArray['description'] = Shopware()->Snippets()
                    ->getNamespace('backend/static/payment_status')
                    ->get($dataArray['name'], false);

        }
        return $dataArray;
    }

    public function paypalexpressAction()
    {
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__ewallet_paypal_express'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shops = $shopRepo->findAll();
        $paypalExpressRepo = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal');
        $paypalConfigs = $paypalExpressRepo->findAll();

        $this->View()->assign(array(
            "shops" => $shops,
            "paypalconfigs" => $paypalConfigs,
            "payonepaymentmethods" => $payonepaymentmethods,
        ));
    }

    public function paypalexpressv2Action()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__ewallet_paypalv2%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
        ));
    }

    public function ratepayAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__fin_ratepay%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();
        $currencyRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency');
        $currencies = $currencyRepo->findAll();
        $ratePayRepo = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay');
        $ratepayConfigs = $ratePayRepo->findAll();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "data" => $data,
            "currencies" => $currencies,
            "ratepayconfigs" => $ratepayConfigs,
        ));
    }
}

/**
 * Logging class:
 */
class Logging
{
    private $log_file, $fp;

    public function lfile($path)
    {
        $this->log_file = $path;
    }

    public function lwrite($message)
    {
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        $time = date('[d.m.Y H:i:s]');
        fwrite($this->fp, "$time  $message<BR>");
    }

    public function lclose()
    {
        fclose($this->fp);
    }

    private function lopen()
    {
        $log_file_default = '/tmp/logfile.txt';
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        $this->fp = fopen($lfile, 'a');
    }
}
