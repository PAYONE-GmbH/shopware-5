/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone_Core to newer
 * versions in the future. If you wish to customize Payone_Core for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         js
 * @subpackage      payone
 * @copyright       Copyright (c) 2016 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Robert Müller <robert.mueller@fatchip.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.fatchip.com
 *
 *
 * @category        Payone
 * @package         js
 * @subpackage      payone
 * @copyright       Copyright (c) 2016 <support@e3n.de> - www.e3n.de
 * @author          Tim Rein <tim.rein@e3n.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.e3n.de
 */

/**
 *
 * @param mode
 * @param paymentMethod
 * @param url
 */
function switchRateOrRuntime(mode, paymentMethod)
{
    if (mode == 'rate') {
        document.getElementById(paymentMethod + '_SwitchToTerm').className = 'ratepay-Active';
        document.getElementById(paymentMethod + '_SwitchToRuntime').className = '';
        document.getElementById(paymentMethod + '_ContentTerm').style.display = 'block';
        document.getElementById(paymentMethod + '_ContentRuntime').style.display = 'none';
    } else if (mode == 'runtime') {
        document.getElementById(paymentMethod + '_SwitchToRuntime').className = 'ratepay-Active';
        document.getElementById(paymentMethod + '_SwitchToTerm').className = '';
        document.getElementById(paymentMethod + '_ContentRuntime').style.display = 'block';
        document.getElementById(paymentMethod + '_ContentTerm').style.display = 'none';
    }
}

/**
 *
 * @param mode
 * @param paymentMethod
 * @param url
 */
function ratepayRateCalculatorAction(mode, paymentMethod, url)
{
    var calcValue,
        calcMethod,
        notification,
        html,
        ratePayshopId,
        amount,
        ratePayCurrency,
        dob


    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    amount = document.getElementById('mopt_payone__ratepay_amount').value;
    ratePayshopId = document.getElementById('ratePayShopId').value;
    ratePayCurrency = document.getElementById('ratePayCurrency').value;
    dob = document.getElementById('mopt_payone__ratepay_installment_birthdaydate').value;
    if (mode == 'rate') {
        calcValue = document.getElementById(paymentMethod + '-rate').value;


        calcMethod = 'calculation-by-rate';
        if (document.getElementById('debitSelect')) {
            dueDate = document.getElementById('debitSelect').value;
        } else {
            dueDate= '';
        }
    } else if (mode == 'runtime') {
        calcValue = document.getElementById(paymentMethod + '-runtime').value;
        calcMethod = 'calculation-by-time';
        notification = (document.getElementById(paymentMethod + '_Notification') == null) ? 0 : 1;
        if(document.getElementById('debitSelectRuntime')){
            dueDate = document.getElementById('debitSelectRuntime').value;
        } else {
            dueDate= '';
        }
    }

    xmlhttp.open("POST", url, false);

    xmlhttp.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded"
    );

    xmlhttp.send(
        "paymentMethod=" + paymentMethod + "&calcValue=" + calcValue + "&calcMethod=" + calcMethod + "&dueDate=" + dueDate
                 + "&notification=" + notification
                 + "&ratePayshopId=" + ratePayshopId + "&ratePayCurrency=" + ratePayCurrency + "&amount=" + amount
                 + "&dob=" + dob
    );

    if (xmlhttp.responseText != null) {
        html = xmlhttp.responseText;
        document.getElementById(paymentMethod + '_ResultContainer').innerHTML = html;
        document.getElementById(paymentMethod + '_ResultContainer').style.display = 'block';
        document.getElementById(paymentMethod + '_ResultContainer').style.padding = '3px 0 0 0';
        document.getElementById(paymentMethod + '_SwitchToTerm').style.display = 'none';
    }

}

/**
 * @param element
 */
function payoneSwitchPayRate(element)
{
    if(element.value === 'RPS'){
        $("ratepay-main-cont").setStyle(
            {
            display: 'block'
            }
        );
    } else {
        $("ratepay-main-cont").setStyle(
            {
            display: 'none'
            }
        );
    }
}
