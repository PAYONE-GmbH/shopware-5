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

    protected function getListQuery()
    {
        $builder = parent::getListQuery();
        $builder->leftJoin('moptPayoneRatepay.currency', 'currency');
        $builder->addSelect(array('currency'));
        return $builder;
    }

    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);
        $builder->leftJoin('moptPayoneRatepay.currency', 'currency');
        $builder->addSelect(array('currency'));
        return $builder;
    }
    
    
    public function downloadRatepayConfigsAction()
    {
        $this->Front()->Plugins()->Json()->setRenderer(true);
        $builder = Shopware()->Models()->createQueryBuilder();
        $data = $builder->select('a')
                ->from('\Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay', 'a')
                ->getQuery()->getArrayResult();
        
        foreach ($data as $dataItem) {
            try {
                
                /**
                 * @var $config \Shopware\Models\Payment\Payment
                 */
                $paymentRatepayId = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
                    array('name' => 'mopt_payone__fin_ratepay_invoice')
                )->getId();

                $currencies = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency');
                $currencyObj = $currencies->find($dataItem['currencyId']);
                $dataItem['currency'] = $currencyObj->getCurrency();

                $ratepayProfile = $this->requestRatePayConfigFromApi(
                    $dataItem['shopid'],
                    $dataItem['currency'],
                    $paymentRatepayId
                );
                if (!$ratepayProfile) {
                    $errorElem[] = $dataItem['id'];
                } else {
                    /**
                     * @var $config \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay
                     */
                    $config = $this->getManager()->find(
                        $this->model,
                        $dataItem['id']
                    );

                    unset($ratepayProfile['currency']);
                    $config->fromArray($ratepayProfile);
                    $this->getManager()->persist($config);
                    $this->getManager()->flush($config);
                }
            } catch (Exception $e) {
                $errorElem[] = $dataItem['id'];
            }
        }

        $data['errorElem'] = $errorElem;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
        exit(0);
    }
    
    public function saveRatepayConfigsAction()
    {
        $this->Front()->Plugins()->Json()->setRenderer(true);
        $params = $this->Request()->getParams();
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        $ratePayRepo = Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay'
        );
        $ratepayConfigs = $ratePayRepo->findAll();

        // Remove all configs that don't exist in the form data
        foreach ($ratepayConfigs as $ratepayConfig) {
            $configStillExists = false;
            foreach ($params['row'] as $ratepayConfigFromJSON) {
                if ($ratepayConfig->getId() == $ratepayConfigFromJSON['id']) {
                    $configStillExists = true;
                }
            }
            if ($configStillExists === false) {
                $this->getManager()->remove($ratepayConfig);
            }
        }

        // Update or create all configs that exist in the form data
        foreach ($params['row'] as $dataset) {
            $config = $this->getManager()->find(
                $this->model,
                $dataset['id']
            );

            if (!$config) {
                $config = new \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay;
            }

            $ratepayProfile = [];
            $ratepayProfile['shopid'] = $dataset['shopid'];
            $currencies = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency');
            $currencyObj =$currencies->find($dataset['currency']);
            $ratepayProfile['currency'] = $currencyObj;
            $ratepayProfile['currencyId'] = $currencyObj->getId();
            $ratepayProfile['ratepayInstallmentMode'] = $dataset['ratepayInstallmentMode'];
            $config->fromArray($ratepayProfile);
            $this->getManager()->persist($config);
        }
        $this->getManager()->flush($config);
        $data['errorElem'] = '';
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
        exit(0);
    }

    public function downloadConfigAction()
    {
        $configId = $this->Request()->getParam('configId');

        /**
         * @var $config \Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay
         */
        $config = $this->getManager()->find(
            $this->model,
            $configId
        );

        /**
         * @var $paymentRatepayId \Shopware\Models\Payment\Payment
         */
        $paymentRatepayId = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            array('name' => 'mopt_payone__fin_ratepay_invoice')
        )->getId();

        $currency = $config->getCurrency();
        $shopid = $config->getShopid();
        $currencyIso = $currency->getCurrency();
        try {
            $ratepayProfile = $this->requestRatePayConfigFromApi($shopid, $currencyIso, $paymentRatepayId);
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        // Currency is not used
        if (isset($ratepayProfile)) {
            unset($ratepayProfile['currency']);
            unset($ratepayProfile['currencyId']);
            $config->fromArray($ratepayProfile);
            $this->getManager()->persist($config);
            $this->getManager()->flush($config);
            $this->View()->assign(array('success' => true, 'error' => ''));
        }
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

    /**
     * @param $ratePayShopId
     * @param $currency
     * @return bool|Payone_Api_Request_Parameter_Paydata_Paydata
     * @throws Exception
     */
    protected function requestRatePayConfigFromApi($ratePayShopId, $currency, $paymentRatepayId)
    {
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $config = $this->moptPayoneMain->getPayoneConfig($paymentRatepayId);
        $financeType = Payone_Api_Enum_RatepayType::RPV;
        $paymentType = Payone_Api_Enum_RatepayType::RPV_FULL;
        try {
            $profileResponse = $this->buildAndCallRatepayProfile(
                $config,
                'fnc',
                $financeType,
                $paymentType,
                $ratePayShopId,
                $currency
            );
        } catch (Exception $e) {
        }

        if (isset($profileResponse) && $profileResponse instanceof Payone_Api_Response_Genericpayment_Ok) {
            $payData = $profileResponse->getRatepayPayDataArray();
            $payData['shop_id'] = $ratePayShopId;
            return $payData;
        }  else {
            throw new Exception("Shop-Id: " . $ratePayShopId ." ". $profileResponse->getErrorMessage());
        }
    }

    /**
     * @param $config
     * @param $clearingType
     * @param $financetype
     * @param $paymenttype
     * @param $ratePayShopId
     * @param $currency
     * @return mixed
     */
    protected function buildAndCallRatepayProfile(
        $config,
        $clearingType,
        $financetype,
        $paymenttype,
        $ratePayShopId,
        $currency
    ) {
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;

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
