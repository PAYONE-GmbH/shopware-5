<?php
/**
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
 * @package         Payone_Api
 * @subpackage      Enum
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Enum
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Api_Enum_FinancingType
{
    const BSV = 'BSV'; // BILLSAFE
    const KLV = 'KLV'; // KLARNA INVOICING old
    const KIS = 'KIS'; // KLARNA INSTALLMENTS
    const KIV = 'KIV'; // KLARNA INVOICE
    const KDD = 'KDD'; // KLARNA DIRECT DEBIT
    const PYV = 'PYV'; // PAYOLUTION INVOICING
    const PYD = 'PYD'; // PAYOLUTION DEBIT
    const PYM = 'PYM'; // PAYOLUTION MONTHLY
    const PYS = 'PYS'; // PAYOLUTION INSTALLMENT
    const PPI = 'PPI'; // PAYPAL_INSTALLMENT
}
