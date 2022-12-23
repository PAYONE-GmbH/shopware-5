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
 * @subpackage      Mapper
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Mapper
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
abstract class Payone_Api_Mapper_Request_Abstract extends Payone_Api_Mapper_Abstract
{
    /**
     * @var Payone_Api_Mapper_Currency_Interface
     */
    protected $mapperCurrency = null;

    /**
     * @param Payone_Api_Mapper_Currency_Interface $mapperCurrency
     */
    public function setMapperCurrency(Payone_Api_Mapper_Currency_Interface $mapperCurrency)
    {
        $this->mapperCurrency = $mapperCurrency;
    }

    /**
     * @return Payone_Api_Mapper_Currency_Interface
     */
    public function getMapperCurrency()
    {
        return $this->mapperCurrency;
    }
}
