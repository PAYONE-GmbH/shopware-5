<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * This class handles:
 * installment, uninstallment
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

use Shopware\Models\Payment\Payment;
use Shopware\Models\Dispatch\Dispatch;
use Doctrine\DBAL\Connection;

class Mopt_PayoneInstallHelper
{
    /**
     * Type mapping from Shopware 5.2 to improve legacy compatibility,
     * as engine/Shopware/Bundle/AttributeBundle/Service/TypeMapping.php
     * is not present in Shopware versions < 5.2.0
     */
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_COMBOBOX = 'combobox';
    const TYPE_SINGLE_SELECTION = 'single_selection';
    const TYPE_MULTI_SELECTION = 'multi_selection';

    const DEFAULT_TRANSACTION_STATE_APPOINTED = 10; // Komplett in Rechnung gestellt
    const DEFAULT_TRANSACTION_STATE_CAPTURE = 33; // Die Zahlung wurde angewiesen
    const DEFAULT_TRANSACTION_STATE_PAID = 12; // Komplett bezahlt;
    const DEFAULT_TRANSACTION_STATE_UNDERPAID = 11; // Teilweise bezahlt;
    const DEFAULT_TRANSACTION_STATE_CANCELATION = 35; // Vorgang wurde abgebrochen;
    const DEFAULT_TRANSACTION_STATE_REFUND = 20; // Wiedergutschrift;
    const DEFAULT_TRANSACTION_STATE_DEBIT = 20; // Wiedergutschrift;

    /**
     * @var array
     */
    private $types = [
        self::TYPE_STRING => [
            'sql' => 'VARCHAR(500)',
            'dbal' => 'string',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => true,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_TEXT => [
            'sql' => 'TEXT',
            'dbal' => 'text',
            'allowDefaultValue' => false,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_HTML => [
            'sql' => 'MEDIUMTEXT',
            'dbal' => 'text',
            'allowDefaultValue' => false,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_INTEGER => [
            'sql' => 'INT(11)',
            'dbal' => 'integer',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'long']
        ],
        self::TYPE_FLOAT => [
            'sql' => 'DOUBLE',
            'dbal' => 'float',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'double']
        ],
        self::TYPE_BOOLEAN => [
            'sql' => 'INT(1)',
            'dbal' => 'boolean',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'boolean']
        ],
        self::TYPE_DATE => [
            'sql' => 'DATE',
            'dbal' => 'date',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => true,
            'elastic' => ['type' => 'date', 'format' => 'yyyy-MM-dd']
        ],
        self::TYPE_DATETIME => [
            'sql' => 'DATETIME',
            'dbal' => 'datetime',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => true,
            'elastic' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss']
        ],
        self::TYPE_COMBOBOX => [
            'sql' => 'MEDIUMTEXT',
            'dbal' => 'text',
            'allowDefaultValue' => false,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_SINGLE_SELECTION => [
            'sql' => 'VARCHAR(500)',
            'dbal' => 'text',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => true,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_MULTI_SELECTION => [
            'sql' => 'MEDIUMTEXT',
            'dbal' => 'text',
            'allowDefaultValue' => false,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'string']
        ]
    ];

    /**
     * after updating the plugin from versions < 3.8.2
     * set the authorisation method of payolution payments to preauth
     *
     * @return void
     */
    public function updatePayolutionAuthSettings()
    {
        $moptPayonePaymentHelper = new Mopt_PayonePaymentHelper();
        $sql = 'SELECT payment_id, authorisation_method FROM s_plugin_mopt_payone_config;';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() > 0) {

            $resultArr = $result->fetchAll();
            foreach ($resultArr as $value) {
                $paymentId = $value['payment_id'];
                $authMethod = $value['authorisation_method'];
                $paymentName = $moptPayonePaymentHelper->getPaymentNameFromId($paymentId);
                if ($moptPayonePaymentHelper->isPayonePayolutionInvoice($paymentName) || $moptPayonePaymentHelper->isPayonePayolutionDebitNote($paymentName) || $moptPayonePaymentHelper->isPayonePayolutionInstallment($paymentName)) {

                    if ($authMethod == 'Vorautorisierung') {
                        // nothing to do
                    } else {
                        $updateSQl = "UPDATE s_plugin_mopt_payone_config SET authorisation_method = \"Vorautorisierung\" WHERE payment_id = $paymentId;";
                        Shopware()->Db()->query($updateSQl);
                    }
                }
            }
        }
    }

    /**
     * returns mapped SQL type from unified type string
     *
     * Type mapping from Shopware 5.2 to improve legacy compatibility,
     * as engine/Shopware/Bundle/AttributeBundle/Service/TypeMapping.php
     * is not present in Shopware versions < 5.2.0
     *
     * @param string $type
     * @return string
     */
    public function unifiedToSQL($type)
    {
        $type = strtolower($type);
        if (!isset($this->types[$type])) {
            return $this->types['string']['sql'];
        }
        $mapping = $this->types[$type];
        return $mapping['sql'];
    }

