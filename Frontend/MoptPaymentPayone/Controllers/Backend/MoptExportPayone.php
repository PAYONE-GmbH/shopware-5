<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptExportPayone extends Shopware_Controllers_Backend_ExtJs
{
    protected $moptPayone__sdk__Builder   = null;
    protected $moptPayone__main           = null;
    protected $moptPayone__helper         = null;
    protected $moptPayone__paymentHelper  = null;
    protected $transactionForwardingUrls  = array();

 /**
   * export global config and payment method configurations
   * it's just one shop which will be exported, but all payment methods from all shops are included
   * 
   * @throws Exception
   */
  public function generateConfigExportAction()
  {
    //$this->Front()->Plugins()->ViewRenderer()->setNoRender();
      
    $this->moptPayone__sdk__Builder = Shopware()->Plugins()->Frontend()
              ->MoptPaymentPayone()->Application()->MoptPayoneBuilder();
    $this->moptPayone__main = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->MoptPayoneMain();
    $this->moptPayone__helper = $this->moptPayone__main->getHelper();
    $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();
      
    $globalPayoneConfig = $this->moptPayone__main->getPayoneConfig();
      
    /** @var $service Payone_Settings_Service_XmlGenerate */
    $service = $this->moptPayone__sdk__Builder->buildServiceSettingsXmlGenerate();
     
    $global = new Payone_Settings_Data_ConfigFile_Shop_Global();
    $global->setMid($globalPayoneConfig['merchantId']);
    $global->setAid($globalPayoneConfig['subaccountId']);
    $global->setPortalid($globalPayoneConfig['portalId']);
      
    $global->setParameterInvoice(array()); // nothing to set
    $globalCreditCardConfig = $this->getGlobalCreditcardConfig();
    $global->setPaymentCreditcard($globalCreditCardConfig); // creditcard payment methods are handled separately
      
    if ($globalPayoneConfig['authorisationMethod'] == 'preAuthorise' || $globalPayoneConfig['authorisationMethod'] == 'Vorautorisierung')
    {
      $global->setRequestType(Payone_Api_Enum_RequestType::PREAUTHORIZATION);
    }
    else
    {
      $global->setRequestType(Payone_Api_Enum_RequestType::AUTHORIZATION);
    }
    $statusMapping = new Payone_Settings_Data_ConfigFile_Global_StatusMapping();
    $personStatusMapping = array();
    $transactionStatusForwarding = new Payone_Settings_Data_ConfigFile_Misc_TransactionstatusForwarding();
    
    $transactionForwardingUrlsGlobal = '';

    foreach ($globalPayoneConfig as $configKey => $configValue)
    {
      if(strpos($configKey, 'state') === 0)
      {
        $statusMapping->addStatusMapping($configKey, $configValue);
      }
      if(strpos($configKey, 'map') === 0)
      {
        $personStatusMapping[$configKey] = $configValue;
      }
      if(strpos($configKey, 'trans') === 0 && !empty($configValue)) 
     {
        $transactionForwardingUrlsGlobal = $transactionForwardingUrlsGlobal . ';' . $configValue;
      }
    }

    $transactionForwardingUrlsGlobal = urlencode(ltrim($transactionForwardingUrlsGlobal, ';'));
    $this->transactionForwardingUrls['global'] = $transactionForwardingUrlsGlobal;
    $global->setStatusMapping($statusMapping);

    $shop = new Payone_Settings_Data_ConfigFile_Shop();
    $em = Shopware()->Models();
    $shopRepository = $em->getRepository('Shopware\Models\Shop\Shop');
    $defaultShop = $shopRepository->getActiveDefault();
    $shop->setName($defaultShop->getName());
    $shop->setCode($this->generateFileHash()); //what is this param used for?, now using for filehash
      
    $system = new Payone_Settings_Data_ConfigFile_Shop_System();
    $system->setName('shopware');
    $system->setEdition(Shopware()->Config()->Version);
    $system->setModules($this->getModules());

    $protect = new Payone_Settings_Data_ConfigFile_Shop_Protect();
    $addressCheck = new Payone_Settings_Data_ConfigFile_Protect_Addresscheck();
    $addressCheck->setActive($globalPayoneConfig['adresscheckActive']);
    $addressCheck->setCheckbilling($globalPayoneConfig['adresscheckBillingAdress']);
    $addressCheck->setCheckshipping($globalPayoneConfig['adresscheckShippingAdress  ']);
    $addressCheck->setMaxOrderTotal($globalPayoneConfig['adresscheckMaxBasket']);
    $addressCheck->setMinOrderTotal($globalPayoneConfig['adresscheckMinBasket']);
    $addressCheck->setMode($globalPayoneConfig['adresscheckLiveMode']);
    $addressCheck->setPersonstatusmapping($personStatusMapping);

    $consumerscore = new Payone_Settings_Data_ConfigFile_Protect_Consumerscore();
    $consumerscore->setActive($globalPayoneConfig['onsumerscoreActive']);
    $consumerscore->setAddresscheck($globalPayoneConfig['consumerscoreCheckMode']);
    $consumerscore->setDuetime($globalPayoneConfig['consumerscoreLifetime']);
    $consumerscore->setMaxOrderTotal($globalPayoneConfig['consumerscoreMaxBasket']);
    $consumerscore->setMinOrderTotal($globalPayoneConfig['consumerscoreMinBasket']);
    $consumerscore->setMode($globalPayoneConfig['consumerscoreLiveMode']);

    $paymentMethods = $this->getPaymentMethods($globalPayoneConfig['checkCc']);

    $clearingTypes = new Payone_Settings_Data_ConfigFile_Shop_ClearingTypes();
    $clearingTypes->setClearingtypes($paymentMethods);

    $misc = new Payone_Settings_Data_ConfigFile_Shop_Misc();
    $misc->setShippingcosts($this->getShippingCosts());
    $transactionStatusForwarding->setTransactionstatusForwarding($this->transactionForwardingUrls);
    $misc->setTransactionstatusforwarding($transactionStatusForwarding);

    $config = new Payone_Settings_Data_ConfigFile_Root();
    $shop->setGlobal($global);
    $protect->setAddresscheck($addressCheck);
    $protect->setConsumerscore($consumerscore);
    $shop->setProtect($protect);
    $shop->setClearingtypes($clearingTypes);
    $shop->setMisc($misc);
    $shop->setSystem($system);
    $config->setShop($shop);

    try
    {
      $export = $service->execute($config);
      $dom = new DOMDocument('1.0');
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($export);
      
      $response = array('success' => true, 'moptConfigExport' => $dom->saveXML());
    }
    catch (Exception $e)
    {
      $response = array('success' => false, 'error_message' => $e->getMessage());
    }

    $this->View()->assign($response);
  }

    protected function getModules()
    {
        $em = Shopware()->Models();

        $repository = $em->getRepository('Shopware\Models\Plugin\Plugin');
        $builder    = $repository->createQueryBuilder('plugin');
        $builder->andWhere('plugin.capabilityEnable = true');
        $builder->addOrderBy('plugin.active', 'desc');
        $builder->addOrderBy('plugin.name');
        $builder->andWhere('plugin.active = true');

        $plugins = $builder->getQuery()->execute();

        $rows = array();

        foreach ($plugins as $plugin)
        {
            $rows[$plugin->getName()] =$plugin->getVersion();
        }
        
        return $rows;
    }
 
    protected function getShippingCosts()
    {
        $queryBuilder = Shopware()->Models()->getRepository('Shopware\Models\Dispatch\Dispatch')->getDispatchesQueryBuilder();
        
        $dispatches = $queryBuilder->getQuery()->execute();
        $shippingMethods = array();
        foreach ($dispatches as $shippingMethod)
        {
            $key = $shippingMethod['name'] . ' ' . $shippingMethod['id'];
            $key = str_replace(' ', '_', $key);
            $key = str_replace(array( '(', ')' ), '', $key);
            $shippingMethods[$key] = $shippingMethod;
        }
        
        return $shippingMethods;
    }
    
    protected function getPaymentMethods($checkCvC)
    {
      $paymentMethods = array();
        
      $repository = Shopware()->Models()->Payment();
      $query = $repository->getListQuery();
      $results = $query->getArrayResult();

      foreach ($results as $paymentMethod)
      {
          if(!$this->moptPayone__paymentHelper->isPayonePaymentMethod($paymentMethod['name']))
          {
              continue;
          }
          
          $paymentMethodData = $this->createPaymentExportObject($paymentMethod, $checkCvC);
          if($paymentMethodData) {
              $paymentMethods[] = $paymentMethodData;
          }
      }
      return $paymentMethods;
    }
    
    /**
     * 
     * @param array $paymentMethod
     * @param string $checkCvC
     * @return \Payone_Settings_Data_ConfigFile_PaymentMethod_Abstract
     */
    protected function createPaymentExportObject(array $paymentMethod, $checkCvC)
    {
      $paymentHelper = $this->moptPayone__paymentHelper;
      $paymentName = $paymentMethod['name'];
      $config = $this->moptPayone__main->getPayoneConfig($paymentMethod['id']);
         
      if($paymentHelper->isPayoneCreditcardForExport($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_Creditcard();
        $paymentDto->setCvc2($checkCvC);
      }
      
      if($paymentHelper->isPayonePayInAdvance($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_AdvancePayment();
      }
      
      if($paymentHelper->isPayoneCashOnDelivery($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_CashOnDelivery();
        $paymentDto->setNewOrderStatus(0); // 0 = open
      }
      
      if($paymentHelper->isPayoneDebitnote($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_DebitPayment();
        $paymentDto->setNewOrderStatus(0); // 0 = open
        $paymentDto->setBankAccountCheck($config['checkAccount']);
      }
      
      if($paymentHelper->isPayoneFinance($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_Financing();
        $paymentDto->setFinancingType($this->getFinancingType($paymentHelper, $paymentName));
        if($paymentHelper->isPayoneKlarna($paymentName))
        {
          $paymentDto->setKlarnaConfig(array('klarnaStoreId' => $config['klarnaStoreId'],
              'klarnaCampaignCode' => $config['klarnaCampaignCode']));
        }
      }
      
      if($paymentHelper->isPayoneInvoice($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_Invoice();
        $paymentDto->setNewOrderStatus(0); // 0 = open
      }
      
      if($paymentHelper->isPayoneInstantBankTransfer($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_OnlineBankTransfer();
      }
      
      if($paymentHelper->isEWallet($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_Wallet();
        
      }
      
      if($paymentHelper->isPayoneBarzahlen($paymentName))
      {
        $paymentDto = new Payone_Settings_Data_ConfigFile_PaymentMethod_Wallet();
        
      }
      
        if (!$paymentDto) {
            return false;
        }

      $paymentDto->setKey($paymentMethod['name']);
      $paymentDto->setTitle($paymentMethod['name']);
      $paymentDto->setId($paymentMethod['id']);
      $paymentDto->setMid($config['merchantId']);
      $paymentDto->setAid($config['subaccountId']);
      $paymentDto->setPortalid($config['portalId']);
      $paymentDto->setFeeConfig($paymentMethod['surcharge']);

      $paymentDto->setActive($paymentMethod['active']);
      $paymentDto->setCountries($this->getCountriesFromPaymentMethod($paymentMethod['countries']));
      $paymentDto->setMode($config['liveMode']);
      
      if ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung')
      {
        $paymentDto->setAuthorization(Payone_Api_Enum_RequestType::PREAUTHORIZATION);
      }
      else
      {
        $paymentDto->setAuthorization(Payone_Api_Enum_RequestType::AUTHORIZATION);
      }

    //add forward urls
    $transactionForwardingUrlsPaymentMethod = '';

    foreach ($config as $configKey => $configValue)
    {
      if(strpos($configKey, 'trans') === 0 && !empty($configValue)) 
      {
        $transactionForwardingUrlsPaymentMethod = $transactionForwardingUrlsPaymentMethod . ';' . $configValue;
      }
    }

    $transactionForwardingUrlsPaymentMethod = urlencode(ltrim($transactionForwardingUrlsPaymentMethod, ';'));
    $this->transactionForwardingUrls[$paymentMethod['name']] = $transactionForwardingUrlsPaymentMethod;
      
      return $paymentDto;
    }
    
    protected function getCountriesFromPaymentMethod(array $countries)
    {
      if(empty($countries))
      {
        return 'all';
      }
      $countryString = '';
      foreach ($countries as $country)
      {
        $countryString = $countryString . $country['iso'] . ',';
      }
      
      return rtrim($countryString, ",");
    }
    
    protected function getFinancingType($paymentHelper, $paymentName)
    {
       if($paymentHelper->isPayoneBillsafe($paymentName))
       {
           return Payone_Api_Enum_FinancingType::BSV;
       }
       if($paymentHelper->isPayoneKlarnaInstallment($paymentName))
       {
           return Payone_Api_Enum_FinancingType::KLS;
       }
       if($paymentHelper->isPayoneKlarna($paymentName))
       {
           return Payone_Api_Enum_FinancingType::KLV;
       }
    }
    
    protected function generateFileHash()
    {
      $path = __DIR__ . '/../..';
      return $this->getMd5FromDirectory($path);
    }
    
    protected function getMd5FromDirectory($dir)
    {
      if (!is_dir($dir))
      {
        return false;
      }
    
      $filemd5s = array();
      $folder = dir($dir);

      while (false !== ($entry = $folder->read()))
      {
        if ($entry != '.' && $entry != '..')
          {
             if (is_dir($dir.'/'.$entry))
             {
               $filemd5s[] = $this->getMd5FromDirectory($dir.'/'.$entry);
             }
             else
             {
               $filemd5s[] = md5_file($dir.'/'.$entry);
             }
          }
       }
    $folder->close();
    return md5(implode('', $filemd5s));
    }
    
    /**
     * retrieve global creditcard config options
     * 
     * @return array
     */
    protected function getGlobalCreditcardConfig()
    {
        $config = array();
        
        $sql = 'SELECT * FROM s_plugin_mopt_payone_creditcard_config';
        $configData = Shopware()->Db()->fetchAll($sql);
        
        if (!$configData) {
            $configData = array('integrationType' => 'not set');
        }elseif($configData[0]['integration_type'] === '0') {
            $configData['integrationType'] = 'hosted IFrame';
        } else {
            $configData['integrationType'] = 'AJAX';
        }

        $configData['jsonConfig'] = json_encode($configData);
        
        $config['integrationType'] = $configData['integrationType'];
        $config['jsonConfig'] = $configData['jsonConfig'];
        
        return $config;
    }
    
}
