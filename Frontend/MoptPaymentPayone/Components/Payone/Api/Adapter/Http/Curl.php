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
 * @subpackage      Adapter
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Adapter
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Api_Adapter_Http_Curl extends Payone_Api_Adapter_Http_Abstract
{
    /**
     * @return array
     * @throws Payone_Api_Exception_InvalidResponse
     */
    protected function doRequest()
    {
        $response = array();

        $testmode = true;
        $capture_testmode = $testmode && $this->params['request'] === 'capture';
        $preauthorization_testmode = $testmode && $this->params['request'] === 'preauthorization';
        $genericpayment_testmode = $testmode && $this->params['request'] === 'genericpayment';
        $compareRequests = $testmode && false;

        if ($capture_testmode) {
            $this->changeCaptureRequest();
        }

        if ($preauthorization_testmode) {
            $this->changePreAuthRequest();
        }

        if ($genericpayment_testmode) {
            $this->changeGenericpaymentRequest();
        }

        $urlArray = $this->generateUrlArray();

        $urlHost = $urlArray['host'];
        $urlPath = isset($urlArray['path']) ? $urlArray['path'] : '';
        $urlScheme = $urlArray['scheme'];
        $urlQuery = $urlArray['query'];

        $curl = curl_init($urlScheme . "://" . $urlHost . $urlPath);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $urlQuery);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);

        $result = curl_exec($curl);

        $this->setRawResponse($result);

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            throw new Payone_Api_Exception_InvalidResponse();
        } elseif (curl_error($curl)) {
            $response[] = "errormessage=" . curl_errno($curl) . ": " . curl_error($curl);
        } else {
            $response = explode("\n", $result);
        }
        curl_close($curl);

        if ($compareRequests) {
            $this->compareRequests($this->params, $response);
        }

        if ($testmode) {
            $this->dumpCall($this->params, $response);
        }

        return $response;
    }

    protected function changeCaptureRequest()
    {
        // 'language' lasse ich erst mal weg, da es nicht gefordert ist
//        $this->params['language'] = 'de';

        unset($this->params['add_paydata[capturemode]']);
        unset($this->params['booking_date']);
        unset($this->params['data']);
        unset($this->params['document_date']);
        unset($this->params['due_time']);
        unset($this->params['ed[1]']);
        unset($this->params['ed[2]']);
        unset($this->params['invoice_deliverydate']);
        unset($this->params['invoice_deliveryenddate']);
        unset($this->params['invoice_deliverymode']);
        unset($this->params['invoiceappendix']);
        unset($this->params['invoiceid']);
        unset($this->params['sd[1]']);
        unset($this->params['sd[2]']);
        unset($this->params['sdk_type']);
        unset($this->params['sdk_version']);

        // hier muss glaube ich nur 'shipment' stehen. Dafür gibt immerhin Konstanten: Payone_Api_Enum_InvoicingItemType::SHIPMENT
//        $this->params['id[2]'] = 'ship9';
    }

    protected function changePreAuthRequest() {
//        $this->params['add_paydata[shipping_title]'] = 'Herr';
//        $this->params['add_paydata[shipping_telephonenumber]'] = '01522113356';
        // 'title' lasse ich erst mal weg, da es nicht gefordert ist
//        $this->params['title'] = 'Herr';

        unset($this->params['ed[1]']);
        unset($this->params['ed[2]']);
        unset($this->params['sd[1]']);
        unset($this->params['sd[2]']);
    }

    protected function changeGenericpaymentRequest() {
//        $this->params['add_paydata[shipping_title]'] = 'Herr';
//        $this->params['add_paydata[shipping_telephonenumber]'] = '01522113356';
//        $this->params['telephonenumber'] = '01522113356';
//        $this->params['title'] = 'Herr';

        unset($this->params['ed[1]']);
        unset($this->params['ed[2]']);
        unset($this->params['sd[1]']);
        unset($this->params['sd[2]']);
    }

    protected function dumpCall($request, $response) {
        $requestName = $request['request'];
        file_put_contents("/var/www/sw564/var/log/log_$requestName.json", json_encode([
            'request' => $request,
            'response' => $response
        ], JSON_UNESCAPED_UNICODE));
    }

    protected function compareRequests($requestParams, $response)
    {
        $testRequest = [
            'amount'             => '63300',
            'capturemode'        => 'completed',
            'currency'           => 'EUR',
            'de[1]'              => 'Kite CORE GT',
            'de[2]'              => 'Aufschlag Versandkosten',
            'encoding'           => 'UTF-8',
            'id[1]'              => '1205',
            'id[2]'              => 'delivery',
            'integrator_name'    => 'oxid',
            'integrator_version' => 'CE4.10.8',
            'it[1]'              => 'goods',
            'it[2]'              => 'shipment',
            'key'                => 'c09edb322d1d301746c71105a50affca',
            'language'           => 'de',
            'mid'                => '17096',
            'mode'               => 'test',
            'no[1]'              => '1',
            'no[2]'              => '1',
            'portalid'           => '2023381',
            'pr[1]'              => '62910',
            'pr[2]'              => '390',
            'request'            => 'capture',
            'sequencenumber'     => '1',
            'settleaccount'      => 'auto',
            'solution_name'      => 'fatchip',
            'solution_version'   => '2.4.0',
            'txid'               => '420860693',
            'va[1]'              => '1900',
            'va[2]'              => '1900',
        ];

        echo "<table>\n";

        if ($response[0] === 'status=ERROR') {
            $row_color = 'red';
        } else {
            $row_color = 'lightgreen';
        }
        foreach ($response as $value) {
            [$key, $item] = explode('=', $value);
            if ($item === '') {
                continue;
            }
            echo "<tr style='background-color: ${row_color}'>\n";
            echo '<td>'.$key.':</td><td>'.$item."</td>\n";
            echo "</tr>\n";
        }
        echo "<tr><td><br></td></tr>";

        foreach ($testRequest as $key => $value) {
            echo "<tr style='background-color: #3adfba'>\n";
            echo '<td>' . $key . ':</td><td>' . $value . "</td>\n";
            echo "</tr>\n";

            echo "<tr style='background-color: #3adfba'>\n";
            echo '<td>' . $key . ':</td><td>' . $requestParams[$key] . "</td>\n";
            echo "</tr>\n";

            echo "<tr><td><br></td></tr>";
        }

        foreach ($requestParams as $key => $request_param) {
            if (array_key_exists($key, $testRequest)) {
                continue;
            }

            echo "<tr style='background-color: #3adfba'>\n";
            echo '<td>' . $key . ':</td><td>' . $requestParams[$key] . "</td>\n";
            echo "</tr>\n";

            echo "<tr><td><br></td></tr>";
        }

        echo "</table>\n";

        var_dump($requestParams);
        die('test');
    }
}
