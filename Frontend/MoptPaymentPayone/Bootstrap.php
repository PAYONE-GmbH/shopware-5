<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class configures:
 * installment, uninstallment, updates, hooks, events, payment methods
 *
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone Payment Plugin for Shopware 5
 * @subpackage      Installer
 * @copyright       Copyright (c) 2016 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Stefan Müller <stefan.mueller@fatchip.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.fatchip.com
 */

// needed for CSRF Protection compatibility SW versions < 5.2
require_once __DIR__ . '/Components/CSRFWhitelistAware.php';

use \Doctrine\ORM\Tools\ToolsException;

class Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
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
    public function afterInit()
    {
        $this->registerCustomModels();
        $this->get('Loader')->registerNamespace('Shopware\\Plugins\\MoptPaymentPayone', $this->Path());
        $this->get('Loader')->registerNamespace('Payone', $this->Path() . 'Components/Payone/');
        $this->get('Snippets')->addConfigDir($this->Path() . 'Snippets/');
        $this->get('Loader')->registerNamespace('Mopt', $this->Path() . 'Components/Classes/');
    }

    /**
     * perform all neccessary install tasks
     *
     * @return array
     */
    public function install()
    {
        $this->registerEvents();
        $this->createPayments();
        $this->createDatabase();
        $this->addAttributes();
        $this->createMenu();
        $this->removePayment('mopt_payone__fin_klarna_installment');

        return array('success' => true, 'invalidateCache' => array('backend', 'proxy', 'theme'));
    }

    /**
     * perform all neccessary uninstall tasks
     *
     * @return boolean
     */
    public function uninstall($deleteModels = false, $removeAttributes = false)
    {
        if ($deleteModels) {
            $this->deleteModels();
        }

        if ($removeAttributes) {
            $this->removeAttributes();
        }

        return true;
    }

    /**
     * delete Custom Models
     *
     *
     */
    protected function deleteModels()
    {
        $em = $this->Application()->Models();
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $tool->dropSchema(array(
            $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog')
        ));
        $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog')));
        $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig')));
        $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal')));
        $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig')));
        $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay')));
        $tool->dropSchema(array($em->getClassMetadata('Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay')));
    }

    /**
     * delete payone custom attribute extensions
     *
     *
     */
    protected function removeAttributes()
    {
        $prefix = 'mopt_payone';

        $tables = $this->getInstallHelper()->moptAttributeExtensionsArray($this->getId());

        /** @var \Shopware\Bundle\AttributeBundle\Service\CrudService $attributeService */
        $attributeService = $this->assertMinimumVersion('5.2') ?
            Shopware()->Container()->get('shopware_attribute.crud_service') : null;

        foreach ($tables as $table => $attributes) {
            foreach ($attributes as $attribute => $options) {
                if ($this->assertMinimumVersion('5.2')) {
                    try {
                        $attributeService->delete($table, $prefix . '_' . $attribute);
                    } catch (\Exception $e) {
                        continue; // if table or column does not exist
                    }
                } else {
                    try {
                        /** @noinspection PhpDeprecationInspection */
                        Shopware()->Models()->removeAttribute($table, $prefix, $attribute);
                    } catch (\InvalidArgumentException $e) {
                        continue; // if table or column does not exist
                    }
                }
            }
        }

        Shopware()->Models()->generateAttributeModels(array_keys($tables));
    }

    /**
     * Remove payment instance
     *
     * @param string $paymentName
     *
     */
    public function removePayment($paymentName)
    {
        $payment = $this->Payments()->findOneBy(
            array(
                'name' => $paymentName
            )
        );
        if ($payment === null) {
            // do nothing

        } else {
            Shopware()->Models()->remove($payment);
            Shopware()->Models()->flush();
        }
    }

    /**
     * update plugin, check previous versions
     *
     * @param type $oldVersion
     */
    public function update($oldVersion)
    {
        //extra handling for early beta version
        if (strpos($oldVersion, '0.0.') === 0) {
            $this->uninstall();
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
    public function enable()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function disable()
    {
        return true;
    }

    public function getCapabilities()
    {
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
    public function getInfo()
    {
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
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['label']['de'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getSolutionName()
    {
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
    protected function registerEvents()
    {
        $this->subscribeEvent('Enlight_Controller_Front_DispatchLoopStartup', 'onDispatchLoopStartup');
        // Lightweight Backend Controller
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_FcPayone',
            'onGetBackendController'
        );
        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Javascript',
            'addJsFiles'
        );
    }

    public function addJsFiles(Enlight_Event_EventArgs $args)
    {
        $jsFiles = [
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_checkout.js',
            $this->Path() . 'Views/frontend/_resources/javascript/client_api.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_payment.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_account.js',
        ];
        return new Doctrine\Common\Collections\ArrayCollection($jsFiles);
    }

    /**
     * register all subscriber classes for dynamic event subscription without plugin reinstallation
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onDispatchLoopStartup(Enlight_Event_EventArgs $args)
    {
        $container = Shopware()->Container();

        $subscribers = array(
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\PayoneResource(),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Paymentfilter($container),
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
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\BackendOrder($container)
        );
        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }

    /**
     * create payment methods
     */
    protected function createPayments()
    {
        $fcPayonePaymentMethods = $this->getInstallHelper()->mopt_payone__getPaymentMethods();

        foreach ($fcPayonePaymentMethods as $paymentMethod) {
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
    protected function createDatabase()
    {
        $em = $this->Application()->Models();
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        $this->getInstallHelper()->moptCreatePaymentDataTable();
        $this->getInstallHelper()->moptInsertDocumentsExtensionIntoDatabaseIfNotExist();

        // payone config sepa extension
        if (!$this->getInstallHelper()->moptPayoneConfigExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigDataTable();
        }

        // payone config address check extension
        if (!$this->getInstallHelper()->moptPayoneConfigAddressCheckExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigAddressCheckDataTable();
        }

        // payone config klarna extension
        if (!$this->getInstallHelper()->moptPayoneConfigKlarnaExtensionExist()) {
            $this->getInstallHelper()->moptExtendConfigKlarnaDataTable();
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

        // config option for ELV showBic
        if (!$this->getInstallHelper()->fcPayoneConfigShowBicExtensionExist()) {
            $this->getInstallHelper()->fcExtendConfigShowBicDataTable();
        }

        // config option for SOFORT Überweisung
        if (!$this->getInstallHelper()->fcPayoneConfigShowSofortIbanBicExtensionExist()) {
            $this->getInstallHelper()->fcExtendConfigShowSofortIbanBicDataTable();
        }

        // config option for Ratepay installment mode
        if (!$this->getInstallHelper()->fcPayoneConfigRatepayInstallmentModeExtensionExist()) {
            $this->getInstallHelper()->fcExtendConfigRatepayInstallmentModeDataTable();
        }

        $this->getInstallHelper()->checkAndUpdateCreditcardConfigModel($this->getPayoneLogger());

        $this->getInstallHelper()->checkAndUpdateCreditcardConfigModelExtension();

        $this->getInstallHelper()->moptInsertEmptyConfigIfNotExists();

        $this->getInstallHelper()->checkAndUpdateCreditcardModelIframeExtension();

        $this->getInstallHelper()->checkAndUpdateConfigModelPayolutionInstallmentExtension();

        $this->getInstallHelper()->checkAndUpdateBoniversumConfigModelExtension();

        // adding transaction id in log table
        if (!$this->getInstallHelper()->payoneApiLogTransactionIdExist()) {
            $this->getInstallHelper()->extendPayoneApiLogTransactionId();
        }
    }

    /**
     * extend shpoware models with PAYONE specific attributes
     */
    protected function addAttributes()
    {
        $prefix = 'mopt_payone';

        $tables = $this->getInstallHelper()->moptAttributeExtensionsArray($this->getId());

        /** @var \Shopware\Bundle\AttributeBundle\Service\CrudService $attributeService */
        $attributeService = $this->assertMinimumVersion('5.2') ?
            Shopware()->Container()->get('shopware_attribute.crud_service') : null;

        foreach ($tables as $table => $attributes) {
            foreach ($attributes as $attribute => $options) {
                $type = is_array($options) ? $options[0] : $options;
                $data = is_array($options) ? $options[1] : [];
                if ($this->assertMinimumVersion('5.2')) {
                    $attributeService->update($table, $prefix . '_' . $attribute, $type, $data);
                } else {
                    $type = $this->getInstallHelper()->unifiedToSQL($type);
                    /** @noinspection PhpDeprecationInspection */
                    Shopware()->Models()->addAttribute($table, $prefix, $attribute, $type, true, null);
                }
            }
        }
        Shopware()->Models()->generateAttributeModels(array_keys($tables));

        // SW 5.2 Use Address Table instead of shipping and billing tables
        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.2.0', '>=')
        ) {

            $tables = $this->getInstallHelper()->moptAttributeExtensionsArray52();
            $attributeService = Shopware()->Container()->get('shopware_attribute.crud_service');

            foreach ($tables as $table => $attributes) {
                foreach ($attributes as $attribute => $options) {
                    $type = is_array($options) ? $options[0] : $options;
                    $data = is_array($options) ? $options[1] : [];
                    $attributeService->update($table, $prefix . '_' . $attribute, $type, $data);
                }
            }
            Shopware()->Models()->generateAttributeModels(array_keys($tables));
        }
    }

    /**
     * Create menu items to access configuration, logs and support page
     */
    protected function createMenu()
    {
        $configurationLabelName = $this->getInstallHelper()->moptGetConfigurationLabelName();

        $labelPayment = array('label' => 'Zahlungen');
        $labelPayOne = array('label' => 'PAYONE');
        $labelKontollZentrum = array('label' => 'PAYONE Kontrollzentrum');

        // Lightweight Backend Controller
        $ret = $this->Menu()->findOneBy($labelKontollZentrum);
        if (!$ret) {
            $this->createMenuItem(
                array(
                'label' => 'PAYONE Kontrollzentrum',
                'onclick' => 'Shopware.ModuleManager.createSimplifiedModule("FcPayone", { "title": "PAYONE Kontrollzentrum" })',
                'class' => 'payoneicon',
                'active' => 1,
                'parent' => $this->Menu()->findOneBy($labelPayment),
                )
            );
        }

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
            'label' => 'Payone Ratepay',
            'controller' => 'MoptPayoneRatepay',
            'action' => 'Index',
            'class' => 'sprite-locale',
            'active' => 1,
            'parent' => $item,
        ));
        $this->createMenuItem(array(
            'label' => 'Payone Amazon Pay',
            'controller' => 'MoptPayoneAmazonPay',
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
    protected function checkAndDeleteOldLogs()
    {
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
    protected function getInstallHelper()
    {
        if (is_null($this->moptPayoneInstallHelper)) {
            $this->moptPayoneInstallHelper = new \Mopt_PayoneInstallHelper();
        }

        return $this->moptPayoneInstallHelper;
    }

    protected function getPayoneLogger()
    {
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
     * Event listener function of the Enlight_Controller_Dispatcher_ControllerPath_Backend_FcPayone
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

    /**
     * This Method can be used to capture orderpositions from your own plugins
     *
     * <b>Example: Method implementation to capture orderpositions</b>
     * <pre>
     * public function myOrderCapture()
     *{
     *  $moptPaymentPlugin = $this->get('plugins')->Frontend()->MoptPaymentPayone();
     *  // orderDetail is an array of orderDetail id's and amounts to capture
     *  // you can get these values from the table s_order_details
     *  // all orderDetail id's have to belong to a single order
     *  // also note that the amount can be lower than the full position amount
     *  // in this case you can repeat partial captures until you set $finalize to true
     *  $orderDetails = [
     *  [
     *    'id' => 1,
     *    'amount' => 3.0
     *    ],
     *    [
     *    'id' => 2,
     *    'amount' => 5
     *    ],
     *  ];
     *  try {
     *    $return = $plugin->captureOrder($orderDetails, true, true);
     *  } catch (Exception $e){
     *    echo "Exception:" . $e->getMessage();
     *  }
     *}</pre>
     *
     * @param $orderDetailParams array of orderdetail id's and amounts to capture, see example above
     * @param bool $finalize set to true on your last capture, afterwards captures are no longer possible
     * @param bool $includeShipment set to true to include shipping costs in capture, set to false if shipping costs have their own order position
     * @return bool
     * @throws Exception
     */
    public function captureOrder($orderDetailParams, $finalize = false, $includeShipment = false)
    {

        $orderParams = array_combine(array_column($orderDetailParams, 'id'), array_column($orderDetailParams, 'amount'));

        try {
            $orderDetailId = key($orderParams);
            if (!$orderDetail = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail')->find($orderDetailId)) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('orderNotFound', 'BestellungsPosition nicht gefunden', true);
                throw new Exception($message);
            }

            if (!$order = $orderDetail->getOrder()) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('orderNotFound', 'Bestellung nicht gefunden', true);
                throw new Exception($message);
            }

            $payment = $order->getPayment();
            $paymentName = $payment->getName();

            //check if order was payment type payone
            if (strpos($paymentName, 'mopt_payone__') !== 0) {
                $message = 'Capture is only possible with payone payments';
                throw new Exception($message);
            }

            $config = Mopt_PayoneMain::getInstance()->getPayoneConfig($payment->getId());

            //fetch params
            $params = Mopt_PayoneMain::getInstance()->getParamBuilder()
                ->buildCustomOrderCapture($order, $orderParams, $finalize, $includeShipment);

            $invoicing = null;

            if ($config['submitBasket'] || Mopt_PayoneMain::getInstance()->getPaymentHelper()->isPayoneBillsafe($paymentName)) {
                $invoicing = Mopt_PayoneMain::getInstance()->getParamBuilder()
                    ->getInvoicingFromOrder($order, array_column($orderDetailParams, 'id'), $finalize, false, $includeShipment);
            }

            //call capture service
            $response = $this->callPayoneCaptureService($params, $invoicing);

            if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED) {
                //increase sequence
                $this->updateSequenceNumber($order, true);

                //mark / fill positions as captured
                $this->markPositionsAsCaptured($order, $orderDetailParams, $includeShipment);

                //extract and save clearing data
                $clearingData = Mopt_PayoneMain::getInstance()->getPaymentHelper()->extractClearingDataFromResponse($response);
                if ($clearingData) {
                    $this->saveClearingData($order, $clearingData);
                }

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * This Method can be used to refund orderpositions from your own plugins
     *
     * <b>Example: Method implementation to refund orderpositions</b>
     * <pre>
     * public function myOrderRefund()
     *{
     *  $moptPaymentPlugin = $this->get('plugins')->Frontend()->MoptPaymentPayone();
     *  // orderDetail is an array of orderDetail id's , amounts and item quantities to refund
     *  // you can get these values from the table s_order_details
     *  // all orderDetail id's have to belong to a single order
     *  // also note that the amount can be lower than the full position amount
     *  // in this case you can repeat partial refunds until you set $finalize to true
     *  $orderDetails = [
     *  [
     *    'id' => 1,
     *    'amount' => 3.0,
     *    'quantity' => 2
     *    ],
     *    [
     *    'id' => 2,
     *    'amount' => 5,
     *    'quantity' => '1'
     *    ],
     *  ];
     *  try {
     *    return $moptPaymentPlugin->refundOrder($orderDetails, true, true);
     *  } catch (Exception $e){
     *    echo "Exception:" . $e->getMessage();
     *  }
     *}</pre>
     *
     * @param $orderDetailParams array of orderdetail id's and amounts and item quantities to refund, see example above
     * @param bool $finalize set to true on your last refund, afterwards refunds are no longer possible
     * @param bool $includeShipment set to true to include shipping costs in refunds, set to false if shipping costs have their own order position
     * @return bool
     * @throws Exception
     */
    public function refundOrder($orderDetailParams, $finalize = false, $includeShipment = false)
    {
        $quantities= array_combine(array_column($orderDetailParams, 'id'), array_column($orderDetailParams, 'quantity'));
        $orderParams = array_combine(array_column($orderDetailParams, 'id'), array_column($orderDetailParams, 'amount'));

        try {
            $orderDetailId = key($orderParams);
            if (!$orderDetail = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail')->find($orderDetailId)) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                        ->get('orderNotFound', 'BestellungsPosition nicht gefunden', true);
                throw new Exception($message);
            }

            if (!$order = $orderDetail->getOrder()) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                        ->get('orderNotFound', 'Bestellung nicht gefunden', true);
                throw new Exception($message);
            }

            $payment = $order->getPayment();
            $paymentName = $payment->getName();

            //check if order was payment type was a payone payment
            if (strpos($paymentName, 'mopt_payone__') !== 0) {
                $message = 'Refund is only possible with payone payments';
                throw new Exception($message);
            }

            $config = Mopt_PayoneMain::getInstance()->getPayoneConfig($payment->getId());

            //fetch params
            $params = Mopt_PayoneMain::getInstance()->getParamBuilder()->buildCustomOrderDebit($order, $orderParams, $includeShipment);

            $invoicing = null;

            if ($config['submitBasket'] || Mopt_PayoneMain::getInstance()->getPaymentHelper()->isPayoneBillsafe($paymentName)) {
                $invoicing = Mopt_PayoneMain::getInstance()->getParamBuilder()
                        ->getInvoicingFromOrder($order, array_column($orderDetailParams, 'id'), $finalize, true, $includeShipment, $quantities);
            }
            //call capture service

            $response = $this->callPayoneRefundService($params, $invoicing);

            if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED) {
                //increase sequence
                $this->updateSequenceNumber($order, true);

                //mark / fill positions as captured
                $this->markPositionsAsDebited($order, $orderDetailParams, $includeShipment);

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function callPayoneCaptureService($params, $invoicing = null)
    {
        $service = Shopware()->Plugins()->Frontend()
            ->MoptPaymentPayone()->get('MoptPayoneBuilder')->buildServicePaymentCapture();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request = new Payone_Api_Request_Capture($params);

        if ($invoicing) {
            $request->setInvoicing($invoicing);
        }

        if ($params['payolution_b2b'] == true) {
            $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $request->setPaydata($paydata);
        }
        return $service->capture($request);
    }

    protected function callPayoneRefundService($params, $invoicing = null)
    {
        $service = Shopware()->Plugins()->Frontend()
            ->MoptPaymentPayone()->Application()->MoptPayoneBuilder()->buildServicePaymentDebit();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request = new Payone_Api_Request_Debit($params);

        if ($invoicing) {
            $request->setInvoicing($invoicing);
        }

        return $service->debit($request);
    }

    protected function updateSequenceNumber($order, $isAuth = false)
    {
        $attribute = Mopt_PayoneMain::getInstance()->getHelper()->getOrCreateAttribute($order);
        $newSeq = $attribute->getMoptPayoneSequencenumber() + 1;
        $attribute->setMoptPayoneSequencenumber($newSeq);
        if ($isAuth) {
            $attribute->setMoptPayoneIsAuthorized(true);
        }

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }

    protected function markPositionsAsCaptured($order, $orderDetailParams, $includeShipment = false)
    {

        $orderParams = array_combine(array_column($orderDetailParams, 'id'), array_column($orderDetailParams, 'amount'));
        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), array_column($orderDetailParams, 'id'))) {
                continue;
            }


            $attribute = Mopt_PayoneMain::getInstance()->getHelper()->getOrCreateAttribute($position);
            $amount = $orderParams[$position->getId()];
            $attribute->setMoptPayoneCaptured($amount + $attribute->getMoptPayoneCaptured());

            Shopware()->Models()->persist($attribute);
            Shopware()->Models()->flush();

            //check if shipping is included as position
            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            $orderAttribute = Mopt_PayoneMain::getInstance()->getHelper()->getOrCreateAttribute($order);
            $orderAttribute->setMoptPayoneShipCaptured($order->getInvoiceShipping());
            Shopware()->Models()->persist($orderAttribute);
            Shopware()->Models()->flush();
        }
    }

    protected function saveClearingData($order, $clearingData)
    {
        $attribute = Mopt_PayoneMain::getInstance()->getHelper()->getOrCreateAttribute($order);
        $attribute->setMoptPayoneClearingData(json_encode($clearingData));

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }

    protected function markPositionsAsDebited($order, $orderDetailParams, $includeShipment = false)
    {

        $orderParams = array_combine(array_column($orderDetailParams, 'id'), array_column($orderDetailParams, 'amount'));

        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), array_column($orderDetailParams, 'id'))) {
                continue;
            }

            $attribute = Mopt_PayoneMain::getInstance()->getHelper()->getOrCreateAttribute($position);
            $amount = $orderParams[$position->getId()];
            $attribute->setMoptPayoneDebit($amount + $attribute->getMoptPayoneDebit());

            Shopware()->Models()->persist($attribute);
            Shopware()->Models()->flush();

            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            $orderAttribute = Mopt_PayoneMain::getInstance()->getHelper()->getOrCreateAttribute($order);
            $orderAttribute->setMoptPayoneShipDebit($order->getInvoiceShipping());
            Shopware()->Models()->persist($orderAttribute);
            Shopware()->Models()->flush();
        }
    }
}
