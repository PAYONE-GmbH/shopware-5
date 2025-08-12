<?php

namespace Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone;

class PayoneRequest
{
    protected $hashParameters = array(
        'mid',
        'amount',
        'productid',
        'aid',
        'currency',
        'accessname',
        'portalid',
        'due_time',
        'accesscode',
        'mode',
        'storecarddata',
        'access_expiretime',
        'request',
        'checktype',
        'access_canceltime',
        'responsetype',
        'addresschecktype',
        'access_starttime',
        'reference',
        'consumerscoretype',
        'access_period',
        'userid',
        'invoiceid',
        'access_aboperiod',
        'customerid',
        'invoiceappendix',
        'access_price',
        'param',
        'invoice_deliverymode',
        'access_aboprice',
        'narrative_text',
        'eci',
        'access_vat',
        'successurl',
        'settleperiod',
        'errorurl',
        'settletime',
        'backurl',
        'vaccountname',
        'exiturl',
        'vreference',
        'clearingtype',
        'encoding',
        'api_version',
    );

    const DEFAULT_TIMEOUT = 45;

    const URL = 'https://api.pay1.de/post-gateway/';

    const PREAUTH = 'preauthorization';

    const AUTH = 'authorization';

    const GENERIC = 'genericpayment';

    public $params = [];

    protected $rawRequest = '';
    protected $rawResponse = '';


    /**
     * @param $action
     * @param array $data
     */
    public function __construct($action, array $data = array())
    {
        if (count($data) > 0) {
            $this->init($action, $data);
        }
    }

    /**
     * @param $action
     * @param array $data
     * @return void
     */
    public function init($action, array $data = array())
    {
        $this->params['request'] = $action;
        foreach ($data as $key => $value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * @param $action
     * @param $params
     * @return PayoneResponse
     */
    public function request($action, $params = null)
    {
        $this->add($params);
        $this->params['request'] = $action;
        // $this->params['currency'] = 'CHF';
        // $this->params['currency'] = 'PLN';
        // $this->params['country'] = 'BE';
        // $this->params['bankcountry'] = 'BE';
        $this->params['key'] = hash('sha384', $this->params['key']);
        $this->params['hash'] = $this->generate($this->params, $this->params['key']);
        $this->params['amount'] = (int)(round($this->params['amount'], 2) * 100);

        $this->setInvoicing();
        $this->rawRequest = $this->parseResponse($this->params);
        $responseRaw = $this->doRequest();

        $result = $this->parseResponse($responseRaw);

        $response = new PayoneResponse($result);
        $response->setRawResponse($result);

        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog');
        $repository->save($this, $response);

        return $response;
    }

    /**
     * @return array
     */
    protected function generateUrlArray()
    {
        $urlRequest = self::URL . '?' . http_build_query($this->getParams(), null, '&');
        $urlArray = parse_url($urlRequest);
        return $urlArray;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function doRequest()
    {
        $response = array();
        $urlArray = $this->generateUrlArray();
        $urlQuery = $urlArray['query'];

        $curl = curl_init(self::URL);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $urlQuery);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);

        $result = curl_exec($curl);

        $this->setRawResponse($result);

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            throw new \Exception('Invalid Response');
        } elseif (curl_error($curl)) {
            $response[] = "errormessage=" . curl_errno($curl) . ": " . curl_error($curl);
        } else {
            $response = explode("\n", $result);
        }
        curl_close($curl);

        return $response;
    }

    /**
     * @param array $responseRaw
     * @return array
     */
    protected function parseResponse(array $responseRaw = array())
    {
        $result = array();

        if (count($responseRaw) == 0) {
            return $result;
        }

        foreach ($responseRaw as $key => $line) {
            if (is_array($line)) {
                continue;
            }
            $pos = strpos($line, "=");

            if ($pos === false) {
                if (strlen($line) > 0) {
                    $result[$key] = $line;
                }
                continue;
            }

            $lineArray = explode('=', $line);
            $resultKey = array_shift($lineArray);
            $result[$resultKey] = implode('=', $lineArray);
        }

        return $result;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $data) {
            if ($data === null) {
                continue;
            }
            $result[$key] = $data;
        }
        ksort($result);
        return $result;
    }

    /**
     * @param string $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $params
     * @param $securityKey
     * @return string
     */
    public function generate($params, $securityKey)
    {
        sort($this->hashParameters);

        $hashString = '';
        foreach ($this->hashParameters as $key) {
            if (!array_key_exists($key, $params)) {
                continue;
            }
            $hashString .= $params[$key];
        }
        $hash = hash_hmac('sha384', $hashString, $securityKey);
        return $hash;
    }

    /**
     * @param $rawResponse
     */
    public function setRawResponse($rawResponse)
    {
        $this->rawResponse = $rawResponse;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param $params
     * @return void
     */
    public function add($params)
    {
        if (is_array($params)) {
            $this->params = array_merge($this->params, $params);
        }
    }

    /**
     * @return void
     */
    public function setInvoicing()
    {
        foreach ($this->params as $key => $value) {
            if (is_array($this->params[$key]) && is_int($key)) {
                $this->params['id[' . ($key + 1) . ']'] = $value['id'];
                $this->params['pr[' . ($key + 1) . ']'] = $value['pr'] * 100;
                $this->params['no[' . ($key + 1) . ']'] = $value['no'];
                $this->params['de[' . ($key + 1) . ']'] = $value['de'];
                $this->params['it[' . ($key + 1) . ']'] = $value['it'];
                $this->params['va[' . ($key + 1) . ']'] = $value['va'];
                $this->params['sd[' . ($key + 1) . ']'] = $value['sd'];
                $this->params['ed[' . ($key + 1) . ']'] = $value['ed'];
                unset($this->params[$key]);
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public function get($value)
    {
        return $this->$value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $stringArray = array();
        foreach ($this->params as $key => $value) {
            $stringArray[] = $key . '=' . $value;
        }
        $result = implode('|', $stringArray);
        return $result;
    }

    /**
     * @return array
     */
    public function getParam($param)
    {
        return $this->$param;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}