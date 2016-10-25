<?php

/**
 * backend controller for payone ratepay 
 *
 * $Id: $
 */
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Backend_MoptPayoneRatepay extends Shopware_Controllers_Backend_Application implements CSRFWhitelistAware {

    protected $model = 'Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay';
    protected $alias = 'moptPayoneRatepay';

    /**
     * PayoneMain
     * @var Mopt_PayoneMain
     */
    protected $moptPayoneMain = null;
    protected $payoneServiceBuilder = null;
    protected $service = null;
    
    public function getWhitelistedCSRFActions()
    {
        $returnArray = array(
            'downloadRatepayConfigs',
            'saveRatepayConfigs',
        );
        return $returnArray;
    }    

    protected function getListQuery() {
        $builder = parent::getListQuery();
        return $builder;
    }

    protected function getDetailQuery($id) {
        $builder = parent::getDetailQuery($id);
        return $builder;
    }
    
    
    public function downloadRatepayConfigsAction(){
        $this->Front()->Plugins()->Json()->setRenderer(true);
        $builder = Shopware()->Models()->createQueryBuilder();
        $data = $builder->select('a')
                ->from('\Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay a')
                ->getQuery()->getArrayResult();
        
        foreach ($data as $dataItem){
            try{
                
            /**
             * @var $config \Shopware\Models\Payment\Payment 
             */
            $paymentRatepayId = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
                            array('name' => 'mopt_payone__fin_ratepay_invoice')
                    )->getId();
            
                    
            $ratepayProfile = $this->requestRatePayConfigFromApi($dataItem['shopid'], $dataItem['currency'], $paymentRatepayId);
            if (!$ratepayProfile){
                $errorElem[] = $dataItem['id'];
            } else {
            /**
             * @var $config \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay 
             */
            $config = $this->getManager()->find(
                    $this->model, $dataItem['id']
            );                
            $config->fromArray($ratepayProfile);
            $this->getManager()->persist($config);
            $this->getManager()->flush($config);
            }
                
            } catch (Exception $e){
                $errorElem[] = $dataItem['id'];
                
            }
        }
        $data['errorElem'] = $errorElem;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
        exit(0);            
    }
    
    public function saveRatepayConfigsAction(){
        $this->Front()->Plugins()->Json()->setRenderer(true);
        $params = $this->Request()->getParams();
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        
        foreach ($params[row] as $dataset ){
            /**
             * @var $config \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay 
             */
            $config = $this->getManager()->find(
                    $this->model, $dataset['id']
            );
            
            if (!$config){
                $config = new \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay;
            }
            
            $ratepayProfile = array();
            $ratepayProfile['shopid'] = $dataset['shopid'];
            $ratepayProfile['currency'] = $dataset['currency'];
            $config->fromArray($ratepayProfile);
            $this->getManager()->persist($config);
            $this->getManager()->flush($config);            
        }
        $data['errorElem'] = '';
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
        exit(0);        
    }    

    public function downloadConfigAction() {
        try {
            $params = $this->Request()->getParams();
            $configId = $this->Request()->getParam('configId');

            /**
             * @var $config \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay 
             */
            $config = $this->getManager()->find(
                    $this->model, $configId
            );

            /**
             * @var $config \Shopware\Models\Payment\Payment 
             */
            $paymentRatepayId = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
                            array('name' => 'mopt_payone__fin_ratepay_invoice')
                    )->getId();

            $currency = $config->getCurrency();
            $shopid = $config->getShopid();

            $ratepayProfile = $this->requestRatePayConfigFromApi($shopid, $currency, $paymentRatepayId);
            $config->fromArray($ratepayProfile);
            $this->getManager()->persist($config);
            $this->getManager()->flush($config);
            $this->View()->assign(array('success' => true));
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Returns the payment plugin config data.
     *
     * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    public function Plugin() {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * @param $ratePayShopId
     * @param $currency
     * @return bool|Payone_Api_Request_Parameter_Paydata_Paydata
     * @throws Exception
     */
    protected function requestRatePayConfigFromApi($ratePayShopId, $currency, $paymentRatepayId) {
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $config = $this->moptPayoneMain->getPayoneConfig($paymentRatepayId);
        $financeType = Payone_Api_Enum_RatepayType::RPV;
        $paymentType = Payone_Api_Enum_RatepayType::RPV_FULL;

        $profileResponse = $this->buildAndCallRatepayProfile($config, 'fnc', $financeType, $paymentType, $ratePayShopId, $currency);


        if ($profileResponse instanceof Payone_Api_Response_Genericpayment_Ok) {
            $payData = $profileResponse->getRatepayPaydataArray();
            $payData['shop_id'] = $ratePayShopId;
            return $payData;
        }
        return false;
    }

    protected function buildAndCallRatepayProfile($config, $clearingType, $financetype, $paymenttype, $ratePayShopId, $currency) {
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;
        //create hash
        $orderVariables = $session['sOrderVariables'];
        $orderHash = md5(serialize($orderVariables));
        $session->moptOrderHash = $orderHash;

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::RATEPAY_PROFILE)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'shop_id', 'data' => $ratePayShopId)
        ));

        $request->setPaydata($paydata);
        $request->setCurrency($currency);
        $request->setClearingtype($clearingType);
        $request->setFinancingType(Payone_Api_Enum_RatepayType::RPV);

        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $response = $this->service->request($request);
        return $response;
    }

}
