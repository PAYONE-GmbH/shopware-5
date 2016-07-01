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
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Response
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Api_Response_AddressCheck_Valid extends Payone_Api_Response_Abstract
{
    /**
     * @var int
     */
    protected $secstatus = null;
    /**
     * @var string
     */
    protected $personstatus = null;
    /**
     * @var string
     */
    protected $street = null;
    /**
     * @var string
     */
    protected $streetname = null;
    /**
     * @var string
     */
    protected $streetnumber = null;
    /**
     * @var string
     */
    protected $zip = null;
    /**
     * @var string
     */
    protected $city = null;

    /**
     * @return bool
     */
    public function isCorrect()
    {
        if ($this->secstatus == Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_CORRECT) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isCorrectable()
    {
        if ($this->secstatus == Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_CORRECTABLE) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isNotCorrectable()
    {
        if ($this->secstatus == Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_NONE_CORRECTABLE) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $personstatus
     */
    public function setPersonstatus($personstatus)
    {
        $this->personstatus = $personstatus;
    }

    /**
     * @return string
     */
    public function getPersonstatus()
    {
        return $this->personstatus;
    }

    /**
     * @param int $secstatus
     */
    public function setSecstatus($secstatus)
    {
        $this->secstatus = $secstatus;
    }

    /**
     * @return int
     */
    public function getSecstatus()
    {
        return $this->secstatus;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $streetname
     */
    public function setStreetname($streetname)
    {
        $this->streetname = $streetname;
    }

    /**
     * @return string
     */
    public function getStreetname()
    {
        return $this->streetname;
    }

    /**
     * @param string $streetnumber
     */
    public function setStreetnumber($streetnumber)
    {
        $this->streetnumber = $streetnumber;
    }

    /**
     * @return string
     */
    public function getStreetnumber()
    {
        return $this->streetnumber;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }
}
