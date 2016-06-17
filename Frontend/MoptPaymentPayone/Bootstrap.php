<?php

/**
 * this class configures:
 * installment, uninstallment, updates, hooks, events, payment methods
 */
class Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap extends Shopware_Components_Plugin_Bootstrap {

    /**
     * PayoneHelper
     * @var Mopt_PayoneInstallHelper
     */
    protected $moptPayoneInstallHelper = null;

    /**
     * PayoneHelper
     * @var Monolog\Logger
     */
    protected $moptPayoneLogger = null;

    /**
     * registers the custom plugin models and plugin namespaces
     */
    public function afterInit() {
        $this->registerCustomModels();
        $this->get('Loader')->registerNamespace('Shopware\\Plugins\\MoptPaymentPayone', $this->Path());
        $this->get('Loader')->registerNamespace('Payone', $this->Path() . 'Components/Payone/');
        $this->get('Snippets')->addConfigDir($this->Path() . 'Snippets/');
        $this->get('Loader')->registerNamespace('Mopt', $this->Path() . 'Components/Classes/');
    }

    /**
     * perform all neccessary install tasks
     *
     * @return boolean 
     */
    public function install() {
        $this->registerEvents();
        $this->createPayments();
        $this->createDatabase();
        $this->addAttributes();
        $this->createMenu();

        return array('success' => true, 'invalidateCache' => array('backend', 'proxy'));
    }

