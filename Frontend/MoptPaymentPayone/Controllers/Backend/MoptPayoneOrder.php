<?php

class Shopware_Controllers_Backend_MoptPayoneOrder extends Shopware_Controllers_Backend_ExtJs
{

    protected $moptPayone__sdk__Builder   = null;
    /** @var Mopt_PayoneMain $moptPayoneMain */
    protected $moptPayone__main           = null;
    protected $moptPayone__helper         = null;
    protected $moptPayone__paymentHelper  = null;

    public function init()
    {
        $this->moptPayone__sdk__Builder = Shopware()->Plugins()->Frontend()
            ->MoptPaymentPayone()->Application()->MoptPayoneBuilder();
        $this->moptPayone__main = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->MoptPayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();
    }

    public function moptPayoneDebitAction()
    {
        $request = $this->Request();

        try {
          //get id
            $orderId = $request->getParam('id');

            if (!$order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId)) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('orderNotFound', 'Bestellung nicht gefunden', true);
                throw new Exception($message);
            }

            if (!$this->moptPayone_isOrderDebitable($order)) {
                $errorMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('debitNotPossibleGeneral', 'Gutschrift nicht möglich.', true);
                throw new Exception($errorMessage);
            }
      
            $payment         = $order->getPayment();
            $paymentName     = $payment->getName();
            if ($request->getParam('includeShipment') === 'true') {
                $includeShipment = true;
            } else {
                $includeShipment = false;
            }
      
            $config = Mopt_PayoneMain::getInstance()->getPayoneConfig($payment->getId());
          //positions ?
            $positionIds = $request->get('positionIds') ? json_decode($request->get('positionIds')) : array();

          //fetch params
            $params = $this->moptPayone__main->getParamBuilder()->buildOrderDebit($order, $positionIds, $includeShipment);
            if ($request->getParam('debitReason') != false) {
                $params['cancellation_reason'] = $request->getParam('debitReason');
            }

            if ($config['submitBasket'] || $this->moptPayone__main->getPaymentHelper()->isPayoneBillsafe($paymentName)) {
                $invoicing = $this->moptPayone__main->getParamBuilder()->getInvoicingFromOrder(
                    $order,
                    $positionIds,
                    'skipCaptureMode',
                    true,
                    $includeShipment
                );
            }

          //call capture service
            $response = $this->moptPayone_callDebitService($params, $invoicing);

