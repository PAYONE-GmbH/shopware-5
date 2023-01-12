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

function fcpoSelectBNPLInstallmentPlan(iIndex) {
    var oRadio = document.getElementById('bnplPlan_' + iIndex);
    if (oRadio) {
        oRadio.checked = true;
    }
    var oDetailsList = document.getElementsByClassName('bnpl_installment_overview');
    for (var i = 0 ; i < oDetailsList.length ; i++) {
        var oElement = oDetailsList[i];

        if (oElement.id === 'bnpl_installment_overview_' + iIndex) {
            oElement.style.display = 'block';
        } else {
            oElement.style.display = 'none';
        }
    }
}
