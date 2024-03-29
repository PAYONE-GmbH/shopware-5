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
 * @package         Payone_Enum
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Enum_ClearingType
{
    const DEBITPAYMENT = 'elv';
    const CREDITCARD = 'cc';
    const ADVANCEPAYMENT = 'vor';
    const INVOICE = 'rec';
    const ONLINEBANKTRANSFER = 'sb';
    const CASHONDELIVERY = 'cod';
    const WALLET = 'wlt';
    const FINANCING = 'fnc';
    const CASH = 'csh';
    const RATEPAY = 'fnc';
}