            if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED) {
            //increase sequence
                $this->moptPayoneUpdateSequenceNumber($order, true);

              //mark / fill positions as captured
                $this->moptPayoneMarkPositionsAsDebited($order, $positionIds, $includeShipment);

                $response = array('success' => true);
            } else {
                $errorMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('debitNotPossibleNow', 'Gutschrift (zur Zeit) nicht möglich.', true);
                $response = array('success' => false, 'error_message' => $errorMessage);
            }
        } catch (Exception $e) {
            $response = array('success' => false, 'error_message' => $e->getMessage());
        }

        $this->View()->assign($response);
    }

    public function moptPayoneCaptureOrderAction()
    {
        $request = $this->Request();

        try {
            $orderId = $request->getParam('id');

            if (!$order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId)) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('orderNotFound', 'Bestellung nicht gefunden', true);
                throw new Exception($message);
            }

            if (!$this->moptPayone_isOrderCapturable($order)) {
                $errorMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('captureNotPossibleGeneral', 'Capture nicht möglich.', true);
                throw new Exception($errorMessage);
            }

            $payment     = $order->getPayment();
            $paymentName = $payment->getName();
            // remove _1 ,_2 ... from duplicated payments before matching
            $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);

            if ($request->getParam('includeShipment') === 'true') {
                $includeShipment = true;
            } else {
                $includeShipment = false;
            }
            $config = Mopt_PayoneMain::getInstance()->getPayoneConfig($payment->getId());

            //positions ?
            $positionIds = $request->get('positionIds') ? json_decode($request->get('positionIds')) : array();

            //covert finalize param
            $finalize = $request->get('finalize') == "true" ? true : false;

          //fetch params
            $params = $this->moptPayone__main->getParamBuilder()
              ->buildOrderCapture($order, $positionIds, $finalize, $includeShipment);

            if ($config['submitBasket'] ||
                $this->moptPayone__main->getPaymentHelper()->isPayoneBillsafe($paymentName) ||
                $this->moptPayone__main->getPaymentHelper()->isPayoneSafeInvoice($paymentName)
            ) {
                $invoicing = $this->moptPayone__main->getParamBuilder()
                ->getInvoicingFromOrder($order, $positionIds, $finalize, false, $includeShipment);
            }

            // see https://integrator.payone.de/jira/browse/SW-149
            $autoSettleAccount = false;
            $doNotSendCaptureMode = false;

            if ($this->moptPayone__main->getPaymentHelper()->isPayoneSafeInvoice($paymentName)){
                $autoSettleAccount = true;
            }

            if (in_array( $cleanedPaymentName,\Mopt_PayoneConfig::PAYMENTS_DONOTSENDCAPTUREMODE)) {
                $doNotSendCaptureMode = true;
            }

            $response = $this->moptPayone_callCaptureService($params, $invoicing, $autoSettleAccount, $doNotSendCaptureMode);

            if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED) {
            //increase sequence
                $this->moptPayoneUpdateSequenceNumber($order, true);

              //mark / fill positions as captured
                $this->moptPayoneMarkPositionsAsCaptured($order, $positionIds, $includeShipment);
        
              //extract and save clearing data
                $clearingData = $this->moptPayone__paymentHelper->extractClearingDataFromResponse($response);
                if ($clearingData) {
                    $this->moptPayoneSaveClearingData($order, $clearingData);
                }

                $response = array('success' => true);
            } else {
                $errorMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('captureNotPossibleNow', 'Capture (zur Zeit) nicht möglich.', true);
                $response = array('success' => false, 'error_message' => $errorMessage);
            }
        } catch (Exception $e) {
            $response = array('success' => false, 'error_message' => $e->getMessage());
        }

        $this->View()->assign($response);
    }


    /**
     * @param $order \Shopware\Models\Order\Order
     * @param bool $isAuth
     */
    protected function moptPayoneUpdateSequenceNumber($order, $isAuth = false)
    {
        $attribute = $this->moptPayone__helper->getOrCreateAttribute($order);
        $newSeq    = $attribute->getMoptPayoneSequencenumber() + 1;
        $attribute->setMoptPayoneSequencenumber($newSeq);
        if ($isAuth) {
            $attribute->setMoptPayoneIsAuthorized(true);
        }

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }

    /**
     * @param $order \Shopware\Models\Order\Order
     * @return bool
     */
    protected function moptPayone_isOrderCapturable($order)
    {
        if (!$this->moptPayone_hasOrderPayonePayment($order) || $this->moptPayone_isOrderFailed($order)) {
            return false;
        }

      //according to PAYONE, perform less checks in shop, let the API validate
        $attribute = $this->moptPayone__helper->getOrCreateAttribute($order);
    
        return true;
    }

    protected function moptPayone_callCaptureService($params, $invoicing = null, $autoSettleAccount = false, $doNotSendCaptureMode = false)
    {
        $service = $this->moptPayone__sdk__Builder->buildServicePaymentCapture();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request = new Payone_Api_Request_Capture($params);

        if ($invoicing) {
            $request->setInvoicing($invoicing);
        }

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();

        if ($params['payolution_b2b'] == true) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $params['vatid'])
            ));
        }

        if (isset($params['shop_id'])) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'shop_id', 'data' => $params['shop_id'])
            ));
        }

        // see https://integrator.payone.de/jira/browse/SW-149
        if (isset($params['capturemode']) && !$doNotSendCaptureMode) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'capturemode', 'data' => $params['capturemode'])
            ));
        }

        // see https://integrator.payone.de/jira/browse/SW-149
        if ($invoicing && $doNotSendCaptureMode === true){
            $invoiceParam = $request->getInvoicing();
            $invoiceParam->setCapturemode(null);
            $request->setInvoicing($invoiceParam);
        }

        if ($autoSettleAccount) {
            $businessParam = $request->getBusiness();
            $businessParam->setSettleaccount('auto');
            $request->setBusiness($businessParam);
        }

        $request->setPaydata($paydata);

        unset($params['data']);

        return $service->capture($request);
    }

    protected function moptPayoneMarkPositionsAsCaptured($order, $positionIds, $includeShipment = false)
    {
        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), $positionIds)) {
                continue;
            }

            $attribute = $this->moptPayone__helper->getOrCreateAttribute($position);
            $attribute->setMoptPayoneCaptured($position->getPrice() * $position->getQuantity());

            Shopware()->Models()->persist($attribute);
            Shopware()->Models()->flush();
      
          //check if shipping is included as position
            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }
    
        if ($includeShipment) {
            $orderAttribute = $this->moptPayone__helper->getOrCreateAttribute($order);
            $orderAttribute->setMoptPayoneShipCaptured($order->getInvoiceShipping());
            Shopware()->Models()->persist($orderAttribute);
            Shopware()->Models()->flush();
        }
    }


    /**
     * @param $order \Shopware\Models\Order\Order
     * @return bool
     */
    protected function moptPayone_isOrderDebitable($order)
    {
        if (!$this->moptPayone_hasOrderPayonePayment($order) || $this->moptPayone_isOrderFailed($order)) {
            return false;
        }

        return true;
    }

    protected function moptPayone_hasOrderPayonePayment($order)
    {
      //order has Payone-Payment ?
        if (strpos($order->getPayment()->getName(), 'mopt_payone__') !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $order \Shopware\Models\Order\Order
     * @return bool
     */
    protected function moptPayone_isOrderFailed($order)
    {
        // mopt_payone_status Attribute is 'failed'
        $orderAttribute = $this->moptPayone__helper->getOrCreateAttribute($order);
        if ($orderAttribute->getMoptPayoneStatus($order) === 'failed' || $orderAttribute->getMoptPayoneStatus($order)  === 'pending' ) {
            return true;
        }

        return false;
    }

    protected function moptPayone_callDebitService($params, $invoicing = null)
    {
        $service = $this->moptPayone__sdk__Builder->buildServicePaymentDebit();

        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request = new Payone_Api_Request_Debit($params);

        if ($invoicing) {
            $request->setInvoicing($invoicing);
        }
        
        if ($params['payolution_b2b'] == true) {
            $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                ['key' => 'b2b', 'data' => 'yes']
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                ['key' => 'company_trade_registry_number', 'data' => $params['vatid']]
            ));
            $request->setPaydata($paydata);
        }

        if (isset($params['cancellation_reason'])) {
            $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'cancellation_reason', 'data' => $params['cancellation_reason'])
            ));
            $request->setPaydata($paydata);
            unset($params['cancellation_reason']);
        }
        
        if (isset($params['shop_id'])) {
            $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'shop_id', 'data' => $params['shop_id'])
            ));
            $request->setPaydata($paydata);
        }
        
        return $service->debit($request);
    }

    protected function moptPayoneMarkPositionsAsDebited($order, $positionIds, $includeShipment = false)
    {
        foreach ($order->getDetails() as $position) {
            if (!$positionIds || !in_array($position->getId(), $positionIds)) {
                continue;
            }

            $attribute = $this->moptPayone__helper->getOrCreateAttribute($position);
            $attribute->setMoptPayoneDebit($position->getPrice() * $position->getQuantity());

            Shopware()->Models()->persist($attribute);
            Shopware()->Models()->flush();
      
            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }
    
        if ($includeShipment) {
            $orderAttribute = $this->moptPayone__helper->getOrCreateAttribute($order);
            $orderAttribute->setMoptPayoneShipDebit($order->getInvoiceShipping());
            Shopware()->Models()->persist($orderAttribute);
            Shopware()->Models()->flush();
        }
    }

    protected function moptPayoneSaveClearingData($order, $clearingData)
    {
        $attribute = $this->moptPayone__helper->getOrCreateAttribute($order);
        $attribute->setMoptPayoneClearingData(json_encode($clearingData));

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }
}
