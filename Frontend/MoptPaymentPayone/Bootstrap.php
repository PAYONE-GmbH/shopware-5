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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Tools\ToolsException;
use Shopware\Models\Attribute\Configuration;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Payment\Repository as PaymentRepository;
use Shopware\Models\Plugin\Plugin;
use Shopware\Plugins\MoptPaymentPayone\Bootstrap\RiskRules;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

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
     * @throws ReflectionException
     */
    public function afterInit()
    {
        $this->registerCustomModels();
        $this->get('loader')->registerNamespace('Shopware\\Plugins\\MoptPaymentPayone', $this->Path());
        $this->get('loader')->registerNamespace('Payone', $this->Path() . 'Components/Payone/');
        $this->get('snippets')->addConfigDir($this->Path() . 'Snippets/');
        $this->get('loader')->registerNamespace('Mopt', $this->Path() . 'Components/Classes/');

        if (version_compare(self::getShopwareVersion(), '5.6.0', '<') || !$this->isPluginActive()) {
            return;
        }

        $proxies = array('Shopware_Proxies_sAdminProxy', 'Shopware_Proxies_sBasketProxy');
        $this->revalidateCoreProxies($proxies);
    }

    /**
     * switching language will immediately produce core proxies not containing hooked methods. as rework we will revalidate those.
     * proxies will be automatically regenerated if needed
     *
     * @param array $proxies
     * @throws ReflectionException
     */
    public function revalidateCoreProxies($proxies = []) {
        foreach($proxies as $proxy) {
            if(class_exists($proxy)) {
                $hooks = $proxy::getHookMethods();

                if(is_array($hooks) && count($hooks) > 0) {
                    continue;
                }

                $proxy = new \ReflectionClass($proxy);
                $proxyFile = $proxy->getFileName();

                unlink($proxyFile);
            }
        }
    }

    public static function getShopwareVersion() {
        $currentVersion = '';

        if(defined('\Shopware::VERSION')) {
            $currentVersion = \Shopware::VERSION;
        }

        //get old composer versions
        if($currentVersion === '___VERSION___' && class_exists('ShopwareVersion') && class_exists('PackageVersions\Versions')) {
            $currentVersion = \ShopwareVersion::parseVersion(
                \PackageVersions\Versions::getVersion('shopware/shopware')
            )['version'];
        }

        if(!$currentVersion || $currentVersion === '___VERSION___') {
            $currentVersion = Shopware()->Container()->getParameter('shopware.release.version');
        }

        return $currentVersion;
    }

    public function isPluginActive() {
        $pluginName = $this->getName();

        /** @var Plugin $plugin */
        $plugin = Shopware()->Models()->getRepository(Plugin::class)->findOneBy(['name' => $pluginName]);

        return $plugin && $plugin->getActive();
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
        // remove all old rules
        $riskRules->removeRiskRules('mopt_payone__fin_payone_secured_invoice');
        $riskRules->removeRiskRules('mopt_payone__fin_payone_secured_installment');
        $riskRules->removeRiskRules('mopt_payone__fin_payone_secured_directdebit');
        $riskRules->createRiskRules();
        $riskRules->removeRiskRules('mopt_payone__fin_paypal_installment');

        $this->removePayment('mopt_payone__fin_klarna_installment');
        $this->removePayment('mopt_payone__ewallet_masterpass');
        $this->removePayment('mopt_payone__fin_billsafe');
        $this->removePayment('mopt_payone__fin_paypal_installment');
        $this->removePayment('mopt_payone__ewallet_paydirekt_express');
        $this->removePayment('mopt_payone__ewallet_paydirekt');
        $this->removePayment('mopt_payone__ibt_giropay');
        $this->removePayment('mopt_payone__csh_barzahlen');
        $this->removePayment('mopt_payone__ibt_trustly');
        $this->removePayment('mopt_payone__cc_maestro_international');

        // Only relevant for update, not for reinstall
        if (!$this->doesCronJobExist('PayoneTransactionForward') && !$this->doesCronJobExist('Shopware_CronJob_PayoneTransactionForward')) {
            $this->createCronJob('Payone Transaktionsweiterleitung', 'PayoneTransactionForward', 60);
        }

        return array('success' => true, 'invalidateCache' => array('backend', 'proxy', 'theme'));
    }

    private function doesCronJobExist($cronJobAction)
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $result = $connection->fetchAll("SELECT * FROM `s_crontab` WHERE `action` = ?",[$cronJobAction]);
        return count($result) > 0;
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

        $this->getInstallHelper()->removeDocumentTemplates();

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
        $attributeService = Shopware()->Container()->get('shopware_attribute.crud_service');

        foreach ($tables as $table => $attributes) {
            foreach ($attributes as $attribute => $options) {
                try {
                    $attributeService->delete($table, $prefix . '_' . $attribute);
                } catch (\Exception $e) {
                    continue; // if table or column does not exist
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
            'CookieCollector_Collect_Cookies',
            'registerAmazonCookie'
        );
        $this->subscribeEvent(
            'Shopware_Modules_Admin_Execute_Risk_Rule_sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT',
            'sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT'
        );
        $this->subscribeEvent('Shopware_CronJob_PayoneTransactionForward', 'onRunCronJob');
    }

    /**
     * @param Enlight_Components_Cron_EventArgs $job
     */
    public function onRunCronJob(Enlight_Components_Cron_EventArgs $job)
    {
        $logPath = Shopware()->Container()->get('kernel')->getLogDir();
        $logFile = $logPath . '/MoptPaymentPayone_transaction_forward_cronjob.log';

        $queueWorker = new Mopt_PayoneTransactionForwardingQueueWorker();
        $queueWorker->processQueue();

        $rfh = new RotatingFileHandler($logFile, 14);
        $rotatingLogger = new Logger('MoptPaymentPayone');
        $rotatingLogger->pushHandler($rfh);
        $rotatingLogger->info(date('Y-m-d H:i:s > ') . 'Payone transactionqueue cronjob started.');
        $rotatingLogger->info(date('Y-m-d H:i:s > ') . 'Payone transactionqueue cronjob stopped.');
    }

    public function registerAmazonCookie()
    {
        if(class_exists('Shopware\\Bundle\\CookieBundle\\CookieCollection')) {
            /** @var PaymentRepository $paymentRepository */
            $paymentRepository = Shopware()->Models()->getRepository(Payment::class);

            /** @var Payment $payment */
            $payment = $paymentRepository->findOneBy(['name' => 'mopt_payone__ewallet_amazon_pay']);
            if (!($payment and $payment->getActive())) {
                return;
            }

            $collection = new \Shopware\Bundle\CookieBundle\CookieCollection();
            $collection->add(new \Shopware\Bundle\CookieBundle\Structs\CookieStruct(
                'moptamazon',
                '/^amazon/',
                'Amazon Payment Cookies',
                \Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct::TECHNICAL
            ));

            return $collection;
        }

        return;
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
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_payolution.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_klarna_shipping_payment.js',
            $this->Path() . 'Views/frontend/_resources/javascript/mopt_klarna_confirm.js',
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
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\BackendOrder($container),
            new \Shopware\Plugins\MoptPaymentPayone\Subscribers\Backend($container)
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

        /** @var Payment $payment */
        $payment = $this->Payments()->findOneBy(
            array('name' => 'mopt_payone__fin_klarna')
        );
        if ($payment) {
            $payment->setName('mopt_payone__fin_klarna_old');
            $payment->setDescription('PAYONE Klarna OLD');
            $payment->setTemplate('mopt_paymentmean_klarna_old.tpl');
            Shopware()->Models()->persist($payment);
            Shopware()->Models()->flush();
        }

        /** @var Shopware\Models\Payment\Payment $payment */
        $payment = $this->Payments()->findOneBy(
            array('name' => 'mopt_payone__cc_discover')
        );
        if ($payment === null) {
            // do nothing

        } else {
            $payment->setActive(false);
            Shopware()->Models()->persist($payment);
            Shopware()->Models()->flush();
        }

        // Update PAYONE Paysafe Payment Names
        /** @var Payment $payment */
        $payment = $this->Payments()->findOneBy(
            array('name' => 'mopt_payone__fin_payolution_invoice')
        );
        if ($payment) {
            $payment->setDescription('PAYONE Unzer Rechnungskauf');
            Shopware()->Models()->persist($payment);
            Shopware()->Models()->flush();
        }

        /** @var Payment $payment */
        $payment = $this->Payments()->findOneBy(
            array('name' => 'mopt_payone__fin_payolution_debitnote')
        );
        if ($payment) {
            $payment->setDescription('PAYONE Unzer Lastschrift');
            Shopware()->Models()->persist($payment);
            Shopware()->Models()->flush();
        }

        /** @var Payment $payment */
        $payment = $this->Payments()->findOneBy(
            array('name' => 'mopt_payone__fin_payolution_installment')
        );
        if ($payment) {
            $payment->setDescription('PAYONE Unzer Ratenkauf');
            Shopware()->Models()->persist($payment);
            Shopware()->Models()->flush();
        }
    }


    /**
     * create tables, add coloumns
     *
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     * @throws DBALException
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

        try {
            $schemaTool->createSchema(array(
                $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionForwardQueue\MoptPayoneTransactionForwardQueue'),
            ));
        } catch (ToolsException $e) {
            // ignore
        }

        $this->getInstallHelper()->moptCreatePaymentDataTable();
        $this->getInstallHelper()->createDocumentTemplates();

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

        // config option for transaction forwarding timeout and max trials
        $this->getInstallHelper()->moptExtendConfigTransactionTimeoutTrials();

        // config option for order change on transaction status
        $this->getInstallHelper()->moptExtendConfigChangeOrderOnTXS();

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

        $this->getInstallHelper()->checkAndUpdateConsumerscoreExtension();

        $this->getInstallHelper()->checkAndUpdateSendOrderNumberAsReferenceExtension();

        $this->getInstallHelper()->checkAndUpdateTransLoggingExtension();

        // config option for AmazaonPay Packstation allow
        $this->getInstallHelper()->checkAndUpdateAmazonPackStationModelExtension();

        // config option for PayPal Packstation allow
        $this->getInstallHelper()->checkAndUpdatePayPalPackStationModelExtension();

        // Add auto_cardtype_detection config table column if needed.
        $this->getInstallHelper()->checkAndAddAutoCardtypeDetectionColumn();

        // Add new reminder level config table columns if needed.
        $this->getInstallHelper()->checkAndAddReminderLevelColumns();

        // Add config field for global Ratepay SnippetId setting.
        $this->getInstallHelper()->checkAndAddRatepaySnippetIdColumn();

        // Applepay fields
        $this->getInstallHelper()->checkAndAddApplepayConfig();

        // used by the bnpl payments
        $this->getInstallHelper()->checkAndUpdateAllowDifferentAdressesOption();

        // add new config field creditcard_description_grouped
        $this->getInstallHelper()->checkAndAddCreditcardDefaultDescription();

        $this->getInstallHelper()->checkAndAddPaypalExpressUseDefaultShipping();

        $this->getInstallHelper()->checkAndAddPaypalV2ShowButton();

        $this->getInstallHelper()->checkAndAddPaypalV2MerchantId();

        $this->getInstallHelper()->checkAndAddPaypalV2ButtonColor();

        $this->getInstallHelper()->checkAndAddPaypalV2ButtonShape();

        // Do not add/remove columns to s_plugin_mopt_payone_config, after PPE migration
        /** @var Payment $payment */
        $paypalExpressPayment = $this->Payments()->findOneBy(['name' => 'mopt_payone__ewallet_paypal_express']);
        $doPaypalMigration = $this->getInstallHelper()->checkPaypalMigration();
        if ($paypalExpressPayment && $doPaypalMigration) {
            // migrate Shopware paypal settings, dispatch Settings and Payone Config settings to paypal express
            $this->getInstallHelper()->migratePaypalSettings();
        }

        // Add shop to paypal express config
        $this->getInstallHelper()->checkAndUpdatePayPalShopModelExtension();

        // remove column is_default from paypal express config
        $this->getInstallHelper()->checkAndUpdatePayPalDefaultModelExtension();

        // remove column locale_id from paypal express config
        $this->getInstallHelper()->checkAndRemovePayPalLocaleModelExtension();

        // Add shop to paypal express config
        $this->getInstallHelper()->checkAndUpdateAmazonPayShopModelExtension();

        $this->getInstallHelper()->checkAndAddPaypalExpressUseDefaultShipping();

        $this->getInstallHelper()->moptCreateCreditcardPaymentDataTable();

        $this->getInstallHelper()->checkAndRemovePaydirektExtension();

        $this->getInstallHelper()->checkAndRemoveTrustlyExtension();

        $this->addGooglePayConfigOptions();


    }

    /**
     * extend shpoware models with PAYONE specific attributes
     */
    protected function addAttributes()
    {
        $prefix = 'mopt_payone';
        $tables = $this->getInstallHelper()->moptAttributeExtensionsArray52($this->getId());

        $attributeService = Shopware()->Container()->get('shopware_attribute.crud_service');
        foreach ($tables as $table => $attributes) {
            foreach ($attributes as $attribute => $options) {
                // if attribute columns are manually deleted, prevent still existing entries in s_attribute_configuration
                // to throw a column already exists exception on insert
                if (!$this->attributeColumnExists($table, $prefix . '_' . $attribute )) {
                    $this->removeAttributeColumnFromConfig($table, $prefix . '_' . $attribute);
                }

                $type = is_array($options) ? $options[0] : $options;
                $data = is_array($options) ? $options[1] : [];
                $attributeService->update($table, $prefix . '_' . $attribute, $type, $data);
            }
        }
        Shopware()->Models()->generateAttributeModels(array_keys($tables));
    }

    /**
     * Helper function to check if a column exists
     *
     * @param string $tableName
     * @param string $columnName
     *
     * @return bool
     */
    public function attributeColumnExists($tableName, $columnName)
    {
        $sql = 'SELECT column_name
                FROM information_schema.columns
                WHERE table_name = :tableName
                    AND column_name = :columnName
                    AND table_schema = DATABASE();';

        $columnNameInDb = Shopware()->DB()->executeQuery(
            $sql,
            ['tableName' => $tableName, 'columnName' => $columnName]
        )->fetchColumn();

        return $columnNameInDb === $columnName;
    }

    /**
     * Helper function to remove entries in s_attribute_configuration
     *
     * @param string $tableName
     * @param string $columnName
     *
     * @return void
     */
    private function removeAttributeColumnFromConfig($tableName, $columnName) {
        /** @var \Shopware\Components\Model\ModelManager $em */
        $em = Shopware()->Models();

        $repository = $em->getRepository(Configuration::class);

        $entity = $repository->findOneBy([
            'tableName' => $tableName,
            'columnName' => $columnName,
        ]);

        if ($entity) {
            $em->remove($entity);
            $em->flush($entity);
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
            'label' => 'Payone PayPal Express',
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
            $streamHandler = new Monolog\Handler\StreamHandler( Shopware()->Container()->get('kernel')->getLogDir()
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

    private function addGooglePayConfigOptions()
    {
        $this->getInstallHelper()->checkAndAddGooglePayAllowCardOptions();
        $this->getInstallHelper()->checkAndAddGooglePayCountryCode();
        $this->getInstallHelper()->checkAndAddGooglePayButtonOptions();
        $this->getInstallHelper()->checkAndAddGooglePayMerchantId();
    }

}
