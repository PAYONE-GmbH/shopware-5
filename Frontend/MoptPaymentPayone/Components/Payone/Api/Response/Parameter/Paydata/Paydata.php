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
class Payone_Api_Response_Parameter_Paydata_Paydata extends Payone_Api_Response_Parameter_Abstract
{

    const PAYDATA_PARAM = 'add_paydata';
    
    /**
     * @var Payone_Api_Response_Parameter_Paydata_DataItem[]
     */
    protected $items = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }

    /**
     * @param array $data
     */
    public function init(array $data = array())
    {

        $addPaydata = array();

        foreach ($data as $key => $value) {
            $part = explode('[', $key);
            if (($part[0] === self::PAYDATA_PARAM) && (count($part) > 1)) {
                $k = trim($part[1], ']');

                $dataItem = new Payone_Api_Response_Parameter_Paydata_DataItem(array('key' => $k, 'data' => $value));

                $addPaydata[] = $dataItem;
            }
        }

        if (count($addPaydata) > 0) {
            $this->setItems($addPaydata);
        }
    }

    public function toArray()
    {
        $data = array(); //parent::toArray();
        /**
         * @var Payone_Api_Response_Parameter_Paydata_DataItem $item
         */
        foreach ($this->items as $item) {

            $data = array_merge($data, $item->toArray());
        }

        return $data;
    }

    /**
     * Returns paydata as assoc array
     * Array
     * (
     *      [shipping_zip] => 79111
     *      [shipping_country] => DE
     *      [shipping_state] => Empty
     *      [shipping_city] => Freiburg
     *      [shipping_street] => ESpachstr. 1
     *      [shipping_firstname] => Max
     *      [shipping_lastname] => Mustermann
     * )
     *
     * @return array
     */
    public function toAssocArray()
    {
        $tmp = array();

        foreach ($this->getItems() as $item) {
            $tmp[$item->getKey()] = $item->getData();
        }
        return $tmp;
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return count($this->items) ? true : false;
    }

    /**
     * @param Payone_Api_Response_Parameter_Paydata_DataItem $item
     */
    public function addItem(Payone_Api_Response_Parameter_Paydata_DataItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @param Payone_Api_Response_Parameter_Paydata_DataItem[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return Payone_Api_Response_Parameter_Paydata_DataItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