    /**
     * - returns the definition for attribute table extensions
     * - intended to be used with Shopware version < 5.2.0
     * - Shopware versions < 5.2.0 can use the definitions by mapping
     * the types with unifiedToSQL() of this helper class
     *
     * @param int $pluginId
     * @return array
     */
    public function moptAttributeExtensionsArray($pluginId)
    {
        return [
            's_user_attributes' => [
                'consumerscore_result' => 'string',
                'consumerscore_date' => 'date',
                'consumerscore_color' => 'string',
                'consumerscore_value' => 'integer',
                'ratepay_ban' => 'date',
                'creditcard_initial_payment' => 'boolean',
            ],
            's_user_billingaddress_attributes' => [
                'addresscheck_result' => 'string',
                'addresscheck_date' => 'date',
                'addresscheck_personstatus' => 'string',
                'consumerscore_result' => 'string',
                'consumerscore_date' => 'date',
                'consumerscore_color' => 'string',
                'consumerscore_value' => 'integer',
            ],
            's_user_shippingaddress_attributes' => [
                'addresscheck_result' => 'string',
                'addresscheck_date' => 'date',
                'addresscheck_personstatus' => 'string',
                'consumerscore_color' => 'string',
                'consumerscore_value' => 'integer',
            ],
            's_order_attributes' => [
                'txid' => 'integer',
                'status' => 'string',
                'sequencenumber' => 'integer',
                'is_authorized' => 'boolean',
                'is_finally_captured' => 'boolean',
                'clearing_data' => 'text',
                // since 2.1.4 - save shipping cost with order
                'ship_captured' => ['float',
                    [
                        'label' => 'Versandkosten bisher eingezogen:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                'ship_debit' => ['float',
                    [
                        'label' => 'Versandkosten bisher gutgeschrieben:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                // since 2.3.0 - save payment data for abo commerce support
                'payment_data' => 'text',
                // since 2.5.2 - save order hash and payment reference
                'payment_reference' => 'string',
                'order_hash' => 'string',
                // since 3.3.8 - Payolution Payment Order extensions
                'payolution_workorder_id' => ['string',
                    [
                        'label' => 'Workorder ID:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                'payolution_clearing_reference' => ['string',
                    [
                        'label' => 'Clearing Reference:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
            ],
            's_order_details_attributes' => [
                'payment_status' => 'string',
                'shipment_date' => 'date',
                'captured' => 'float',
                'debit' => 'float',
            ],
        ];
    }

    /**
     * - returns the definition for attribute table extensions
     * - intended to be used with Shopware version >= 5.2.0
     *
     * @return array
     */
    public function moptAttributeExtensionsArray52($pluginId)
    {
        return [
            's_user_attributes' => [
                'consumerscore_result' => 'string',
                'consumerscore_date' => 'date',
                'consumerscore_color' => 'string',
                'consumerscore_value' => 'integer',
                'ratepay_ban' => 'date',
                'klarna_personalid' => 'string',
                'creditcard_initial_payment' => 'boolean',
            ],
            's_user_addresses_attributes' => [
                'addresscheck_result' => 'string',
                'addresscheck_date' => 'date',
                'addresscheck_personstatus' => 'string',
                'consumerscore_result' => 'string',
                'consumerscore_date' => 'date',
                'consumerscore_color' => 'string',
                'consumerscore_value' => 'integer',
            ],
            's_order_attributes' => [
                'txid' => 'integer',
                'status' => 'string',
                'sequencenumber' => 'integer',
                'is_authorized' => 'boolean',
                'is_finally_captured' => 'boolean',
                'clearing_data' => 'text',
                // since 2.1.4 - save shipping cost with order
                'ship_captured' => ['float',
                    [
                        'label' => 'Versandkosten bisher eingezogen:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                'ship_debit' => ['float',
                    [
                        'label' => 'Versandkosten bisher gutgeschrieben:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                // since 2.3.0 - save payment data for abo commerce support
                'payment_data' => 'text',
                // since 2.5.2 - save order hash and payment reference
                'payment_reference' => 'string',
                'order_hash' => 'string',
                // since 3.3.8 - Payolution Payment Order extensions
                'payolution_workorder_id' => ['string',
                    [
                        'label' => 'Workorder ID:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                'payolution_clearing_reference' => ['string',
                    [
                        'label' => 'Clearing Reference:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
            ],
            's_order_details_attributes' => [
                'payment_status' => 'string',
                'shipment_date' => 'date',
                'captured' => 'float',
                'debit' => 'float',
            ],
        ];
    }

    /**
     * returns array of PAYONE payment methods
     *
     * payment types are grouped
     * mopt_payone__[group]_[brand]
     *
     * @return array
     */
    public function mopt_payone__getPaymentMethods()
    {
        return [
            [
                'name' => 'mopt_payone__cc_visa',
                'description' => 'PAYONE Visa',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 1,],
            [
                'name' => 'mopt_payone__cc_mastercard',
                'description' => 'PAYONE Mastercard',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 2,],
            [
                'name' => 'mopt_payone__cc_american_express',
                'description' => 'PAYONE American Express',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 3,],
            [
                'name' => 'mopt_payone__cc_carte_blue',
                'description' => 'PAYONE Carte Blue',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 4,],
            [
                'name' => 'mopt_payone__cc_diners_club',
                'description' => 'PAYONE Diners Club',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 5,],
            [
                'name' => 'mopt_payone__cc_jcb',
                'description' => 'PAYONE JCB',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 7,],
            [
                'name' => 'mopt_payone__cc_china_union',
                'description' => 'PAYONE China Union Pay',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 8,],
            [
                'name' => 'mopt_payone__ibt_sofortueberweisung',
                'description' => 'PAYONE SOFORT Überweisung',
                'template' => 'mopt_paymentmean_sofort.tpl',
                'position' => 9,],
            [
                'name' => 'mopt_payone__ibt_eps',
                'description' => 'PAYONE eps',
                'template' => 'mopt_paymentmean_eps.tpl',
                'position' => 10,],
            [
                'name' => 'mopt_payone__ibt_post_efinance',
                'description' => 'PAYONE Post-Finance EFinance',
                'template' => null,
                'position' => 11,],
            [
                'name' => 'mopt_payone__ibt_post_finance_card',
                'description' => 'PAYONE Post-Finance Card',
                'template' => null,
                'position' => 12,],
            [
                'name' => 'mopt_payone__ibt_ideal',
                'description' => 'PAYONE iDeal',
                'template' => 'mopt_paymentmean_ideal.tpl',
                'position' => 13,],
            [
                'name' => 'mopt_payone__ewallet_paypal',
                'description' => 'PAYONE PayPal',
                'template' => null,
                'position' => 14,],
            [
                'name' => 'mopt_payone__acc_debitnote',
                'description' => 'PAYONE Lastschrift',
                'template' => 'mopt_paymentmean_debit.tpl',
                'position' => 15,],
            [
                'name' => 'mopt_payone__acc_invoice',
                'description' => 'PAYONE Offene Rechnung',
                'template' => null,
                'position' => 16,],
            [
                'name' => 'mopt_payone__acc_payinadvance',
                'description' => 'PAYONE Vorkasse',
                'template' => null,
                'position' => 17,],
            [
                'name' => 'mopt_payone__acc_cashondel',
                'description' => 'PAYONE Nachnahme',
                'template' => null,
                'position' => 18,],
            [
                'name' => 'mopt_payone__fin_kis_klarna_installments',
                'description' => 'PAYONE Klarna Slice It',
                'template' => 'mopt_paymentmean_klarna.tpl',
                'position' => 19,],
            [
                'name' => 'mopt_payone__fin_kiv_klarna_invoice',
                'description' => 'PAYONE Klarna Pay Later',
                'template' => 'mopt_paymentmean_klarna.tpl',
                'position' => 20,],
            [
                'name' => 'mopt_payone__fin_kdd_klarna_direct_debit',
                'description' => 'PAYONE Klarna Pay Now',
                'template' => 'mopt_paymentmean_klarna.tpl',
                'position' => 21,],
            [
                'name' => 'mopt_payone__ibt_p24',
                'description' => 'PAYONE P24',
                'template' => null,
                'position' => 22,],
            [
                'name' => 'mopt_payone__fin_payolution_invoice',
                'description' => 'PAYONE Unzer Rechnungskauf',
                'template' => 'mopt_paymentmean_payolution_invoice.tpl',
                'position' => 23,],
            [
                'name' => 'mopt_payone__fin_payolution_debitnote',
                'description' => 'PAYONE Unzer Lastschrift',
                'template' => 'mopt_paymentmean_payolution_debitnote.tpl',
                'position' => 24,],
            [
                'name' => 'mopt_payone__fin_payolution_installment',
                'description' => 'PAYONE Unzer Ratenkauf',
                'template' => 'mopt_paymentmean_payolution_installment.tpl',
                'position' => 25,],
            [
                'name' => 'mopt_payone__fin_ratepay_invoice',
                'description' => 'PAYONE Ratepay Rechnungskauf',
                'template' => 'mopt_paymentmean_ratepay_invoice.tpl',
                'position' => 26,],
            [
                'name' => 'mopt_payone__fin_ratepay_installment',
                'description' => 'PAYONE Ratepay Ratenkauf',
                'template' => 'mopt_paymentmean_ratepay_installment.tpl',
                'position' => 27,],
            [
                'name' => 'mopt_payone__fin_ratepay_direct_debit',
                'description' => 'PAYONE Ratepay Lastschrift',
                'template' => 'mopt_paymentmean_ratepay_direct_debit.tpl',
                'position' => 28,],
            [
                'name' => 'mopt_payone__acc_payone_safe_invoice',
                'description' => 'PAYONE Rechnung mit Zahlungsgarantie',
                'template' => 'mopt_paymentmean_payone_safe_invoice.tpl',
                'position' => 29,],
            [
                'name' => 'mopt_payone__ibt_bancontact',
                'description' => 'PAYONE Bancontact',
                'template' => 'mopt_paymentmean_bancontact.tpl',
                'position' => 30,],
            [
                'name' => 'mopt_payone__ewallet_amazon_pay',
                'description' => 'PAYONE Amazon Pay',
                'template' => 'mopt_paymentmean_amazon_ewallet.tpl',
                'position' => 31,],
            [
                'name' => 'mopt_payone__ewallet_alipay',
                'description' => 'PAYONE AliPay',
                'template' => 'mopt_paymentmean_alipay_ewallet.tpl',
                'position' => 32,],
            [
                'name' => 'mopt_payone__ewallet_wechatpay',
                'description' => 'PAYONE WeChatPay',
                'template' => null,
                'position' => 33,],
            [
                'name' => 'mopt_payone__ewallet_applepay',
                'description' => 'PAYONE Apple Pay',
                'template' => 'mopt_paymentmean_applepay.tpl',
                'position' => 34,],
            [
                'name' => 'mopt_payone__ewallet_paypal_express',
                'description' => 'PAYONE PayPal Express',
                'template' => null,
                'position' => 35,],
            [
                'name' => 'mopt_payone__fin_payone_secured_invoice',
                'description' => 'PAYONE Gesicherter Rechnungskauf',
                'template' => 'mopt_paymentmean_payone_secured_invoice.tpl',
                'position' => 36,],
            [
                'name' => 'mopt_payone__fin_payone_secured_installment',
                'description' => 'PAYONE Gesicherter Ratenkauf',
                'template' => 'mopt_paymentmean_payone_secured_installment.tpl',
                'position' => 37,],
            [
                'name' => 'mopt_payone__fin_payone_secured_directdebit',
                'description' => 'PAYONE Gesicherte Lastschrift',
                'template' => 'mopt_paymentmean_payone_secured_directdebit.tpl',
                'position' => 38,],
            [
                'name' => 'mopt_payone__ewallet_paypalv2',
                'description' => 'PAYONE PayPal v2',
                'template' => null,
                'position' => 39,],
            [
                'name' => 'mopt_payone__ewallet_paypal_expressv2',
                'description' => 'PAYONE PayPal Express v2',
                'template' => null,
                'position' => 40,],
            [
                'name' => 'mopt_payone__ewallet_googlepay',
                'description' => 'PAYONE Google Pay',
                'template' => 'mopt_paymentmean_googlepay.tpl',
                'position' => 41,],
        ];
    }

    /**
     * add payment data table
     */
    public function moptCreatePaymentDataTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_payment_data` 
                (`userId` int(11) NOT NULL,`moptPaymentData` TEXT NOT NULL, PRIMARY KEY (`userId`))";
        Shopware()->Db()->exec($sql);
    }

    /**
     * add payment data table
     */
    public function moptCreateCreditcardPaymentDataTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_creditcard_payment_data`
                (`userId` int(11) NOT NULL, `moptCreditcardPaymentData` TEXT , PRIMARY KEY (`userId`))";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with sepa config coloumns
     */
    public function moptExtendConfigDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN show_accountnumber TINYINT(1) NOT NULL DEFAULT 0,"
            . "ADD COLUMN mandate_active TINYINT(1) NOT NULL DEFAULT 0,"
            . "ADD COLUMN mandate_download_enabled TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * Extends transaction forwarding config with timeout, timeout raise and max trials if not exits
     */
    public function moptExtendConfigTransactionTimeoutTrials()
    {
        $queries = [];
        if (!$this->moptConfigTransactionTimeoutExist()) {
            $queries[] = "ALTER TABLE `s_plugin_mopt_payone_config` ADD COLUMN trans_timeout int(11) NOT NULL DEFAULT 0;";
        }
        if (!$this->moptConfigTransactionTimeoutRaiseExist()) {
            $queries[] = "ALTER TABLE `s_plugin_mopt_payone_config` ADD COLUMN trans_timeout_raise int(11) NOT NULL DEFAULT 0;";
        }
        if (!$this->moptConfigTransactionTrialsExist()) {
            $queries[] = "ALTER TABLE `s_plugin_mopt_payone_config` ADD COLUMN trans_max_trials int(11) NOT NULL DEFAULT 0;";
        }

        if (count($queries) > 0) {
            foreach ($queries as $query) {
                try {
                    Shopware()->Db()->exec($query);
                } catch (Zend_Db_Adapter_Exception $e) {
                }
            }
        }
    }

    /**
     * Extends main config with change_order_on_txs if not exits
     *
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function moptExtendConfigChangeOrderOnTXS()
    {
        if (!$this->moptConfigChangeOrderOnTXSExist()) {
            $query = "ALTER TABLE `s_plugin_mopt_payone_config` ADD COLUMN change_order_on_txs TINYINT(1) NOT NULL DEFAULT 0;";

            Shopware()->Db()->exec($query);
        }
    }

    /**
     * Checks the existence of change_order_on_txs
     *
     * @throws Zend_Db_Statement_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function moptConfigChangeOrderOnTXSExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND TABLE_NAME='s_plugin_mopt_payone_config'
                AND COLUMN_NAME ='change_order_on_txs'";

        $result = Shopware()->Db()->query($sql);

        return $result->rowCount() !== 0;
    }

    /**
     * Checks the existence of trans_timeout
     */
    public function moptConfigTransactionTimeoutExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND TABLE_NAME='s_plugin_mopt_payone_config'
                AND COLUMN_NAME ='trans_timeout'";
        try {
            $result = Shopware()->Db()->query($sql);

            return $result->rowCount() !== 0;
        } catch (Zend_Db_Statement_Exception $e) {
        } catch (Zend_Db_Adapter_Exception $e) {
        }
    }

    /**
     * Checks the existence of trans_timeout_raise
     */
    public function moptConfigTransactionTimeoutRaiseExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND TABLE_NAME='s_plugin_mopt_payone_config'
                AND COLUMN_NAME ='trans_timeout_raise'";
        try {
            $result = Shopware()->Db()->query($sql);

            return $result->rowCount() !== 0;
        } catch (Zend_Db_Statement_Exception $e) {
        } catch (Zend_Db_Adapter_Exception $e) {
        }
    }

    /**
     * Checks the existence of trans_max_trials
     */
    public function moptConfigTransactionTrialsExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND TABLE_NAME='s_plugin_mopt_payone_config'
                AND COLUMN_NAME ='trans_max_trials'";
        try {
            $result = Shopware()->Db()->query($sql);

            return $result->rowCount() !== 0;
        } catch (Zend_Db_Statement_Exception $e) {
        } catch (Zend_Db_Adapter_Exception $e) {
        }
    }

    /**
     * extend config data table with extended address check attributes
     */
    public function moptExtendConfigAddressCheckDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN map_address_check_not_possible INT(11) NOT NULL DEFAULT 0,"
            . "ADD COLUMN map_address_okay_building_unknown INT(11) NOT NULL DEFAULT 0,"
            . "ADD COLUMN map_person_moved_address_unknown INT(11) NOT NULL DEFAULT 0,"
            . "ADD COLUMN map_unknown_return_value INT(11) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with klarna config coloumn
     */
    public function moptExtendConfigKlarnaDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN klarna_store_id VARCHAR(255) DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with payolution config coloumn
     */
    public function moptExtendConfigPayolutionDataTable()
    {
        $sql = "SELECT value FROM s_core_config_values "
            . "WHERE element_id = '893';";
        $result = Shopware()->Db()->query($sql);
        $serializedCompanyName = $result->fetchColumn(0);
        $companyName = unserialize((string)$serializedCompanyName);
        if ($companyName) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN payolution_company_name VARCHAR(255) DEFAULT '" . $companyName . "' ,"
                . "ADD COLUMN payolution_b2bmode TINYINT(1) NOT NULL DEFAULT 1;";
        } else {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN payolution_company_name VARCHAR(255) DEFAULT 'Ihr Firmenname' ,"
                . "ADD COLUMN payolution_b2bmode TINYINT(1) NOT NULL DEFAULT 1;";
        }

        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with showBic config column
     */
    public function fcExtendConfigShowBicDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN show_bic TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with showSofortIbanBic config column
     */
    public function fcExtendConfigShowSofortIbanBicDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN show_sofort_iban_bic TINYINT(1) NOT NULL DEFAULT 1;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with showSofortIbanBic config column
     */
    public function fcExtendConfigRatepayInstallmentModeDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_ratepay` "
            . "ADD COLUMN ratepay_installment_mode TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with save terms coloumn
     */
    public function moptExtendConfigSaveTermsDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN save_terms VARCHAR(255) DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with save terms coloumn
     */
    public function moptExtendConfigPaypalEcsDataTable()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN paypal_ecs_active TINYINT(1) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config data table with save terms coloumn
     */
    public function moptExtendConfigCreditcardMinValidDays()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN creditcard_min_valid INT(11) NOT NULL DEFAULT 0;";
        Shopware()->Db()->exec($sql);
    }

    /**
     * extend config table for addresscheck country configuration
     */
    public function moptExtendConfigAddressCheckCountries()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN adresscheck_billing_countries VARCHAR(255);";
        $sql1 = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN adresscheck_shipping_countries VARCHAR(255);";
        Shopware()->Db()->exec($sql);
        Shopware()->Db()->exec($sql1);
    }

    /**
     * @return void
     */
    public function createDocumentTemplates()
    {
        $sql = 'SELECT * FROM s_core_documents_box WHERE name = ? OR name = ?';
        $result = Shopware()->Db()->query($sql, ['PAYONE_Footer', 'PAYONE_Content_Info']);

        if ($result->rowCount() < 2) {

            $sql = "
            INSERT INTO `s_core_documents_box` (`documentID`, `name`, `style`, `value`) VALUES
            (1, 'PAYONE_Footer', :footerStyle, :footerValue),
            (1, 'PAYONE_Content_Info', :contentStyle, :contentValue);
        ";

            $footerStyle = \file_get_contents(__DIR__ . '/../../Documents/Payone_Footer_Style.html');
            $footerContent = \file_get_contents(__DIR__ . '/../../Documents/Payone_Footer.html');
            $contentInfoStyle = \file_get_contents(__DIR__ . '/../../Documents/Payone_Content_Info_Style.html');
            $contentInfoContent = \file_get_contents(__DIR__ . '/../../Documents/Payone_Content_Info.html');

            Shopware()->Db()->query($sql, [
                'footerStyle' => $footerStyle,
                'footerValue' => $footerContent,
                'contentStyle' => $contentInfoStyle,
                'contentValue' => $contentInfoContent
            ]);
        }
    }

    /**
     * @return void
     */
    public function removeDocumentTemplates()
    {
        $sql = "DELETE FROM s_core_documents_box WHERE `name` LIKE 'PAYONE%'";
        Shopware()->Db()->query($sql);
    }

    /**
     * insert empty config on installations to keep the shop stable
     * without any config and activated plugin exceptions will occur during checkout
     */
    public function moptInsertEmptyConfigIfNotExists()
    {
        $sql = 'SELECT id FROM s_plugin_mopt_payone_config WHERE payment_id = 0;';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "INSERT INTO `s_plugin_mopt_payone_config` (`payment_id`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `authorisation_method`, `submit_basket`, `adresscheck_active`, `adresscheck_live_mode`, `adresscheck_billing_adress`, `adresscheck_shipping_adress`, `adresscheck_automatic_correction`, `adresscheck_failure_handling`, `adresscheck_min_basket`, `adresscheck_max_basket`, `adresscheck_lifetime`, `adresscheck_failure_message`, `map_person_check`, `map_know_pre_lastname`, `map_know_lastname`, `map_not_known_pre_lastname`, `map_multi_name_to_adress`, `map_undeliverable`, `map_person_dead`, `map_wrong_adress`, `map_address_check_not_possible`, `map_address_okay_building_unknown`, `map_person_moved_address_unknown`, `map_unknown_return_value`, `consumerscore_active`, `consumerscore_live_mode`, `consumerscore_check_moment`, `consumerscore_check_mode_b2c`, `consumerscore_check_mode_b2b`,`consumerscore_default`, `consumerscore_lifetime`, `consumerscore_min_basket`, `consumerscore_max_basket`, `consumerscore_failure_handling`, `consumerscore_note_message`, `consumerscore_note_active`, `consumerscore_agreement_message`, `consumerscore_agreement_active`, `consumerscore_abtest_value`, `consumerscore_abtest_active`, `payment_specific_data`, `state_appointed`, `state_capture`, `state_paid`, `state_underpaid`, `state_cancelation`, `state_refund`, `state_debit`, `state_reminder`, `state_vauthorization`, `state_vsettlement`, `state_transfer`, `state_invoice`, `state_failed`,  `check_cc`, `check_account`, `trans_appointed`, `trans_capture`, `trans_paid`, `trans_underpaid`, `trans_cancelation`, `trans_refund`, `trans_debit`, `trans_reminder`, `trans_vauthorization`, `trans_vsettlement`, `trans_transfer`, `trans_invoice`, `trans_failed` , `trans_timeout` , `trans_timeout_raise` , `trans_max_trials`, `googlepay_allow_visa`, `googlepay_allow_master_card`, `googlepay_allow_prepaid_cards`, `googlepay_allow_credit_cards`, `googlepay_country_code`, `googlepay_button_color`, `googlepay_button_type`  ) VALUES
      (0, 0, 0, 0, '0', 0, 'Vorautorisierung', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'Es ist ein Fehler aufgetreten', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'IH','NONE', 0, 0, 0, 0, 0, '', 0, '', 0, 0, 0, 'N;'," . self::DEFAULT_TRANSACTION_STATE_APPOINTED . ", " . self::DEFAULT_TRANSACTION_STATE_CAPTURE . ", " . self::DEFAULT_TRANSACTION_STATE_PAID . ", " . self::DEFAULT_TRANSACTION_STATE_UNDERPAID . ", " . self::DEFAULT_TRANSACTION_STATE_CANCELATION . ", " . self::DEFAULT_TRANSACTION_STATE_REFUND . ", " . self::DEFAULT_TRANSACTION_STATE_REFUND . ", 0, 0, 0, 0, 0, 121,  1, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', :timeout, :timeout_raise, :max_trials, 1, 1, 1, 1, 'DE', 'default', 'pay');
      ";
            Shopware()->Db()->query($sql, [
                ':timeout' => Mopt_PayoneConfig::$MOPT_PAYONE_FORWARD_TRANSACTION_STATUS_DEFAULTS['curl_timeout'],
                ':timeout_raise' => Mopt_PayoneConfig::$MOPT_PAYONE_FORWARD_TRANSACTION_STATUS_DEFAULTS['curl_timeout_raise'],
                ':max_trials' => Mopt_PayoneConfig::$MOPT_PAYONE_FORWARD_TRANSACTION_STATUS_DEFAULTS['curl_trials_max'],
            ]);
        }

        // insert default values for creditcard config
        $sql = 'SELECT id FROM s_plugin_mopt_payone_creditcard_config';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "INSERT INTO `s_plugin_mopt_payone_creditcard_config` (`id`,`error_locale_id`, `shop_id`, `show_errors`, `is_default`, `integration_type`, `standard_input_css`, `standard_input_css_selected`, `standard_iframe_height`, `standard_iframe_width`, `cardno_input_chars`, `cardno_input_chars_max`, `cardno_input_css`, `cardno_custom_iframe`, `cardno_iframe_height`, `cardno_iframe_width`, `cardno_custom_style`, `cardno_field_type`, `cardcvc_input_chars`, `cardcvc_input_chars_max`, `cardcvc_input_css`, `cardcvc_custom_iframe`, `cardcvc_iframe_height`, `cardcvc_iframe_width`, `cardcvc_custom_style`, `cardcvc_field_type`, `cardmonth_input_chars`, `cardmonth_input_chars_max`, `cardmonth_input_css`, `cardmonth_custom_iframe`, `cardmonth_iframe_height`, `cardmonth_iframe_width`, `cardmonth_custom_style`, `cardmonth_field_type`, `cardyear_input_chars`, `cardyear_input_chars_max`, `cardyear_input_css`, `cardyear_custom_iframe`, `cardyear_iframe_height`, `cardyear_iframe_width`, `cardyear_custom_style`, `cardyear_field_type`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `check_cc`, `creditcard_min_valid`) VALUES
      (1,74, 1, 1, 1, 0, 'box-shadow:inset 0 1px 1px #dadae5;background:#f8f8fa;border:1px solid #dadae5;border-top-color:#cbcbdb;line-height:19px;font-size:.875rem;width:85%;padding:.625rem .625rem .5625rem .625rem;color:#8798a9;border-radius:3px','box-shadow:inset 0 1px 1px #dadae5;background:#f8f8fa;border:1px solid #dadae5;border-top-color:#cbcbdb;line-height:19px;font-size:.875rem;width:85%;padding:.625rem .625rem .5625rem .625rem;color:#8798a9;', '40px', '80px', 20, 20, '', 0, '40px', '100%', 1, 'tel', 4, 4, '', 0, '40px', '100px', 1, 'tel', 4, 4, '', 0, '40px', '80px', 1, 'select', 4, 4, '', 0, '40px', '90px', 1, 'select', 0, 0, 0, '', 0, 0, 0);
      ";
            Shopware()->Db()->query($sql);
        }

        // insert default values for amazon
        $sql = 'SELECT id FROM s_plugin_mopt_payone_amazon_pay';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "INSERT INTO `s_plugin_mopt_payone_amazon_pay` (`id`, `client_id`, `seller_id`, `button_type`, `button_color`, `button_language`, `amazon_mode`) VALUES
                  (1, '', '', 'PwA', 'Gold', 'none', 'sync');";
            Shopware()->Db()->query($sql);
        }
    }

    /**
     * check if payone configuration is already extended for sepa options
     *
     * @return boolean
     */
    public function moptPayoneConfigExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='show_accountnumber'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended for new address check codes
     *
     * @return boolean
     */
    public function moptPayoneConfigAddressCheckExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='map_unknown_return_value'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended for klarna options
     *
     * @return boolean
     */
    public function moptPayoneConfigKlarnaExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='klarna_store_id'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended for payolution payment
     *
     * @return boolean
     */
    public function moptPayoneConfigPayolutionExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='payolution_company_name';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended for show_bic config option
     *
     * @return boolean
     */
    public function fcPayoneConfigShowBicExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='show_bic';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended for show_sofort_iban_bic config option
     *
     * @return boolean
     */
    public function fcPayoneConfigShowSofortIbanBicExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='show_sofort_iban_bic';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended for show_ratepay_installment_mode config option
     *
     * @return boolean
     */
    public function fcPayoneConfigRatepayInstallmentModeExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_ratepay'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='ratepay_installment_mode';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended with save terms option
     *
     * @return boolean
     */
    public function moptPayoneConfigSaveTermsExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='save_terms'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended with paypal ecs option
     *
     * @return boolean
     */
    public function moptPayoneConfigPaypalEcsExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='paypal_ecs_active'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if ordernr column has correct type
     *
     * @return boolean
     */
    public function isTransactionLogModelUpdated()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_transaction_log'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='order_nr'";
        $result = Shopware()->Db()->query($sql);
        //$sql1    = "SHOW FIELDS FROM s_plugin_mopt_payone_transaction_log WHERE Field='order_nr'";
        //$sql2    = "describe ". $DBConfig['dbname'] .".s_plugin_mopt_payone_transaction_log order_nr";

        $type = $result->fetch();

        if ($type['DATA_TYPE'] === 'int') {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended country config for risk checks
     *
     * @return boolean
     */
    public function moptPayoneConfigRiskCountryExtensionExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='creditcard_min_valid'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * check if payone configuration is already extended boniversum config for risk checks
     *
     * @return void
     */
    public function checkAndUpdateBoniversumConfigModelExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='consumerscore_boniversum_unknown'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN consumerscore_boniversum_unknown INT(11) NOT NULL DEFAULT 2;";

            $db->exec($sql);
        }
    }

    /**
     * check if payone configuration is already extended for new status failed
     *
     * @return void
     */
    public function checkAndUpdateFailedStatusConfigModelExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='state_failed'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN state_failed INT(11) NULL DEFAULT NULL;";

            $db->exec($sql);
        }

        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='trans_failed'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN trans_failed LONGTEXT NULL DEFAULT NULL;";

            $db->exec($sql);
        }
    }

    /**
     * make sure an email template for payment status 121 exists
     * so customers get notified when txaction=failed is received from payone
     *
     * @return void
     */
    public function checkAndInsertFailedStatusEmailTemplate()
    {
        $db = Shopware()->Db();

        $sql = "SELECT * FROM s_core_states
                WHERE  id = 121";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = '
            INSERT INTO `s_core_states` (`id`, `name`, `description`, `position`, `group`, `mail`) VALUES
            
            (121, "amazon_failed", "Amazon Failed",121, "payment", 0);
            ';
            $db->exec($sql);
        }

        $sql = "SELECT * FROM s_core_config_mails
                WHERE  stateId = 121";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = '
            INSERT INTO `s_core_config_mails` (`stateId`, `name`, `frommail`, `fromname`, `subject`, `content`, `contentHTML`, `ishtml`, `attachment`, `mailtype`, `context`, `dirty`) VALUES
            ' .
                "
            (121, 'sORDERSTATEMAIL121', '{config name=mail}', '{config name=shopName}', 'Bitte kontaktieren Sie uns wegen Ihrer Bestellung',
            " . '\'{include file="string:{config name=emailheaderplain}\"}\r\n\r\n
Sehr geehrter Kunde,\n\n
Leider wurde die Zahlung zu Ihrer Bestellung in unserem Onlineshop {config name=shopName} von Amazon Pay zurückgewiesen. Bitte kontaktieren Sie uns.\r\n\r\n
{include file=\"string:{config name = emailfooterplain}\"}\', \'\', 0, \'\', 3, NULL, 0);
            ';
            $db->exec($sql);
        }

        if (version_compare(Shopware()->Config()->get('version'), '5.5.0', '>=') ||
            Shopware()->Config()->get('version') == '__VERSION___') {
            $sql = "SELECT * FROM s_core_snippets
                WHERE  name = 'amazon_failed' AND namespace = 'backend/static/payment_status'";
            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = '
            INSERT INTO `s_core_snippets` (`namespace`, `shopID`, `localeID`, `name`, `value` ) VALUES
            
            ("backend/static/payment_status",1 , 1, "amazon_failed", "Amazon Failed");
            ';
                $db->exec($sql);
            }
        }
    }

    /**
     * make sure an email template for payment status 119 exists
     * so customers get notified when txaction=approved and transaction_status = pending is received from payone
     *
     * @return void
     */
    public function checkAndInsertDelayedStatusEmailTemplate()
    {
        $db = Shopware()->Db();

        $sql = "SELECT * FROM s_core_states
                WHERE  id = 119";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = '
            INSERT INTO `s_core_states` (`id`, `name`, `description`, `position`, `group`, `mail`) VALUES
            
            (119, "amazon_delayed", "Amazon Delayed",119, "payment", 0);
            ';
            $db->exec($sql);
        }

        $sql = "SELECT * FROM s_core_config_mails
                WHERE  stateId = 119";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = '
            INSERT INTO `s_core_config_mails` (`stateId`, `name`, `frommail`, `fromname`, `subject`, `content`, `contentHTML`, `ishtml`, `attachment`, `mailtype`, `context`, `dirty`) VALUES
            ' .
                "
            (119, 'sORDERSTATEMAIL119', '{config name=mail}', '{config name=shopName}', 'Bitte aktualisieren Sie Ihre Zahlungsinformationen',
            " . '\'{include file="string:{config name=emailheaderplain}\"}\r\n\r\nSehr geehrter Kunde,\r\n
Vielen Dank für Ihre Bestellung bei {config name=shopName}.\r\n
Leider wurde Ihre Bezahlung von Amazon Pay abgelehnt.\r\n
Sie können unter https://pay.amazon.com/de/jr/youraccount/orders?language=de_DE
die Zahlungsinformationen für Ihre Bestellung
aktualisieren, indem Sie eine andere Zahlungsweise auswählen oder eine neue
Zahlungsweise angeben. Mit der neuen Zahlungsweise wird dann ein erneuter
Zahlungsversuch vorgenommen, und Sie erhalten eine Bestätigungsemail.\r\n\r\n
{include file=\"string:{config name = emailfooterplain}\"}\', \'\', 0, \'\', 3, NULL, 0);
            ';
            $db->exec($sql);
        }

        if (version_compare(Shopware()->Config()->get('version'), '5.5.0', '>=') ||
            Shopware()->Config()->get('version') == '__VERSION___') {
            $sql = "SELECT * FROM s_core_snippets
                WHERE  name = 'amazon_delayed' AND namespace = 'backend/static/payment_status'";
            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = '
            INSERT INTO `s_core_snippets` (`namespace`, `shopID`, `localeID`, `name`, `value` ) VALUES
            
            ("backend/static/payment_status",1 , 1, "amazon_delayed", "Amazon Delayed");
            ';
                $db->exec($sql);
            }
        }

    }

    /**
     * check if the old PAYONE plugin is active and rename label to prevent errors
     *
     * @return string
     */
    public function moptGetConfigurationLabelName()
    {
        $sql = "SELECT id FROM s_core_menu
                WHERE name = 'Konfiguration'
                AND controller <> 'MoptConfigPayone'";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return 'Konfiguration';
        }

        return 'PAYONE Konfiguration';
    }

    public function updateTransactionLogModel()
    {
        $sql = 'alter table s_plugin_mopt_payone_transaction_log change order_nr order_nr varchar(100)';
        Shopware()->Db()->query($sql);
    }

    public function checkAndUpdateCreditcardConfigModel($logger)
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "select CONSTRAINT_NAME from "
            . "information_schema.key_column_usage where "
            . "table_schema = '" . $DBConfig['dbname'] . "' and "
            . "table_name = 's_plugin_mopt_payone_creditcard_config' and column_name = 'error_locale_id';";
        $result = Shopware()->Db()->query($sql);


        if ($result->rowCount() === 1) {
            $constraint = $result->fetch();

            try {
                $sqlUpdate = 'ALTER TABLE s_plugin_mopt_payone_creditcard_config DROP INDEX '
                    . $constraint['CONSTRAINT_NAME'] . ';';
                Shopware()->Db()->query($sqlUpdate);
            } catch (Exception $exc) {
                $logger->error('removing unique index from '
                    . 's_plugin_mopt_payone_creditcard_config.error_locale_id failed', $exc->getMessage());
            }
        }

        $sqlColumn = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_creditcard_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='is_default'";
        $resultColumn = Shopware()->Db()->query($sqlColumn);

        if ($resultColumn->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_creditcard_config` "
                . "ADD COLUMN is_default TINYINT(1) DEFAULT 0;";
            Shopware()->Db()->exec($sql);
        }
    }

    public function checkAndUpdateCreditcardConfigModelExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_creditcard_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='merchant_id'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_creditcard_config` "
                . "ADD COLUMN merchant_id INT(11) NOT NULL DEFAULT 0,"
                . "ADD COLUMN portal_id INT(11) NOT NULL DEFAULT 0,"
                . "ADD COLUMN subaccount_id INT(11) NOT NULL DEFAULT 0,"
                . "ADD COLUMN api_key VARCHAR(100) NOT NULL,"
                . "ADD COLUMN live_mode TINYINT(1) NOT NULL DEFAULT 0,"
                . "ADD COLUMN check_cc TINYINT(1) NOT NULL DEFAULT 1,"
                . "ADD COLUMN creditcard_min_valid INT(11) DEFAULT 0;";

            $db->exec($sql);
        }
    }

    public function checkAndUpdateCreditcardModelIframeExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_creditcard_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='default_translation_iframe_month1'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_creditcard_config` "
                . "ADD COLUMN default_translation_iframe_month1 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month2 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month3 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month4 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month5 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month6 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month7 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month8 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month9 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month10 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month11 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframe_month12 VARCHAR(40) NULL,"
                . "ADD COLUMN default_translation_iframeinvalid_cardpan VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframeinvalid_cvc VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframeinvalid_pan_for_cardtype VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframeinvalid_cardtype VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframeinvalid_expire_date VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframeinvalid_issue_number VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframetransaction_rejected VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframe_cardpan VARCHAR(255) NULL,"
                . "ADD COLUMN default_translation_iframe_cvc VARCHAR(255) NULL;";
            $db->exec($sql);
        }
    }

    public function checkAndUpdateConfigModelPayolutionInstallmentExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='payolution_draft_user'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` ADD `payolution_draft_user` VARCHAR(255) NULL AFTER `payolution_b2bmode`,
                   ADD `payolution_draft_password` VARCHAR(255) NULL AFTER `payolution_draft_user`;";
            $db->exec($sql);
        }
    }

    public function checkAndUpdateConfigModelRatepayExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_ratepay'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='id'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_ratepay`(
            `shop_id` VARCHAR(32) NOT NULL,
            `merchant_name` VARCHAR(32) NULL,
            `merchant_status` TINYINT(2) NULL,
            `shop_name` VARCHAR(32) NULL,
            `name` VARCHAR(32) NULL,
            `currency_id` INT NULL,
            `type` VARCHAR(32) NULL,
            `activation_status_elv` TINYINT(2) NULL,
            `activation_status_installment` TINYINT(2) NULL,
            `activation_status_invoice` TINYINT(2) NULL,
            `activation_status_prepayment` TINYINT(2) NULL,
            `amount_min_longrun` DOUBLE NULL,
            `b2b_pq_full` TINYINT(1) NULL,
            `b2b_pq_light` TINYINT(1) NULL,
            `b2b_elv` TINYINT(1) NULL,
            `b2b_installment` TINYINT(1) NULL,
            `b2b_invoice` TINYINT(1) NULL,
            `b2b_prepayment` TINYINT(1) NULL,
            `country_code_billing` VARCHAR(32) NULL,
            `country_code_delivery` VARCHAR(32) NULL,
            `delivery_address_pq_full` TINYINT(1) NULL,
            `delivery_address_pq_light` TINYINT(1) NULL,
            `delivery_address_elv` TINYINT(1) NULL,
            `delivery_address_installment` TINYINT(1) NULL,
            `delivery_address_invoice` TINYINT(1) NULL,
            `delivery_address_prepayment` TINYINT(1) NULL,
            `device_fingerprint_snippet_id` VARCHAR(32) NULL,
            `eligibility_device_fingerprint` TINYINT(1) NULL,
            `eligibility_ratepay_elv` TINYINT(1) NULL,
            `eligibility_ratepay_installment` TINYINT(1) NULL,
            `eligibility_ratepay_invoice` TINYINT(1) NULL,
            `eligibility_ratepay_pq_full` TINYINT(1) NULL,
            `eligibility_ratepay_pq_light` TINYINT(1) NULL,
            `eligibility_ratepay_prepayment` TINYINT(1) NULL,
            `interest_rate_merchant_towards_bank` DOUBLE NULL,
            `interestrate_default` DOUBLE NULL,
            `interestrate_max` DOUBLE NULL,
            `interestrate_min` DOUBLE NULL,
            `min_difference_dueday` TINYINT(2) NULL,
            `month_allowed` VARCHAR(32) NULL,
            `month_longrun` TINYINT(2) NULL,
            `month_number_max` TINYINT(2) NULL,
            `month_number_min` TINYINT(2) NULL,
            `payment_amount` DOUBLE NULL,
            `payment_firstday` TINYINT(2) NULL,
            `payment_lastrate` DOUBLE NULL,
            `rate_min_longrun` DOUBLE NULL,
            `rate_min_normal` DOUBLE NULL,
            `service_charge` DOUBLE NULL,
            `tx_limit_elv_max` DOUBLE NULL,
            `tx_limit_elv_min` DOUBLE NULL,
            `tx_limit_installment_max` DOUBLE NULL,
            `tx_limit_installment_min` DOUBLE NULL,
            `tx_limit_invoice_max` DOUBLE NULL,
            `tx_limit_invoice_min` DOUBLE NULL,
            `tx_limit_prepayment_max` DOUBLE NULL,
            `tx_limit_prepayment_min` DOUBLE NULL,
            `valid_payment_firstdays` TINYINT(2) NULL;";
            $db->exec($sql);
        }
    }

    /**
     * check if transaction id exist in api log table
     * @return bool
     */
    public function payoneApiLogTransactionIdExist()
    {
        $DBConfig = Shopware()->Db()->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_api_log'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='transaction_id';";
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            return false;
        }

        return true;
    }

    /**
     * adding new field to database
     */
    public function extendPayoneApiLogTransactionId()
    {
        $sql = "ALTER TABLE `s_plugin_mopt_payone_api_log` "
            . "ADD COLUMN transaction_id VARCHAR(255);";
        Shopware()->Db()->exec($sql);
    }

    public function checkAndUpdateConsumerscoreExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='consumerscore_check_mode_b2b'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` ADD `consumerscore_check_mode_b2b` VARCHAR(4) NOT NULL DEFAULT 'NO';";
            $db->exec($sql);
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` CHANGE `consumerscore_check_mode` `consumerscore_check_mode_b2c` VARCHAR(4);";
            $db->exec($sql);
        }
    }

    public function checkAndUpdateSendOrderNumberAsReferenceExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='send_ordernumber_as_reference'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` ADD `send_ordernumber_as_reference` TINYINT(1) NULL DEFAULT 1;";
            $db->exec($sql);
        }
    }

    public function checkAndUpdateTransLoggingExtension()
    {
        $db = Shopware()->Db();

        $DBConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='trans_logging'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` ADD `trans_logging` TINYINT(1) NOT NULL DEFAULT 0;";
            $db->exec($sql);
        }
    }

    /**
     * check if amazon configuration is already extended Pack station check
     *
     * @return void
     */
    public function checkAndUpdateAmazonPackStationModelExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_amazon_pay'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='pack_station_mode'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_amazon_pay` "
                . "ADD COLUMN pack_station_mode VARCHAR(50) DEFAULT 'allow';";
            $db->exec($sql);
        }

    }

    /**
     * check if paypal configuration is already extended Pack station check
     *
     * @return void
     */
    public function checkAndUpdatePayPalPackStationModelExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_paypal'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='pack_station_mode'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_paypal` "
                . "ADD COLUMN pack_station_mode VARCHAR(50) DEFAULT 'allow';";
            $db->exec($sql);

            $sql = "UPDATE s_plugin_mopt_payone_paypal SET pack_station_mode = 'allow';";
            $db->exec($sql);


        }

    }

    /**
     * Check if auto_cardtype_detection column is present and create the
     * column if it is not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddAutoCardtypeDetectionColumn()
    {
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_creditcard_config'
                AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                AND COLUMN_NAME = 'auto_cardtype_detection'";

        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_creditcard_config`
                    ADD COLUMN `auto_cardtype_detection` BOOLEAN NULL;";

            $db->exec($sql);
        }
    }

    /**
     * Checks if reminder level columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddReminderLevelColumns()
    {
        $reminderLevels = ['2', '3', '4', '5', 'A', 'S', 'M', 'I'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($reminderLevels AS $reminderLevel) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = 'state_reminder$reminderLevel'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `state_reminder$reminderLevel` INT(11) NULL;";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if ratepay global snippetid column is present and creates
     * column if not present.
     * @return void
     */
    public function checkAndAddRatepaySnippetIdColumn()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='ratepay_snippet_id'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN ratepay_snippet_id VARCHAR(50) DEFAULT 'ratepay';";
            $db->exec($sql);
        }

    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddApplepayConfig()
    {
        $textColumns = ['applepay_merchant_id', 'applepay_certificate', 'applepay_private_key', 'applepay_private_key_password'];
        $tinyIntColumns = ['applepay_visa', 'applepay_mastercard', 'applepay_girocard', 'applepay_amex', 'applepay_discover', 'applepay_debug'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }

        foreach ($tinyIntColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` TINYINT(1) NULL DEFAULT '0';";
                $db->exec($sql);
            }
        }
    }

    /**
     * check if paypal configuration is already extended Pack station check
     *
     * @return void
     */
    public function checkAndUpdatePayPalShopModelExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_paypal'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='shop_id'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_paypal` "
                . "ADD COLUMN shop_id int(11) DEFAULT 1;";
            $db->exec($sql);

            $sql = "UPDATE s_plugin_mopt_payone_paypal SET shop_id = 1;";
            $db->exec($sql);
        }

    }

    /**
     * check if paypal configuration is already extended Pack station check
     *
     * @return void
     */
    public function checkAndUpdateAmazonPayShopModelExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_amazon_pay'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='shop_id'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_amazon_pay` "
                . "ADD COLUMN shop_id int(11) DEFAULT 1;";
            $db->exec($sql);

            $sql = "UPDATE s_plugin_mopt_payone_amazon_pay SET shop_id = 1;";
            $db->exec($sql);
        }

    }

    /**
     * check if paypal configuration is already extended Pack station check
     *
     * @return void
     */
    public function checkAndUpdatePayPalDefaultModelExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_paypal'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='is_default'";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_paypal` "
                . "DROP COLUMN is_default;";
            $db->exec($sql);
        }

    }

    /**
     * check if paypal configuration is already extended lcoale
     *
     * @return void
     */
    public function checkAndRemovePayPalLocaleModelExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_paypal'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='locale_id'";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_paypal` "
                . "DROP COLUMN locale_id;";
            $db->exec($sql);
        }

    }

    /**
     * check if paypal configuration is already extended lcoale
     *
     * @return void
     */
    public function checkAndRemoveTrustlyExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='trustly_show_iban_bic'";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "DROP COLUMN trustly_show_iban_bic;";
            $db->exec($sql);
        }

    }

    /**
     * check if paypal configuration is already extended lcoale
     *
     * @return void
     */
    public function checkAndRemovePaydirektExtension()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='paydirekt_overcapture'";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "DROP COLUMN paydirekt_overcapture;";
            $db->exec($sql);
        }

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='paydirekt_order_secured'";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "DROP COLUMN paydirekt_order_secured;";
            $db->exec($sql);
        }

        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='paydirekt_preauthorization_validity'";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "DROP COLUMN paydirekt_preauthorization_validity;";
            $db->exec($sql);
        }

    }

    /**
     * @return void
     */
    public function migratePaypalSettings()
    {
        $connection = Shopware()->Container()->get('dbal_connection');
        $payoneMain = new Mopt_PayoneMain();
        /** @var Shopware\Models\Payment\Payment $paypalPayment */
        $paypalPayment = Shopware()->Models()->getRepository(Payment::class)->findOneBy(['name' => 'mopt_payone__ewallet_paypal']);
        /** @var Shopware\Models\Payment\Payment $paypalExpressPayment */
        $paypalExpressPayment = Shopware()->Models()->getRepository(Payment::class)->findOneBy(['name' => 'mopt_payone__ewallet_paypal_express']);
        if ($paypalPayment === null) {
            return;
        }

        // only enable Paypal Express if Paypal was active and Paypal Express is active in Payone Config
        $paymentActive = $paypalPayment->getActive();
        $paypalConfig = $payoneMain->getPayoneConfig($paypalPayment->getId());
        $paypalExpressActive = $paypalConfig['paypalEcsActive'];
        if ($paymentActive && $paypalExpressActive) {
            $paypalExpressPayment->setActive(true);
        }
        $paypalExpressPayment->setCountries($paypalPayment->getCountries());
        $paypalExpressPayment->setShops($paypalPayment->getShops());
        $paypalExpressPayment->setDebitPercent($paypalPayment->getDebitPercent());
        $paypalExpressPayment->setSurcharge($paypalPayment->getSurcharge());
        $paypalExpressPayment->setSurchargeString($paypalPayment->getSurchargeString());
        $paypalExpressPayment->setEsdActive($paypalPayment->getEsdActive());
        $paypalExpressPayment->setMobileInactive($paypalPayment->getMobileInactive());

        Shopware()->Models()->persist($paypalExpressPayment);
        Shopware()->Models()->flush();

        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select([
            'd.id',
        ])
            ->from('s_premium_dispatch', 'd')
            ->join('d', 's_premium_dispatch_paymentmeans', 'dp', 'd.id = dp.dispatchID AND dp.paymentID=:paymentID')
            ->where('d.active = 1');

        $queryBuilder->setParameter('paymentID', $paypalPayment->getId());
        $dispatchIds = $queryBuilder->execute()->fetchAll();
        foreach ($dispatchIds AS $dispatchID) {
            /** @var Dispatch $dispatch */
            $dispatch = Shopware()->Models()->getRepository(Dispatch::class)->findOneBy(['id' => $dispatchID]);
            $payments = $dispatch->getPayments();
            $payments->add($paypalExpressPayment);
            $dispatch->setPayments($payments);
            Shopware()->Models()->persist($dispatch);
            Shopware()->Models()->flush();
        }
    }

    /**
     * @return bool
     */
    public function checkPaypalMigration()
    {
        $connection = Shopware()->Container()->get('dbal_connection');
        /** @var Shopware\Models\Payment\Payment $paypalExpressPayment */
        $paypalExpressPayment = Shopware()->Models()->getRepository(Payment::class)->findOneBy(['name' => 'mopt_payone__ewallet_paypal_express']);
        if ($paypalExpressPayment === null) {
            return false;
        }

        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select([
            'd.id',
        ])
            ->from('s_premium_dispatch', 'd')
            ->join('d', 's_premium_dispatch_paymentmeans', 'dp', 'd.id = dp.dispatchID AND dp.paymentID=:paymentID')
            ->where('d.active = 1');

        $queryBuilder->setParameter('paymentID', $paypalExpressPayment->getId());
        $dispatchIds = $queryBuilder->execute()->fetchColumn(0);
        return empty($dispatchIds) ? true : false;
    }

    /**
     * check option allowDifferentAdresses for bnpl payments
     *
     * @return void
     */
    public function checkAndUpdateAllowDifferentAdressesOption()
    {
        $db = Shopware()->Db();
        $DBConfig = $db->getConfig();
        $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='" . $DBConfig['dbname'] . "'
                AND COLUMN_NAME ='allow_different_addresses'";
        $result = $db->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
                . "ADD COLUMN allow_different_addresses TINYINT(1) NULL DEFAULT '0';";
            $db->exec($sql);
        }

    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddCreditcardDefaultDescription()
    {
        $textColumns = ['creditcard_default_description'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if paypalExressUseDefaultShipping columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddPaypalExpressUseDefaultShipping()
    {
        $textColumns = ['paypal_express_use_default_shipping'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` TINYINT(1) NOT NULL DEFAULT 0;";
                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if paypalExressUseDefaultShipping columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddPaypalV2ShowButton()
    {
        $textColumns = ['paypal_v2_show_button'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` TINYINT(1) NOT NULL DEFAULT 0;";
                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddPaypalV2MerchantId()
    {
        $textColumns = ['paypal_v2_merchant_id'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddPaypalV2ButtonColor()
    {
        $textColumns = ['paypal_v2_button_color'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddPaypalV2ButtonShape()
    {
        $textColumns = ['paypal_v2_button_shape'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddGooglePayButtonOptions()
    {
        $textColumns = ['googlepay_button_type', 'googlepay_button_color'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddGooglePayMerchantId()
    {
        $textColumns = ['googlepay_merchant_id'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT '';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if applepay columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddGooglePayCountryCode()
    {
        $textColumns = ['googlepay_country_code'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` VARCHAR(255) NULL DEFAULT 'DE';";

                $db->exec($sql);
            }
        }
    }

    /**
     * Checks if paypalExressUseDefaultShipping columns are present and creates
     * columns if not present.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function checkAndAddGooglePayAllowCardOptions()
    {
        $textColumns = ['googlepay_allow_visa', 'googlepay_allow_master_card', 'googlepay_allow_credit_cards', 'googlepay_allow_prepaid_cards'];
        $db = Shopware()->Db();
        $dbConfig = $db->getConfig();

        foreach ($textColumns AS $column) {
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                    AND TABLE_SCHEMA = '{$dbConfig['dbname']}'
                    AND COLUMN_NAME = '$column'";

            $result = $db->query($sql);

            if ($result->rowCount() === 0) {
                $sql = "ALTER TABLE `s_plugin_mopt_payone_config`
                        ADD COLUMN `$column` TINYINT(1) NOT NULL DEFAULT 1;";
                $db->exec($sql);
            }
        }
    }
}

