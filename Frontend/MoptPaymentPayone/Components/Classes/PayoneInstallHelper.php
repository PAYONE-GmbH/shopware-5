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

    /**
     * @var array
     */
    private $types = [
        self::TYPE_STRING   => [
            'sql' => 'VARCHAR(500)',
            'dbal' => 'string',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => true,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_TEXT     => [
            'sql' => 'TEXT',
            'dbal' => 'text',
            'allowDefaultValue' => false,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_HTML     => [
            'sql' => 'MEDIUMTEXT',
            'dbal' => 'text',
            'allowDefaultValue' => false,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'string']
        ],
        self::TYPE_INTEGER  => [
            'sql' => 'INT(11)',
            'dbal' => 'integer',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'long']
        ],
        self::TYPE_FLOAT    => [
            'sql' => 'DOUBLE',
            'dbal' => 'float',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'double']
        ],
        self::TYPE_BOOLEAN  => [
            'sql' => 'INT(1)',
            'dbal' => 'boolean',
            'allowDefaultValue' => true,
            'quoteDefaultValue' => false,
            'elastic' => ['type' => 'boolean']
        ],
        self::TYPE_DATE     => [
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
     * - intended to be used with Shopware version >= 5.2.0
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
                'consumerscore_result'              => 'string',
                'consumerscore_date'                => 'date',
                'consumerscore_color'               => 'string',
                'consumerscore_value'               => 'integer',
            ],
            's_user_billingaddress_attributes' => [
                'addresscheck_result'               => 'string',
                'addresscheck_date'                 => 'date',
                'addresscheck_personstatus'         => 'string',
                'consumerscore_result'              => 'string',
                'consumerscore_date'                => 'date',
                'consumerscore_color'               => 'string',
                'consumerscore_value'               => 'integer',
            ],
            's_user_shippingaddress_attributes' => [
                'addresscheck_result'               => 'string',
                'addresscheck_date'                 => 'date',
                'addresscheck_personstatus'         => 'string',
                'consumerscore_color'               => 'string',
                'consumerscore_value'               => 'integer',
            ],
            's_order_attributes' => [
                'txid'                              => 'integer',
                'status'                            => 'string',
                'sequencenumber'                    => 'integer',
                'is_authorized'                     => 'boolean',
                'is_finally_captured'               => 'boolean',
                'clearing_data'                     => 'text',
                // since 2.1.4 - save shipping cost with order
                'ship_captured'                     => ['float',
                    [
                        'label' => 'Versandkosten bisher eingezogen:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                'ship_debit'                        => ['float',
                    [
                        'label' => 'Versandkosten bisher gutgeschrieben:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                // since 2.3.0 - save payment data for abo commerce support
                'payment_data'                      => 'text',
                // since 2.5.2 - save order hash and payment reference
                'payment_reference'                 => 'string',
                'order_hash'                        => 'string',
                // since 3.3.8 - Payolution Payment Order extensions
                'payolution_workorder_id'           => ['string',
                    [
                        'label' => 'Workorder ID:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
                'payolution_clearing_reference'     => ['string',
                    [
                        'label' => 'Clearing Reference:',
                        'helpText' => '',
                        'displayInBackend' => true,
                        'pluginId' => $pluginId
                    ]
                ],
            ],
            's_order_details_attributes' => [
                'payment_status'                    => 'string',
                'shipment_date'                     => 'date',
                'captured'                          => 'float',
                'debit'                             => 'float',
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
        return array(
            array(
                'name' => 'mopt_payone__cc_visa',
                'description' => 'PAYONE Visa',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 1,),
            array(
                'name' => 'mopt_payone__cc_mastercard',
                'description' => 'PAYONE Mastercard',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 2,),
            array(
                'name' => 'mopt_payone__cc_american_express',
                'description' => 'PAYONE American Express',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 3,),
            array(
                'name' => 'mopt_payone__cc_carte_blue',
                'description' => 'PAYONE Carte Blue',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 4,),
            array(
                'name' => 'mopt_payone__cc_diners_club',
                'description' => 'PAYONE Diners Club',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 5,),
            array(
                'name' => 'mopt_payone__cc_discover',
                'description' => 'PAYONE Discover',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 6,),
            array(
                'name' => 'mopt_payone__cc_jcb',
                'description' => 'PAYONE JCB',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 7,),
            array(
                'name' => 'mopt_payone__cc_maestro_international',
                'description' => 'PAYONE Maestro International',
                'template' => 'mopt_paymentmean_creditcard.tpl',
                'position' => 8,),
            array(
                'name' => 'mopt_payone__ibt_sofortueberweisung',
                'description' => 'PAYONE SOFORT Überweisung',
                'template' => 'mopt_paymentmean_sofort.tpl',
                'position' => 9,),
            array(
                'name' => 'mopt_payone__ibt_giropay',
                'description' => 'PAYONE Giropay',
                'template' => 'mopt_paymentmean_giropay.tpl',
                'position' => 10,),
            array(
                'name' => 'mopt_payone__ibt_eps',
                'description' => 'PAYONE eps',
                'template' => 'mopt_paymentmean_eps.tpl',
                'position' => 11,),
            array(
                'name' => 'mopt_payone__ibt_post_efinance',
                'description' => 'PAYONE Post-Finance EFinance',
                'template' => null,
                'position' => 12,),
            array(
                'name' => 'mopt_payone__ibt_post_finance_card',
                'description' => 'PAYONE Post-Finance Card',
                'template' => null,
                'position' => 13,),
            array(
                'name' => 'mopt_payone__ibt_ideal',
                'description' => 'PAYONE iDeal',
                'template' => 'mopt_paymentmean_ideal.tpl',
                'position' => 14,),
            array(
                'name' => 'mopt_payone__ewallet_paypal',
                'description' => 'PAYONE PayPal',
                'template' => null,
                'position' => 15,),
            array(
                'name' => 'mopt_payone__acc_debitnote',
                'description' => 'PAYONE Lastschrift',
                'template' => 'mopt_paymentmean_debit.tpl',
                'position' => 16,),
            array(
                'name' => 'mopt_payone__acc_invoice',
                'description' => 'PAYONE Offene Rechnung',
                'template' => null,
                'position' => 17,),
            array(
                'name' => 'mopt_payone__acc_payinadvance',
                'description' => 'PAYONE Vorkasse',
                'template' => null,
                'position' => 18,),
            array(
                'name' => 'mopt_payone__acc_cashondel',
                'description' => 'PAYONE Nachnahme',
                'template' => null,
                'position' => 19,),
            array(
                'name' => 'mopt_payone__fin_billsafe',
                'description' => 'PAYONE BillSAFE',
                'template' => null,
                'position' => 20,),
            array(
                'name' => 'mopt_payone__fin_klarna',
                'description' => 'PAYONE Klarna',
                'template' => 'mopt_paymentmean_klarna.tpl',
                'position' => 21,),
            array(
                'name' => 'mopt_payone__ibt_p24',
                'description' => 'PAYONE P24',
                'template' => null,
                'position' => 22,),
            array(
                'name' => 'mopt_payone__creditcard_iframe',
                'description' => 'PAYONE Kreditkarte',
                'template' => null,
                'position' => 23,),
            array(
                'name' => 'mopt_payone__csh_barzahlen',
                'description' => 'PAYONE Barzahlen',
                'template' => null,
                'position' => 24,
                'additionalDescription' => '<label for="payment_barzahlen">'
                . '  <img style="height: 3em; vertical-align: -1em;" src="https://cdn.barzahlen.de/images/barzahlen_logo.png" alt="Barzahlen">'
                . '</label>'
                . '<img style="float: right; margin-left: 10px; max-width: 180px; max-height: 180px;" src="https://cdn.barzahlen.de/images/barzahlen_special.png">'
                . 'Mit Abschluss der Bestellung bekommen Sie einen Zahlschein angezeigt, den Sie sich ausdrucken oder auf Ihr Handy schicken lassen können. Bezahlen Sie den Online-Einkauf mit Hilfe des Zahlscheins an der Kasse einer Barzahlen-Partnerfiliale.<br/><br/>'
                . '<strong>Bezahlen Sie bei:</strong>'
                . ' '
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_01.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_02.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_03.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_04.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_05.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_06.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_07.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_08.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_09.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'
                . '<img src="https://cdn.barzahlen.de/images/barzahlen_partner_10.png" style="height: 1em; vertical-align: -0.1em; display: initial;">'),
            array(
                'name' => 'mopt_payone__ewallet_paydirekt',
                'description' => 'PAYONE Paydirekt',
                'template' => null,
                'position' => 25,),
            array(
                'name' => 'mopt_payone__fin_payolution_invoice',
                'description' => 'PAYONE Payolution Rechnungskauf',
                'template' => 'mopt_paymentmean_payolution_invoice.tpl',
                'position' => 26,),
            array(
                'name' => 'mopt_payone__fin_payolution_debitnote',
                'description' => 'PAYONE Payolution Lastschrift',
                'template' => 'mopt_paymentmean_payolution_debitnote.tpl',
                'position' => 27,),
            array(
                'name' => 'mopt_payone__fin_payolution_installment',
                'description' => 'PAYONE Payolution Ratenzahlung',
                'template' => 'mopt_paymentmean_payolution_installment.tpl',
                'position' => 28,),
            array(
                'name' => 'mopt_payone__fin_ratepay_invoice',
                'description' => 'PAYONE Ratepay Rechnungskauf',
                'template' => 'mopt_paymentmean_ratepay_invoice.tpl',
                'position' => 29,),
            array(
                'name' => 'mopt_payone__fin_ratepay_installment',
                'description' => 'PAYONE Ratepay Ratenkauf',
                'template' => 'mopt_paymentmean_ratepay_installment.tpl',
                'position' => 30,),
            array(
                'name' => 'mopt_payone__fin_ratepay_direct_debit',
                'description' => 'PAYONE Ratepay Lastschrift',
                'template' => 'mopt_paymentmean_ratepay_direct_debit.tpl',
                'position' => 31,),
            array(
                'name' => 'mopt_payone__acc_payone_safe_invoice',
                'description' => 'PAYONE Rechnung mit Zahlungsgarantie',
                'template' => null,
                'position' => 32,),
        );
    }

    /**
     * add payment data table
     */
    public function moptCreatePaymentDataTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_payment_data` (`userId` int(11) NOT NULL,`moptPaymentData`"
                . " text NOT NULL, PRIMARY KEY (`userId`))";
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
        $companyName = unserialize((string) $serializedCompanyName);
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
     * insert document extensions (used to display additional PAYONE data) if not already stored in DB
     */
    public function moptInsertDocumentsExtensionIntoDatabaseIfNotExist()
    {
        $sql = 'SELECT * FROM s_core_documents_box WHERE name = ? OR name = ?';
        $result = Shopware()->Db()->query($sql, array('PAYONE_Footer', 'PAYONE_Content_Info'));

        if ($result->rowCount() < 2) {
            // add PAYONE block for documents
            $sql = "INSERT INTO `s_core_documents_box` (`documentID`, `name`, `style`, `value`) VALUES
	(1, 'PAYONE_Footer', 'width: 170mm;\r\nposition:fixed;\r\nbottom:-20mm;\r\nheight: 15mm;', ?),
	(1, 'PAYONE_Content_Info', ?, ?);";
            Shopware()->Db()->query($sql, array(
                '<table style="height: 90px;" border="0" width="100%">'
                . '<tbody>'
                . '<tr valign="top">'
                . '<td style="width: 33%;">'
                . '<p><span style="font-size: xx-small;">Demo GmbH</span></p>'
                . '<p><span style="font-size: xx-small;">Steuer-Nr <br />UST-ID: <br />Finanzamt '
                . '</span><span style="font-size: xx-small;">Musterstadt</span></p>'
                . '</td>'
                . '<td style="width: 33%;">'
                . '<p><span style="font-size: xx-small;">AGB<br /></span></p>'
                . '<p><span style="font-size: xx-small;">Gerichtsstand ist Musterstadt<br />'
                . 'Erf&uuml;llungsort Musterstadt</span></p>'
                . '</td>'
                . '<td style="width: 33%;">'
                . '<p><span style="font-size: xx-small;">Gesch&auml;ftsf&uuml;hrer</span></p>'
                . '<p><span style="font-size: xx-small;">Max Mustermann</span></p>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>',
                '.payment_instruction, .payment_instruction td, .payment_instruction tr {'
                . '	margin: 0;'
                . '	padding: 0;'
                . '	border: 0;'
                . '	font-size:8px;'
                . '	font: inherit;'
                . '	vertical-align: baseline;'
                . '}'
                . '.payment_note {'
                . '	font-size: 10px;'
                . '	color: #333;'
                . '}',
                '<div class="payment_note">'
                . '<br/>'
                . '{$instruction.clearing_instructionnote}<br/>'
                . '{$instruction.clearing_legalnote}}<br/><br/>'
                . '</div>'
                . '<table class="payment_instruction">'
                . '<tr>'
                . '	<td>Empfänger:</td>'
                . '	<td>{$instruction.clearing_bankaccountholder}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>Kontonr.:</td>'
                . '	<td>{$instruction.clearing_bankaccount}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>BLZ:</td>'
                . '	<td>{$instruction.clearing_bankcode}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>IBAN:</td>'
                . '	<td>{$instruction.clearing_bankiban}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>BIC:</td>'
                . '	<td>{$instruction.clearing_bankbic}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>Bank:</td>'
                . '	<td>{$instruction.clearing_bankname}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>Betrag:</td>'
                . '	<td>{$instruction.amount|currency}</td>'
                . '</tr>'
                . '<tr>'
                . '	<td>Verwendungszweck:</td>'
                . '	<td>{$instruction.clearing_reference}{$instruction.clearing_reference}</td>'
                . '</tr>'
                . '</table>'
            ));
        }
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
            $sql = "INSERT INTO `s_plugin_mopt_payone_config` (`payment_id`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `authorisation_method`, `submit_basket`, `adresscheck_active`, `adresscheck_live_mode`, `adresscheck_billing_adress`, `adresscheck_shipping_adress`, `adresscheck_automatic_correction`, `adresscheck_failure_handling`, `adresscheck_min_basket`, `adresscheck_max_basket`, `adresscheck_lifetime`, `adresscheck_failure_message`, `map_person_check`, `map_know_pre_lastname`, `map_know_lastname`, `map_not_known_pre_lastname`, `map_multi_name_to_adress`, `map_undeliverable`, `map_person_dead`, `map_wrong_adress`, `map_address_check_not_possible`, `map_address_okay_building_unknown`, `map_person_moved_address_unknown`, `map_unknown_return_value`, `consumerscore_active`, `consumerscore_live_mode`, `consumerscore_check_moment`, `consumerscore_check_mode`, `consumerscore_default`, `consumerscore_lifetime`, `consumerscore_min_basket`, `consumerscore_max_basket`, `consumerscore_failure_handling`, `consumerscore_note_message`, `consumerscore_note_active`, `consumerscore_agreement_message`, `consumerscore_agreement_active`, `consumerscore_abtest_value`, `consumerscore_abtest_active`, `payment_specific_data`, `state_appointed`, `state_capture`, `state_paid`, `state_underpaid`, `state_cancelation`, `state_refund`, `state_debit`, `state_reminder`, `state_vauthorization`, `state_vsettlement`, `state_transfer`, `state_invoice`, `check_cc`, `check_account`, `trans_appointed`, `trans_capture`, `trans_paid`, `trans_underpaid`, `trans_cancelation`, `trans_refund`, `trans_debit`, `trans_reminder`, `trans_vauthorization`, `trans_vsettlement`, `trans_transfer`, `trans_invoice`) VALUES
      (0, 0, 0, 0, '0', 0, 'Vorautorisierung', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'Es ist ein Fehler aufgetreten', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'IH', 0, 0, 0, 0, 0, '', 0, '', 0, 0, 0, 'N;', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, '', '', '', '', '', '', '', '', '', '', '', '');
      ";
            Shopware()->Db()->query($sql);
        }
        
        // insert default values for creditcard config
        $sql = 'SELECT id FROM s_plugin_mopt_payone_creditcard_config';
        $result = Shopware()->Db()->query($sql);

        if ($result->rowCount() === 0) {
            $sql = "INSERT INTO `s_plugin_mopt_payone_creditcard_config` (`error_locale_id`, `shop_id`, `show_errors`, `is_default`, `integration_type`, `standard_input_css`, `standard_input_css_selected`, `standard_iframe_height`, `standard_iframe_width`, `cardno_input_chars`, `cardno_input_chars_max`, `cardno_input_css`, `cardno_custom_iframe`, `cardno_iframe_height`, `cardno_iframe_width`, `cardno_custom_style`, `cardno_field_type`, `cardcvc_input_chars`, `cardcvc_input_chars_max`, `cardcvc_input_css`, `cardcvc_custom_iframe`, `cardcvc_iframe_height`, `cardcvc_iframe_width`, `cardcvc_custom_style`, `cardcvc_field_type`, `cardmonth_input_chars`, `cardmonth_input_chars_max`, `cardmonth_input_css`, `cardmonth_custom_iframe`, `cardmonth_iframe_height`, `cardmonth_iframe_width`, `cardmonth_custom_style`, `cardmonth_field_type`, `cardyear_input_chars`, `cardyear_input_chars_max`, `cardyear_input_css`, `cardyear_custom_iframe`, `cardyear_iframe_height`, `cardyear_iframe_width`, `cardyear_custom_style`, `cardyear_field_type`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `check_cc`, `creditcard_min_valid`) VALUES
      (74, 1, 1, 1, 0, 'box-shadow:inset 0 1px 1px #dadae5;background:#f8f8fa;border:1px solid #dadae5;border-top-color:#cbcbdb;line-height:19px;font-size:.875rem;width:85%;padding:.625rem .625rem .5625rem .625rem;color:#8798a9;','box-shadow:inset 0 1px 1px #dadae5;background:#f8f8fa;border:1px solid #dadae5;border-top-color:#cbcbdb;line-height:19px;font-size:.875rem;width:85%;padding:.625rem .625rem .5625rem .625rem;color:#8798a9;', '20px', '80px', 20, 20, '', 0, '20px', '100px', 1, 'tel', 4, 4, '', 0, '20px', '100px', 1, 'tel', 4, 4, '', 0, '20px', '40px', 1, 'select', 4, 4, '', 0, '20px', '40px', 1, 'select', 0, 0, 0, '', 0, 1, 0);
      ";
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

    public function checkAndUpdateConfigModelRatepayExtension() {
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
}
