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
class Payone_Api_Request_Consumerscore extends Payone_Api_Request_Abstract
{
    protected $request = Payone_Api_Enum_RequestType::CONSUMERSCORE;

    /**
     * @var int
     */
    protected $aid = null;
    /**
     * @var string
     */
    protected $addresschecktype = null;
    /**
     * @var string
     */
    protected $consumerscoretype = null;
    /**
     * @var string
     */
    protected $firstname = null;
    /**
     * @var string
     */
    protected $lastname = null;
    /**
     * @var string
     */
    protected $company = null;
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
     * @var string
     */
    protected $country = null;
    /**
     * @var string
     */
    protected $birthday = null;
    /**
     * @var string
     */
    protected $telephonenumber = null;
    /**
     * @var string
     */
    protected $language = null;

    /**
     * @param string $addresschecktype
     */
    public function setAddresschecktype($addresschecktype)
    {
        $this->addresschecktype = $addresschecktype;
    }

    /**
     * @return string
     */
    public function getAddresschecktype()
    {
        return $this->addresschecktype;
    }

    /**
     * @param int $aid
     */
    public function setAid($aid)
    {
        $this->aid = $aid;
    }

    /**
     * @return int
     */
    public function getAid()
    {
        return $this->aid;
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
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $consumerscoretype
     */
    public function setConsumerscoretype($consumerscoretype)
    {
        $this->consumerscoretype = $consumerscoretype;
    }

    /**
     * @return string
     */
    public function getConsumerscoretype()
    {
        return $this->consumerscoretype;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
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
     * @param string $telephonenumber
     */
    public function setTelephonenumber($telephonenumber)
    {
        $this->telephonenumber = $telephonenumber;
    }

    /**
     * @return string
     */
    public function getTelephonenumber()
    {
        return $this->telephonenumber;
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
