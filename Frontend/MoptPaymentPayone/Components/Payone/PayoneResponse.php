<?php

namespace Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone;

class PayoneResponse
{
    protected $status;

    protected $secstatus;

    protected $rawResponse;

    protected $errorCode;

    protected $redirecturl;

    protected $personstatus;

    protected $paydata;

    protected $workorderid;

    /**
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        if (count($params) > 0) {
            $this->init($params);
        }
    }

    /**
     * @param array $data
     */
    public function init(array $data = array())
    {
        foreach ($data as $key => $value) {
            if (strpos($key, 'add_paydata') !== false) {
                $newKey = str_replace(['add_paydata[', ']'], '', $key);
                $this->paydata[$newKey] = $value;
            } else {
                $this->$key = $value;
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getSecstatus()
    {
        return $this->secstatus;
    }

    /**
     * @param $rawResponse
     * @return void
     */
    public function setRawResponse($rawResponse)
    {
        $this->rawResponse = $rawResponse;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getRedirecturl()
    {
        return $this->redirecturl;
    }

    /**
     * @return mixed
     */
    public function getPersonstatus()
    {
        return $this->personstatus;
    }

    /**
     * @return mixed
     */
    public function getRatepayPayDataArray() {
        $aPayData = $this->getPaydata();
        foreach($aPayData as $key => $value) {
            $sCorrectedKey = strtolower($key);
            $sCorrectedKey = str_replace('-', '_', $sCorrectedKey);

            // make keys and values compatible to SW magic getter and setters
            $sCorrectedKey = ucwords($sCorrectedKey, "_");
            $sCorrectedKey = str_replace('_', '', $sCorrectedKey);
            $sCorrectedKey = lcfirst($sCorrectedKey);

            // correct yes/no DataItems to bool
            $correctedItem = $value;
            $correctedItem = $correctedItem === 'yes' ? 1 : $correctedItem;
            $correctedItem = $correctedItem === 'no' ? 0 : $correctedItem;
            $aPayData[$sCorrectedKey] = $correctedItem;
        }
        ksort($aPayData);
        return $aPayData;
    }

    /**
     * @return false|mixed
     */
    public function getInstallmentData()
    {
        $aInstallmentData = array();
        $aPayData = $this->getPaydata();
        foreach ($aPayData as $sKey => $sValue) {
            $aSplit = explode('_', $sKey);
            for($i = count($aSplit); $i > 0; $i--) {
                if($i == count($aSplit)) {
                    $aTmp = array(strtolower($aSplit[$i-1]) => $sValue);
                } else {
                    $aTmp = array(strtolower($aSplit[$i-1]) => $aTmp);
                }
            }
            $aInstallmentData = array_replace_recursive($aInstallmentData, $aTmp);
        }

        if(isset($aInstallmentData['paymentdetails']) && count($aInstallmentData['paymentdetails']) > 0) {
            return $aInstallmentData['paymentdetails'];
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getPaydata() {
        return $this->paydata;
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
     * @return string
     */
    public function __toString()
    {
        $stringArray = array();
        foreach ($this->toArray() as $key => $value) {
            $stringArray[] = $key . '=' . $value;
        }
        $result = implode('|', $stringArray);
        return $result;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        if ($this->getStatus() === PayoneEnums::REDIRECT) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->getStatus() === PayoneEnums::VALID) {
            return true;
        }
        return false;
    }

    /**
     * @param $status
     * @return void
     */
    public function setStatus($status) {
        $this->status = $status;
    }

}