    /**
     * perform all neccessary uninstall tasks
     *
     * @return boolean 
     */
    public function uninstall($deleteModels = false, $removeAttributes = false) {
        if ($deleteModels) {
            $em = $this->Application()->Models();
            $platform = $em->getConnection()->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('enum', 'string');
            $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

            $tool->dropSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog')
            ));
            $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog')));
            $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig')));
        }

        if ($removeAttributes) {
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_result');
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_date');
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_color');
            Shopware()->Models()->removeAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_value');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_result');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_date');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_result');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_date');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_color');
            Shopware()->Models()->removeAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_value');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_result');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_date');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_color');
            Shopware()->Models()->removeAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_value');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'txid');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'status');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'sequencenumber');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'is_authorized');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'is_finally_captured');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'clearing_data', 'text');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'payment_status');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'shipment_date');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'captured');
            Shopware()->Models()->removeAttribute('s_order_details_attributes', 'mopt_payone', 'debit');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'payolution_workorder_id');
            Shopware()->Models()->removeAttribute('s_order_attributes', 'mopt_payone', 'payolution_clearing_reference');            

            Shopware()->Models()->generateAttributeModels(
                    array(
                        's_user_attributes',
                        's_core_paymentmeans_attributes',
                        's_user_billingaddress_attributes',
                        's_user_shippingaddress_attributes',
                        's_order_attributes',
                        's_order_details_attributes',
                    )
            );
        }

        return true;
    }

    /**
     * update plugin, check previous versions
     * 
     * @param type $oldVersion 
     */
    public function update($oldVersion) {
        //extra handling for early beta version
        if (strpos($oldVersion, '0.0.') === 0) {
            $this->uninstall(true);
            $this->install();

            return true;
        }

        $this->install();
        $this->checkAndDeleteOldLogs();

        return true;
    }

    /**
     * @return boolean 
     */
    public function enable() {
        return true;
    }

    /**
     * @return boolean 
     */
    public function disable() {
        return true;
    }

    public function getCapabilities() {
        return array(
            'install' => true,
            'update' => true,
            'enable' => true
        );
    }

    /**
     * Returns the informations of plugin as array.
     *
     * @return array
     */
    public function getInfo() {
        $logo = base64_encode(file_get_contents(dirname(__FILE__) . '/logo.png'));
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
        return array(
            'label' => $this->getLabel(),
            'author' => $info['author'],
            'copyright' => $info['copyright'],
            'link' => $info['link'],
            'support' => $info['support'],
            'version' => $this->getVersion(),
            'description' => '<p><img src="data:image/png;base64,' . $logo . '" /></p> '
            . file_get_contents(__DIR__ . '/description.txt'),
            'solution_name' => $this->getSolutionName()
        );
    }

    /**
     * Returns the version of plugin as string.
     *
     * @return string
     */
    public function getVersion() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['label']['de'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getSolutionName() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['solution_name'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     * register for several events to extend shop functions
     */
    protected function registerEvents() {
        $this->subscribeEvent('Enlight_Controller_Front_DispatchLoopStartup',
            'onDispatchLoopStartup');
        // Lightweight Backend Controller 
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_FcPayone',
            'onGetBackendController');
    }

    /**
     * register all subscriber classes for dynamic event subscription without plugin reinstallation
     * 
     * @param Enlight_Event_EventArgs $args
     */
    public function onDispatchLoopStartup(Enlight_Event_EventArgs $args) {
        $container = Shopware()->Container();

        $subscribers = array(
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Resource(),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\ControllerPath($this->Path()),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\AddressCheck($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\EMail($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Document($container, $this->Path()),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Payment($container),
            // Frontend
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\FrontendCheckout($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\FrontendAccount($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\FrontendPostDispatch($container, $this->Path()),
            // Backend
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\BackendPayment($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\BackendRiskManagement($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\BackendOrder($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\BackendOrder($container)
        );
        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }

    /**
     * create payment methods
     */
    protected function createPayments() {
        $mopt_payone__paymentMethods = $this->getInstallHelper()->mopt_payone__getPaymentMethods();

        foreach ($mopt_payone__paymentMethods as $paymentMethod) {
            if ($this->Payments()->findOneBy(array('name' => $paymentMethod['name']))) {
                continue;
            }

            $payment = array(
                'name' => $paymentMethod['name'],
                'description' => $paymentMethod['description'],
                'action' => 'mopt_payment_payone',
                'active' => 0,
                'position' => $paymentMethod['position'],
                'additionalDescription' => 'Pay save and secured through our payment service.',
            );

            if (!is_null($paymentMethod['template'])) {
                $payment['template'] = $paymentMethod['template'];
            }
            if (isset($paymentMethod['additionalDescription']) && !is_null($paymentMethod['additionalDescription'])) {
                $payment['additionalDescription'] = $paymentMethod['additionalDescription'];
            }
            $this->createPayment($payment);
        }
    }

    /**
     * create tables, add coloumns
     */
    protected function createDatabase() {
        $em = $this->Application()->Models();
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig'),
            ));
        } catch (\Doctrine\ORM\Tools\ToolsException $e) {
            // ignore
        }

        $this->getInstallHelper()->moptCreatePaymentDataTable();
        $this->getInstallHelper()->moptInsertDocumentsExtensionIntoDatabaseIfNotExist();

        // payone config sepa extension
        if (!$this->getInstallHelper()->moptPayoneConfigExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigDataTable();
        }

        // payone config klarna extension
        if (!$this->getInstallHelper()->moptPayoneConfigKlarnaExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigKlarnaDataTable();
        }

        // payone config klarna installment extension
        if (!$this->getInstallHelper()->moptPayoneConfigKlarnaInstallmentExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigKlarnaInstallmentDataTable();
        }

        // payone save terms acceptance extension
        if (!$this->getInstallHelper()->moptPayoneConfigsaveTermsExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigSaveTermsDataTable();
        }

        // payone paypal ecs extension
        if (!$this->getInstallHelper()->moptPayoneConfigPaypalEcsExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigPaypalEcsDataTable();
        }

        // payone creditcard min days and riskcheck country config extension
        if (!$this->getInstallHelper()->moptPayoneConfigRiskCountryExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigCreditcardMinValidDays();
            $this->getInstallHelper()->moptExtendConfigAddressCheckCountries();
        }

        // check if transaction log model is updated
        if (!$this->getInstallHelper()->isTransactionLogModelUpdated()) {
            $this->getInstallHelper()->updateTransactionLogModel();
        }

        // payone config payolution installment extension
        if (!$this->getInstallHelper()->moptPayoneConfigPayolutionExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigPayolutionDataTable();
        }
        
        $this->getInstallHelper()->checkAndUpdateCreditcardConfigModel($this->getPayoneLogger());

        $this->getInstallHelper()->checkAndUpdateCreditcardConfigModelExtension();
        
        $this->getInstallHelper()->moptInsertEmptyConfigIfNotExists();
    }

    /**
     * extend shpoware models with PAYONE specific attributes 
     */
    protected function addAttributes() {
        $models = array();

        if (!$this->getInstallHelper()->moptUserAttributesExist()) {
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
            Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);

            $models[] = 's_user_attributes';
        }

        if (!$this->getInstallHelper()->moptBillingAddressAttributesExist()) {
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
            Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);

            $models[] = 's_user_billingaddress_attributes';
        }

        if (!$this->getInstallHelper()->moptShippingAddressAttributesExist()) {
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_result', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
            Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);

            $models[] = 's_user_shippingaddress_attributes';
        }

        if (!$this->getInstallHelper()->moptOrderAttributesExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'txid', 'integer', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'status', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'sequencenumber', 'int(11)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'is_authorized', 'TINYINT(1)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'is_finally_captured', 'TINYINT(1)', true, null);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'clearing_data', 'text', true, null);
            
            
            $models[] = 's_order_attributes';
        }

        if (!$this->getInstallHelper()->moptOrderDetailsAttributesExist()) {
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'payment_status', 'VARCHAR(100)', true, null);
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'shipment_date', 'date', true, null);
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'captured', 'double', true, null);
            Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'debit', 'double', true, null);

            $models[] = 's_order_details_attributes';
        }

        if (!empty($models)) {
            Shopware()->Models()->generateAttributeModels($models);
        }

        // 2nd order extension since 2.1.4 - save shipping cost with order
        if (!$this->getInstallHelper()->moptOrderAttributesShippingCostsExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'ship_captured', 'double', true, 0.00);
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'ship_debit', 'double', true, 0.00);

            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }

        // 3rd order extension since 2.3.0 - save payment data for abo commerce support
        if (!$this->getInstallHelper()->moptOrderAttributesPaymentDataExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'payment_data', 'text', true, null);

            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }

        // 4th order extension since 2.5.2 - save order hash and payment reference
        if (!$this->getInstallHelper()->moptOrderAttributesOrderHashExist()) {
            $this->getInstallHelper()->moptExtendOrderAttributes();
        }
        
        // 5th order extension since 3.3.8 - Payolution Payment Order extensions
        
        if (!$this->getInstallHelper()->moptPayolutionWorkOrderIdAttributeExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'payolution_workorder_id', 'VARCHAR(64)', true, null);
            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }        
        
        if (!$this->getInstallHelper()->moptPayolutionClearingReferenceAttributeExist()) {
            Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'payolution_clearing_reference', 'VARCHAR(64)', true, null);
            Shopware()->Models()->generateAttributeModels(array('s_order_attributes'));
        }        
    }

    /**
     * Create menu items to access configuration, logs and support page
     */
    protected function createMenu() {
        $configurationLabelName = $this->getInstallHelper()->moptGetConfigurationLabelName();

        $labelPayment = array('label' => 'Zahlungen');
        $labelPayOne = array('label' => 'PAYONE'); 
        
        // Lightweight Backend Controller 
        $this->createMenuItem(
            array(
                'label' => 'PAYONE Kontrollzentrum',
                'onclick' => 'Shopware.ModuleManager.createSimplifiedModule("FcPayone", { "title": "PAYONE Kontrollzentrum" })',
                'class' => 'payoneicon',
                'active' => 1,
                'parent' => $this->Menu()->findOneBy($labelPayment),
            )
        );

        if ($this->Menu()->findOneBy($labelPayOne)) {
            return;
        }

        $parent = $this->Menu()->findOneBy($labelPayment);
        $item = $this->createMenuItem(array(
            'label' => 'PAYONE',
            'class' => 'payoneicon',
            'active' => 1,
            'parent' => $parent,
        ));

        $this->createMenuItem(array(
            'label' => $configurationLabelName,
            'controller' => 'MoptConfigPayone',
            'action' => 'Index',
            'class' => 'sprite-wrench-screwdriver',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Payone PayPal',
            'controller' => 'MoptPayonePaypal',
            'action' => 'Index',
            'class' => 'sprite-locale',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Payone Kreditkartenkonfiguration',
            'controller' => 'MoptPayoneCreditcardConfig',
            'action' => 'Index',
            'class' => 'sprite-wrench-screwdriver',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'API-Log',
            'controller' => 'MoptApilogPayone',
            'action' => 'Index',
            'class' => 'sprite-cards-stack',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Transaktionsstatus-Log',
            'controller' => 'MoptPayoneTransactionLog',
            'action' => 'Index',
            'class' => 'sprite-cards-stack',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Hilfe & Support',
            'controller' => 'MoptSupportPayone',
            'action' => 'Index',
            'class' => 'sprite-lifebuoy',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Konfigurationsexport',
            'controller' => 'MoptExportPayone',
            'action' => 'Index',
            'class' => 'sprite-script-export',
            'active' => 1,
            'parent' => $item,
        ));
    }

    /**
     * delete old logfiles
     */
    protected function checkAndDeleteOldLogs() {
        $path = $this->Path() . '../../../../../../';

        foreach (glob($path . 'payone_*.lo*') as $file) {
            if (file_exists($file)) {
                file_put_contents($file, '');
                unlink($file);
            }
        }
    }

    /**
     * internal getter for install helper
     * 
     * @return \Mopt_PayoneInstallHelper
     */
    protected function getInstallHelper() {
        if (is_null($this->moptPayoneInstallHelper)) {
            $this->moptPayoneInstallHelper = new \Mopt_PayoneInstallHelper();
        }

        return $this->moptPayoneInstallHelper;
    }

    protected function getPayoneLogger() {
        if (!$this->moptPayoneLogger) {
            $this->moptPayoneLogger = new Monolog\Logger('moptPayone');
            $streamHandler = new Monolog\Handler\StreamHandler(Shopware()->Application()->Kernel()->getLogDir()
                    . '/moptPayone.log', Monolog\Logger::ERROR);
            $this->moptPayoneLogger->pushHandler($streamHandler);
        }

        return $this->moptPayoneLogger;
    }
    
    /**
     * Returns the path to the controller.
     *
     * Event listener function of the Enlight_Controller_Dispatcher_ControllerPath_Backend_SwagFavorites
     * event.
     * Fired if an request will be root to the own Favorites backend controller.
     *
     * @return string
     */
    public function onGetBackendController()
    {
        $this->get('template')->addTemplateDir($this->Path() . 'Views/');

        return $this->Path() . 'Controllers/Backend/FcPayone.php';
    }
}
