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
class Payone_Api_Response_Preauthorization_Approved extends Payone_Api_Response_Authorization_Abstract {

    /**
     * add_paydata[workorderid] = workorderid from payone
     * add_paydata[...] = delivery data
     * @var Payone_Api_Response_Parameter_Paydata_Paydata
     */
    protected $paydata = NULL;
    /**
     * @param array $params
     */
    function __construct(array $params = array()) {
        parent::__construct($params);

        $this->setRawResponse($params);
        $this->initPaydata($params);
    }

    protected function initPaydata($param) {

        $payData = new Payone_Api_Response_Parameter_Paydata_Paydata($param);

        if ($payData->hasItems()) {
            $this->setPaydata($payData);
        } else {
            $this->setPaydata(NULL);
        }
    }


    /**
     * usage:
     * $request = new Payone_Api_Request_Preauthorization(array_merge( $your_accountData, $requestData));
     * $builder = $this->getPayoneBuilder();
     *
     * $service = $builder->buildServicePaymentPreauthorize();
     * $response = $service->request($request);
     * print_r($response->getPaydata()->toAssocArray());
     * 
     * you get an array like that:
     * 
     * Array
     * (
     *      [content_encoding]=>UTF-8
     *      [instruction_notes]=> "content"
     *      [content_format]=>HTML
     * )
     * 
     * @return Payone_Api_Response_Parameter_Paydata_Paydata
     */
    public function getPaydata() {
        return $this->paydata;
    }

    /**
     * @param Payone_Api_Response_Parameter_Paydata_Paydata $paydata
     */
    public function setPaydata($paydata) {
        $this->paydata = $paydata;
    }
}
