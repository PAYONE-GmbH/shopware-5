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
use Shopware\Plugins\MoptPaymentPayone\Bootstrap\RiskRules;

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
        $this->updatePayments();
        $this->createDatabase();
        $this->addAttributes();
        $this->createMenu();
        $riskRules = new RiskRules();
        $riskRules->createRiskRules();
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
        if (version_compare($oldVersion, '3.8.3', '<')) {
            $this->getInstallHelper()->updatePayolutionAuthSettings();
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
        $this->subscribeEvent(
            'Shopware_Modules_Admin_Execute_Risk_Rule_sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS',
            'sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS'
        );
        $this->subscribeEvent(
            'Shopware_Modules_Admin_Execute_Risk_Rule_sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT',
            'sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT'
        );
    }

    public function addJsFiles(Enlight_Event_EventArgs $args)
    {
        $jsFiles = [
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_checkout.js',
            $this->Path() . 'Views/frontend/_resources/javascript/client_api.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_payment.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_account.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_shipping.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_amazonpay.js',
            $this->Path() . 'Views/frontend/_resources/javascript/fatchipBSPayoneMasterpass.js',
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
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Document($container, $this->Path()),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Payment($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\OrderNumber($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\EMail($container),
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
     * updates some specific payment methods
     */
    protected function updatePayments()
    {
        /** @var Shopware\Models\Payment\Payment $payment */
        $payment = $this->Payments()->findOneBy(
            array('name' => 'mopt_payone__acc_payone_safe_invoice')
        );
        if ($payment === null) {
            // do nothing

        } else {
            $payment->setTemplate('mopt_paymentmean_payone_safe_invoice.tpl');
            Shopware()->Models()->persist($payment);
            Shopware()->Models()->flush();
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

        $cacheManager = Shopware()->Container()->get('shopware.cache_manager');

        $cacheManager->clearProxyCache();

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

        $this->getInstallHelper()->checkAndUpdateFailedStatusConfigModelExtension();

        $this->getInstallHelper()->checkAndInsertFailedStatusEmailTemplate();

        $this->getInstallHelper()->checkAndInsertDelayedStatusEmailTemplate();


        // adding transaction id in log table
        if (!$this->getInstallHelper()->payoneApiLogTransactionIdExist()) {
            $this->getInstallHelper()->extendPayoneApiLogTransactionId();
        }

        $this->getInstallHelper()->checkAndUpdateConfigModelPaydirektOvercaptureExtension();

        $this->getInstallHelper()->checkAndUpdateConsumerscoreExtension();

        $this->getInstallHelper()->checkAndUpdateSendOrderNumberAsReferenceExtension();

        $this->getInstallHelper()->checkAndUpdateTransLoggingExtension();
    }

    /**
     * extend shpoware models with PAYONE specific attributes
     */
    protected function addAttributes()
    {
        $prefix = 'mopt_payone';
        $tables = $this->assertMinimumVersion('5.2') ? $this->getInstallHelper()->moptAttributeExtensionsArray52($this->getId()) : $this->getInstallHelper()->moptAttributeExtensionsArray($this->getId());

        if (version_compare(\Shopware::VERSION, '5.2.0', '<') && \Shopware::VERSION !== '___VERSION___') {
            foreach ($tables as $table => $attributes) {
                foreach ($attributes as $attribute => $options) {
                    $type = is_array($options) ? $options[0] : $options;
                    $type = $this->getInstallHelper()->unifiedToSQL($type);
                    /** @noinspection PhpDeprecationInspection */
                    Shopware()->Models()->addAttribute($table, $prefix, $attribute, $type, true, null);
                }
            }
        }

        if ($this->assertMinimumVersion('5.2')) {
            $attributeService = Shopware()->Container()->get('shopware_attribute.crud_service');
            foreach ($tables as $table => $attributes) {
                foreach ($attributes as $attribute => $options) {
                    $type = is_array($options) ? $options[0] : $options;
                    $data = is_array($options) ? $options[1] : [];
                    $attributeService->update($table, $prefix . '_' . $attribute, $type, $data);
                }
            }
        }
        Shopware()->Models()->generateAttributeModels(array_keys($tables));
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
     * @deprecated Use Shopware()->Container()->get('payone_service')->captureOrder() instead
     *
     * @param array $orderDetailParams array of order detail ID's and amounts to capture
     * @param bool $finalize true marks the last capture operation; afterwards captures are no longer possible
     * @param bool $includeShipment true to include shipping costs; false if they are an extra order position
     * @return bool true if the request has been approved
     * @throws Exception
     */
    public function captureOrder($orderDetailParams, $finalize = false, $includeShipment = false)
    {
        return $this->get('payone_service')->captureOrder($orderDetailParams, $finalize, $includeShipment);
    }

    /**
     * @deprecated Use Shopware()->Container()->get('payone_service')->refundOrder() instead
     *
     * @param array $orderDetailParams array of order detail ID's, amounts and quantities to refund
     * @param bool $finalize true marks the last refund operation; afterwards refunds are no longer possible
     * @param bool $includeShipment true to include shipping costs; false if they are an extra order position
     * @return bool true if the request has been approved
     * @throws Exception
     */
    public function refundOrder($orderDetailParams, $finalize = false, $includeShipment = false)
    {
        return $this->get('payone_service')->refundOrder($orderDetailParams, $finalize, $includeShipment);
    }

    /**
     * this method is only used to prevent a methodNotFound exception when our custom risk rule registration in onDispatchLoopStartup()
     * is too late.
     * The only known case where this happens is when changing currencies in shops with multiple currencies enabled
     *
     * @param Enlight_Event_EventArgs $args
     * @return false
     */
    public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS(Enlight_Event_EventArgs $args)
    {
        $args->setReturn(false);
        return false;
    }

    /**
     * this method is only used to prevent a methodNotFound exception when our custom risk rule registration in onDispatchLoopStartup()
     * is too late.
     * The only known case where this happens is when changing currencies in shops with multiple currencies enabled
     *
     * @param Enlight_Event_EventArgs $args
     * @return false
     */
    public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT(Enlight_Event_EventArgs $args)
    {
        $args->setReturn(false);
        return false;
    }

}
