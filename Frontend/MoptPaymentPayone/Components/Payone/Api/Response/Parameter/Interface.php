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
 * @subpackage      Response
 * @author          Ronny Schröder
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 */
interface Payone_Api_Response_Parameter_Interface
{
    /**
     * @abstract
     * @param array $data
     */
    public function init(array $data = array());

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function __toString();
}
