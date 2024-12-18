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
            'connectiontest',
            'ajaxgetTestResults',
            'ajaxconfig',
            'apilog',
            'ajaxapilog',
            'ajaxtransactionstatus',
            'ajaxpaymentstatusconfig',
            'ajaxgetPaymentStatusConfig',
            'ajaxgetRiskCheckConfig',
            'ajaxgetAddressCheckConfig',
            'transactionlog',
            'ajaxcreditcard',
            'ajaxgetCreditCardConfig',
            'ajaxgetIframeConfig',
            'ajaxgetPaypalConfig',
            'ajaxdebit',
            'ajaxgetDebitConfig',
            'ajaxfinance',
            'ajaxgetFinanceConfig',
            'ajaxonlinetransfer',
            'ajaxgetOnlineTransferConfig',
            'ajaxwallet',
            'ajaxgetWalletConfig',
            'ajaxtransactionstatusconfig',
            'ajaxgetTransactionStatusConfig',
            'ajaxgeneralconfig',
            'ajaxgetGeneralConfig',
            'ajaxriskcheck',
            'ajaxaddresscheck',
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
        );
        return $returnArray;
    }

    public function indexAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");

        $filename = __DIR__ . '/../../dashboardconfig.txt';
        
        $config = file_get_contents($filename);
        $aConfig = json_decode($config);
        $breadcrump = '';
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
            $ret[$i] = $data[0]['platzhalter'];
            $i++;
        }

        $this->View()->assign(array(
            "data" => $ret,
            "breadcrump" => $breadcrump,
            "title" => $title,
            "params" => $params,
            ));
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

    public function connectiontestAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->mid = $_GET['mid'];
        $this->aid = $_GET['aid'];
        $this->pid = $_GET['pid'];
        $this->apikey = $_GET['apikey'];

        unlink(Shopware()->Container()->get('kernel')->getLogDir() . '/moptPayoneConnectionTest.log');
        $this->logging->lwrite('<span style="color: green;">Starte Verbindungstest</span>', $this->aLogcontext);
        if (!$this->testfailed) {
            $this->creditcardCheckVisa(true);
        }
        if (!$this->testfailed) {
            $this->creditcardCheckMaster(true);
        }
        if (!$this->testfailed) {
            $this->vorkasseCheck(true);
        }
        if (!$this->testfailed) {
            $this->rechnungCheck(true);
        }
        if (!$this->testfailed) {
            $this->lastschriftCheck();
        }
    }

    /**
     * @return string
     */
    public function getFutureExpiredate() {
        $future = new DateTime();
        $future->modify("+2 years");
        return $future->format("ym");
    }

    public function creditcardCheckVisa($ecommercemode = null)
    {
        $this->aMinimumParams = array('clearingtype' => 'cc',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
            'cardpan' => '4111111111111111', 'cardtype' => 'V',
            'pseudocardpan' => '5500000000099999', 'cardexpiredate' => $this->getFutureExpiredate()
        );
        
        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();

        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $request = new Payone_Api_Request_Preauthorization($params);
        $request->setAid($this->aid);
        $request->setMid($this->mid);
        $request->setPortalid($this->pid);
        $request->setKey($this->apikey);
        $request->setAmount($this->aMinimumParams['amount']);
        $request->setCurrency($this->aMinimumParams['currency']);
        $request->setReference(rand(10000000, 99999999));
        $request->setClearingtype($this->aMinimumParams['clearingtype']);

        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $pData = new Payone_Api_Request_Parameter_Authorization_PersonalData();
        $pData->setFirstname($this->aMinimumParams['firstname']);
        $pData->setLastname($this->aMinimumParams['lastname']);
        $pData->setCountry($this->aMinimumParams['country']);
        $paymentData = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard;
        $paymentData->setCardexpiredate($this->aMinimumParams['cardexpiredate']);
        $paymentData->setCardpan($this->aMinimumParams['cardpan']);
        $paymentData->setCardtype($this->aMinimumParams['cardtype']);
        $paymentData->setEcommercemode('internet');

        $request->setPersonalData($pData);
        $request->setPayment($paymentData);
        $request->setSuccessurl('https://payone.com');
        $request->setMode('test');
        $generateHashService = $this->container->get('MoptPayoneBuilder')->buildServiceClientApiGenerateHash();
        $request->set('hash', $generateHashService->generate($request, $this->apikey));

        $this->logging->lwrite('<span style="color: yellow;">teste Request Authorisierung im Modus Test mit Zahlart Kreditkarte (Visa)</span>');
        $response = $this->service->preauthorize($request);
        if ($response->getStatus() == "APPROVED") {
            $this->logging->lwrite('<span style="color: green;">Test erfolgreich</span>');
        } else {
            $this->logging->lwrite('<span style="color: red;">Test fehlgeschlagen!!!</span>');
            $this->logging->lwrite('<span style="color: red;">Fehlermeldung:' . $response->getErrorMessage() . '</span>');

            $this->testfailed = true;
        }
        $this->View()->assign(array(
            "data" => $data,
            "params" => $params
            ));
    }

    public function creditcardCheckMaster($ecommercemode = null)
    {

        $this->aMinimumParams = array('clearingtype' => 'cc',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
            'cardpan' => '5500000000000004', 'cardtype' => 'M',
            'pseudocardpan' => '5500000000099999', 'cardexpiredate' => $this->getFutureExpiredate()
        );

        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");

        $request = new Payone_Api_Request_Preauthorization($params);
        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request->setAmount($this->aMinimumParams['amount']);
        $request->setCurrency($this->aMinimumParams['currency']);
        $request->setReference(rand(10000000, 99999999));
        $request->setClearingtype($this->aMinimumParams['clearingtype']);

        $pData = new Payone_Api_Request_Parameter_Authorization_PersonalData();
        $pData->setFirstname($this->aMinimumParams['firstname']);
        $pData->setLastname($this->aMinimumParams['lastname']);
        $pData->setCountry($this->aMinimumParams['country']);

        $paymentData = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard;
        $paymentData->setCardexpiredate($this->aMinimumParams['cardexpiredate']);
        $paymentData->setCardpan($this->aMinimumParams['cardpan']);
        $paymentData->setCardtype($this->aMinimumParams['cardtype']);

        $request->setPersonalData($pData);
        $request->setPayment($paymentData);

        $paymentData->setEcommercemode('internet');
        $request->setSuccessurl('https://payone.com');
        $request->setMode('test');

        $this->logging->lwrite('<span style="color: yellow;">teste Request Authorisierung im Modus Test mit Zahlart Kreditkarte (Mastercard)</span>');
        $response = $this->service->preauthorize($request);
        if ($response->getStatus() == "APPROVED") {
            $this->logging->lwrite('<span style="color: green;">Test erfolgreich</span>');
        } else {
            $this->logging->lwrite('<span style="color: red;">Test fehlgeschlagen!!!</span>');
            $this->logging->lwrite('<span style="color: red;">Fehlermeldung:' . $response->getErrorMessage() . '</span>');
            $this->testfailed = true;
        }

        $this->View()->assign(array(
            "data" => $data,
            "params" => $params
            ));
    }

    public function vorkasseCheck($ecommercemode = null)
    {

        $this->aMinimumParams = array('clearingtype' => 'vor',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE'
        );

        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize("mopt_payone__acc_payinadvance");
        $request = new Payone_Api_Request_Preauthorization($params);
        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
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

        $this->logging->lwrite('<span style="color: yellow;"> teste Request Authorisierung im Modus Test mit Zahlart Vorkasse</span>');
        $response = $this->service->preauthorize($request);
        if ($response->getStatus() == "APPROVED") {
            $this->logging->lwrite('<span style="color: green;">Test erfolgreich</span>');
        } else {
            $this->logging->lwrite('<span style="color: red;">Test fehlgeschlagen!!!</span>');
            $this->logging->lwrite('<span style="color: red;">Fehlermeldung:' . $response->getErrorMessage() . '</span>');
            $this->testfailed = true;
        }

        $this->View()->assign(array(
            "data" => $data,
            "params" => $params
            ));
    }

    public function rechnungCheck($ecommercemode = null)
    {

        $this->aMinimumParams = array('clearingtype' => 'rec',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
        );

        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();

        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize("mopt_payone__acc_invoice");
        $request = new Payone_Api_Request_Preauthorization($params);

        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
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

        $this->logging->lwrite('<span style="color: yellow;"> teste Request Authorisierung im Modus Test mit Zahlart Rechnung</span>');
        $response = $this->service->preauthorize($request);
        if ($response->getStatus() == "APPROVED") {
            $this->logging->lwrite('<span style="color: green;">Test erfolgreich</span>');
        } else {
            $this->logging->lwrite('<span style="color: red;">Test fehlgeschlagen!!!</span>');
            $this->logging->lwrite('<span style="color: red;">Fehlermeldung:' . $response->getErrorMessage() . '</span>');
            $this->testfailed = true;
        }


        $this->View()->assign(array(
            "data" => $data,
            "params" => $params
            ));
    }

    public function lastschriftCheck()
    {

        $this->aMinimumParams = array('clearingtype' => 'elv',
            'amount' => '2099', 'currency' => 'EUR',
            'firstname' => 'Timo', 'lastname' => 'Tester', 'country' => 'DE',
            'bankaccount' => '2599100003',
        );

        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();

        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize("mopt_payone__acc_debitnote");
        $request = new Payone_Api_Request_Preauthorization($params);

        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request->setAmount($this->aMinimumParams['amount']);
        $request->setCurrency($this->aMinimumParams['currency']);
        $request->setReference(rand(10000000, 99999999));
        $request->setClearingtype($this->aMinimumParams['clearingtype']);

        $pData = new Payone_Api_Request_Parameter_Authorization_PersonalData();
        $pData->setFirstname($this->aMinimumParams['firstname']);
        $pData->setLastname($this->aMinimumParams['lastname']);
        $pData->setCountry($this->aMinimumParams['country']);

        $paymentData = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment;
        $paymentData->setBankaccount($this->aMinimumParams['bankaccount']);
        $paymentData->setBankcountry($this->aMinimumParams['country']);

        $request->setPersonalData($pData);
        $request->setPayment($paymentData);
        $request->setMode('test');

        $this->logging->lwrite('<span style="color: yellow;">teste Request Authorisierung im Modus Test mit Zahlart Lastschrift</span>');

        $response = $this->service->preauthorize($request);
        if ($response->getStatus() == "APPROVED") {
            $this->logging->lwrite('<span style="color: green;">Test erfolgreich</span>');
        } else {
            $this->logging->lwrite('<span style="color: red;">Test fehlgeschlagen!!!</span>');
            $this->logging->lwrite('<span style="color: red;">Fehlermeldung:' . $response->getErrorMessage() . '</span>');
            $this->testfailed = true;
        }

        $this->View()->assign(array(
            "data" => $data,
            "params" => $params
            ));
    }

    public function ajaxgetTestResultsAction()
    {

        $data = array();
        sleep(2);
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $filename = Shopware()->Container()->get('kernel')->getLogDir() . '/moptPayoneConnectionTest.log';
        $resultfile = fopen($filename, "r");
        $aLines = '';
        while (!feof($resultfile)) {
            $aLines .= fgets($resultfile) . "<br>";
        }

        fclose($resultfile);
        $data = $aLines;
        echo $data;
    }

    public function ajaxconfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Allgemein", "ajaxconfig", "Verbindung"
        );

        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function apilogAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Protokolle", "apilog", "API-Anfragen"
        );

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
            "breadcrump" => $breadcrump,
            "params" => $params,
            ));
    }

    public function ajaxapilogAction()
    {

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $offset = $this->Request()->get('offset');
        $limit = $this->Request()->get('limit');

        // API Log Entries
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(array('log'))
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

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
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Features", "ajaxtransactionstatusconfig", "Statusweiterleitung"
        );
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $start = $this->Request()->get('start');
        $limit = $this->Request()->get('limit');

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('log')
            ->from('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog', 'log');

        $order = (array) $this->Request()->getParam('sort', array());

        if ($order) {
            foreach ($order as $ord) {
                $builder->addOrderBy('log.' . $ord['property'], $ord['direction']);
            }
        } else {
            $builder->addOrderBy('log.creationDate', 'DESC');
        }

        $builder->addOrderBy('log.creationDate', 'DESC');

        $builder->setFirstResult($start)->setMaxResults($limit);

        $result = $builder->getQuery()->getArrayResult();
        $total = Shopware()->Models()->getQueryCount($builder->getQuery());
        $result = $this->addArrayOrderDetails($result);

        $ret = array('total' => $total, 'rows' => $result);
        $encoded = json_encode($ret);
        echo $encoded;
    }

    public function ajaxpaymentstatusconfigAction()
    {
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Features", "ajaxpaymentstatusconfig", "Paymentstatus"
        );
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
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxgetPaymentStatusConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $data['data'] = $this->get('MoptPayoneMain')->getPayoneConfig($paymentid, true);
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxgetRiskCheckConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $data['data'] = $this->get('MoptPayoneMain')->getPayoneConfig($paymentid, true);
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxgetAddressCheckConfigAction()
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
        $breadcrump = array(
            "Konfiguration", "Protokolle", "transactionlog", "Zahlstatus"
        );

        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $this->View()->assign(array
            (
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxcreditcardAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Zahlungsarten", "ajaxcreditcard", "Kreditkarte"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__cc%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxgetCreditCardConfigAction()
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

    public function ajaxgetIframeConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig');
        $query = $this->getAllPaymentsQuery(array('isDefault' => '1'), null, $repository);
        $iframedata = $query->getArrayResult();
        $data['iframedata'] = $iframedata[0];
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
        $query = $this->getAllPaymentsQuery(null,null,$repository);
        $ratepaydata = $query->getArrayResult();
        // replace currencyId field currency->name Field for Display
        foreach($ratepaydata as $key => $ratepayconfig){
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
        $query = $this->getAllPaymentsQuery(null,null,$repository);
        $amazondata = $query->getArrayResult();
        $data['amazondata'] = $amazondata;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxdebitAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Zahlungsarten", "ajaxdebit", "Kontobasiert"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__acc%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxgetDebitConfigAction()
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

    public function ajaxfinanceAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Zahlungsarten", "ajaxfinance", "Finanzierung"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__fin%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();
    
        $currencyRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency');
        $currencies = $currencyRepo->findAll();
        $ratePayRepo = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay');
        $ratepayConfigs = $ratePayRepo->findAll();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            "currencies" => $currencies,
            "ratepayconfigs" => $ratepayConfigs,
            ));
    }

    public function ajaxgetFinanceConfigAction()
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

    public function ajaxonlinetransferAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Zahlungsarten", "ajaxonlinetransfer", "Onlineüberweisung"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__ibt%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxgetOnlineTransferConfigAction()
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

    public function ajaxwalletAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Zahlungsarten", "ajaxwallet", "Wallet"
        );
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
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            "amazonpayconfigs" => $amazonpayConfigs,
            "shops" => $shops,
            "paypalconfigs" => $paypalConfigs,
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

    public function ajaxtransactionstatusconfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Features", "ajaxtransactionstatusconfig", "Statusweiterleitung"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxgetTransactionStatusConfigAction()
    {
        $data = array();
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $data['data'] = $this->get('MoptPayoneMain')->getPayoneConfig($paymentid, true);
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxgeneralconfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Features", "ajaxgeneralconfig", "Allgemein"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxgetGeneralConfigAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $paymentid = $this->Request()->getParam('paymentid');
        $data['data'] = $this->get('MoptPayoneMain')->getPayoneConfig($paymentid, true);
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxriskcheckAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Risk", "ajaxriskcheck", "Bonitätsprüfung"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            "data" => $data,
            ));
    }

    public function ajaxaddresscheckAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Risk", "ajaxriskcheck", "Addressprüfung"
        );
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            ));
    }

    public function ajaxtextblocksAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $params = $this->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize("mopt_payone_creditcard");
        $breadcrump = array(
            "Konfiguration", "Allgemein", "ajaxtextblocks", "Textbausteine"
        );
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $query = $this->getAllPaymentsQuery(array('name' => 'mopt_payone__%'), null, $repository);
        $payonepaymentmethods = $query->getArrayResult();

        $this->View()->assign(array(
            "payonepaymentmethods" => $payonepaymentmethods,
            "breadcrump" => $breadcrump,
            "params" => $params,
            ));
    }

    public function ajaxgettextblocksAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $offset = $this->Request()->get('offset');
        $limit = $this->Request()->get('limit');
        $localeId = $this->Request()->get('localeId');

        // API Log Entries
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('snippets')
            ->from('Shopware\Models\Snippet\Snippet', 'snippets');
        $builder->Where('snippets.localeId = :localeId')
            ->setParameter('localeId', $localeId);
        $builder->andWhere('snippets.namespace = :namespace1')
            ->setParameter('namespace1', 'frontend/MoptPaymentPayone/errorMessages');
        $builder->orWhere('snippets.namespace = :namespace2')
            ->setParameter('namespace2', 'frontend/MoptPaymentPayone/messages');
        $builder->orWhere('snippets.namespace = :namespace3')
            ->setParameter('namespace3', 'frontend/MoptPaymentPayone/payment');
        $builder->setFirstResult($offset)->setMaxResults($limit);
        $apilogentries = $builder->getQuery()->getArrayResult();
        $total = Shopware()->Models()->getQueryCount($builder->getQuery());
        $apilogentries = $this->addArrayRequestResponse($apilogentries);
        $ret = array('total' => $total, 'rows' => $apilogentries);
        $encoded = json_encode($ret);
        echo $encoded;
    }

    public function ajaxsavetextblocksAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $localeId = $this->Request()->get('localeId');
        $snippetId = $this->Request()->get('snippetId');

        $snippetData = $this->Request()->getPost();
        unset($snippetData['name']);
        $this->updateSnippet($snippetData);

        $data['status'] = 'success';
        $data['message'] = 'Zahlungsart erfolgreich gespeichert!';

        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxSavePaymentConfigAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->Request()->getPost();
        $this->createPayment($paymentData);
        $data['status'] = 'success';
        $data['message'] = 'Zahlungsart erfolgreich gespeichert!';
        $encoded = json_encode($data);
        echo $encoded;
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
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->Request()->getPost();
        $data['status'] = 'success';
        $data['message'] = 'Konfiguration erfolgreich gespeichert!';
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
        foreach ($paymentData['row'] As $config) {
            $this->createPaypalConfig($config);
        }
        $encoded = json_encode($data);
        echo $encoded;
    }

    public function ajaxSaveApplepayCertAction()
    {
        $shoproot = Shopware()->Container()->getParameter('kernel.root_dir');
        $folder = '/var/cert/';
        if (!is_dir($shoproot.$folder)) {
            mkdir($shoproot.$folder, 0700);
        }
        $response = 0;
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (isset($_FILES['file']) && !empty($_FILES['file']['name'] && $_FILES['file']['size'] > 0)) {
            $fileData = $_FILES['file'];
            $filename = $fileData['name'];
            /* Location */
            $location = $shoproot . $folder . $filename;
            $fileType = pathinfo($location,PATHINFO_EXTENSION);
            $fileType = strtolower($fileType);
            /* Valid extensions */
            $valid_extensions = array("pem");

            $response = 0;
            /* Check file extension */
            if(in_array(strtolower($fileType), $valid_extensions)) {
                /* Upload file */
                // $test = move_uploaded_file($_FILES['file']['tmp_name'],$location);
                if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
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
        if (!is_dir($shoproot.$folder)) {
            mkdir($shoproot.$folder, 0700);
        }

        $response = 0;
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (isset($_FILES['file']) && !empty($_FILES['file']['name'] && $_FILES['file']['size'] > 0)) {
            $fileData = $_FILES['file'];
            $filename = $fileData['name'];
            /* Location */
            $location = $shoproot . $folder . $filename;
            $fileType = pathinfo($location,PATHINFO_EXTENSION);
            $fileType = strtolower($fileType);
            /* Valid extensions */
            $valid_extensions = array("key");

            $response = 0;
            /* Check file extension */
            if(in_array(strtolower($fileType), $valid_extensions)) {
                /* Upload file */
                // $test = move_uploaded_file($_FILES['file']['tmp_name'],$location);
                if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
                    chmod($location, 0644);
                    $response = $location;
                }
            }

        }
        echo $response;
    }

    public function updateSnippet($options)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Snippet\Snippet');
        $snippet = $repository->findOneBy(
            array(
                'id' => $options['pk']
            )
        );

        $snippet->fromArray($options);

        Shopware()->Models()->flush($snippet);

        return $snippet;
    }

    public function createPayment($options)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment');
        $payment = $repository->findOneBy(
            array(
                'id' => $options['paymentId']
            )
        );
        if ($payment === null) {
            $payment = new \Shopware\Models\Payment\Payment();
            $payment->setName($options['name']);
            Shopware()->Models()->persist($payment);
        };
        $payment->fromArray($options);
        Shopware()->Models()->flush($payment);

        return $payment;
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
        Shopware()->Models()->flush($payment);

        return $payment;
    }

    public function createPaypalConfig($data)
    {
        // if new image was uploaded $data['image'] contains the image base64 encoded
        if (!empty($data['filename'])) {
            $mediaService = $this->container->get('shopware_media.media_service');
            $image = explode( ';base64,', $data['image']);
            $imageDecoded = base64_decode($image[1]);
            $mediaService->write("media/image/{$data['filename']}", $imageDecoded);
            $url =  $mediaService->getUrl("media/image/{$data['filename']}");
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

        if (! $payment) {
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
        Shopware()->Models()->flush($data);

        return $data;
    }

    public function createPayoneCreditcardConfig($options)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig');
        /** @var $creditcardConfig \Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig */
        $creditcardConfig = $repository->findOneBy(
            array(
                'id' => '1'
            )
        );
        if (isset($options['merchantId'])) {
            $creditcardConfig->setMerchantId($options['merchantId']);
        }
        if (isset($options['portalId'])) {
            $creditcardConfig->setPortalId($options['portalId']);
        }
        if (isset($options['subaccountId'])) {
            $creditcardConfig->setSubaccountId($options['subaccountId']);
        }
        if (isset($options['apiKey'])) {
            $creditcardConfig->setApiKey($options['apiKey']);
        }

        Shopware()->Models()->flush($creditcardConfig);
    }

    public function getAllIframeConfigQuery($filter , $order, $repository)
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

        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
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
