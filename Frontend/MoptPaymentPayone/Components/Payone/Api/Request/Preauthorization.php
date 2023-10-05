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
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Api_Request_Preauthorization extends Payone_Api_Request_Authorization_Abstract
{
    protected $request = Payone_Api_Enum_RequestType::PREAUTHORIZATION;
    protected $api_version = null;
    protected $cashtype = null;
    protected $financingtype = null;
    protected $birthday = null;
    
    
    
    public function getApiVersion()
    {
        return $this->api_version;
    }

    public function getCashtype()
    {
        return $this->cashtype;
    }

    public function setApiVersion($api_version)
    {
        $this->api_version = $api_version;
    }

    public function setCashtype($cashtype)
    {
        $this->cashtype = $cashtype;
    }
    
    /**
     * @param string $financingtype
     */
    public function setFinancingtype($financingtype)
    {
        $this->financingtype = $financingtype;
    }

    /**
     * @return string
     */
    public function getFinancingtype()
    {
        return $this->financingtype;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }
}
