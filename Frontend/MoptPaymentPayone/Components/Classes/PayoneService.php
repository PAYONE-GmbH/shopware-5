<?php

/**
 * NOTICE
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 */
/**
 * This class implements a service that provides access to some of the plugin's operations. You can request
 * the service by calling `$this->get('payone_service')` e.g. from controllers or plugin bootstraps.
 * You can also use the global DI container: `Shopware()->Container()->get('payone_service')`.
 *
 *
 * @category        Payone
 * @package         Payone Payment Plugin for Shopware 5
 * @subpackage      Components
 * @copyright       Copyright (c) 2017 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Mario Dorn <mario.dorn@fatchip.de>
 * @link            http://www.fatchip.com
 */
class Mopt_PayoneService
{
    /**
     * @var Mopt_PayoneMain $payoneMain
     */
    protected $payoneMain = null;

    /**
     * Mopt_PayoneService constructor.
     */
    public function __construct()
    {
        $this->payoneMain = Mopt_PayoneMain::getInstance();
    }

    /**
     * Can be used to capture funds for the given order positions.
     *
     * <b>Example: Service implementation to capture funds</b>
     * <pre>
     * public function myOrderCapture()
     * {
     *   $payoneService = $this->get('payone_service');
     *   // $orderDetails is an array of Shopware order detail IDs and corresponding amounts to capture.
     *   // You can get these values e.g. from the database table s_order_details.
     *   // All given order details must belong to the same order.
     *   // Also note that the given amount can be lower than the position's actual amount.
     *   // In this case, you may repeat partial captures until you set $finalize to true.
     *   $orderDetails = [
     *     [
     *       'id' => 1,
     *       'amount' => 3.0
     *     ],
     *     [
     *       'id' => 2,
     *       'amount' => 5
     *     ],
     *   ];
     *   try {
     *     $return $payoneService->captureOrder($orderDetails, true, true);
     *   } catch (Exception $e){
     *     echo "Exception:" . $e->getMessage();
     *   }
     * }
     * </pre>
     *
     *
     * @param array $orderDetailParams array of order detail ID's and amounts to capture; see full example
     * @param bool $finalize true marks the last capture operation; afterwards captures are no longer possible
     * @param bool $includeShipment true to include shipping costs; false if they are an extra order position
     * @return bool true if the request has been approved
     * @throws Exception
     */
    public function captureOrder($orderDetailParams, $finalize = false, $includeShipment = false)
    {

        $orderParams = array_combine(
            array_column($orderDetailParams, 'id'),
            array_column($orderDetailParams, 'amount')
        );

        try {
            $orderDetailId = key($orderParams);
            $repository = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail');

            /** @var \Shopware\Models\Order\Detail $orderDetail */
            if (!$orderDetail = $repository->find($orderDetailId)) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('orderDetailNotFound', 'Bestell-Position nicht gefunden', true);
                throw new Exception($message);
            }

            /** @var \Shopware\Models\Order\Order $order */
            if (!$order = $orderDetail->getOrder()) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('orderNotFound', 'Bestellung nicht gefunden', true);
                throw new Exception($message);
            }

            $payment = $order->getPayment();
            $paymentName = $payment->getName();

            // check if the order used a payone payment type
            if (strpos($paymentName, 'mopt_payone__') !== 0) {
                $message = 'Capturing funds is only possible for Payone payments!';
                throw new Exception($message);
            }

            $config = $this->payoneMain->getPayoneConfig($payment->getId());

            // build API request parameters
            $params = $this->payoneMain->getParamBuilder()
                ->buildCustomOrderCapture($order, $orderParams, $finalize, $includeShipment);

            $invoicing = null;

            if ($config['submitBasket'] || $this->payoneMain->getPaymentHelper()->isPayoneBillsafe($paymentName)) {
                $invoicing = $this->payoneMain->getParamBuilder()->getInvoicingFromOrder(
                    $order,
                    array_column($orderDetailParams, 'id'),
                    $finalize,
                    false,
                    $includeShipment
                );
            }

            // call the capture service
            $response = $this->callPayoneCaptureService($params, $invoicing);

            if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED) {
                // increase the sequence number
                $this->updateSequenceNumber($order, true);

                // mark positions as captured
                $this->markPositionsAsCaptured($order, $orderDetailParams, $includeShipment);

                // extract and save clearing data
                $clearingData = $this->payoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);
                if ($clearingData) {
                    $this->saveClearingData($order, $clearingData);
                }

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Can be used to refund the given order positions.
     *
     * <b>Example: Service implementation to refund order positions</b>
     * <pre>
     * public function myOrderRefund()
     * {
     *   $payoneService = $this->get('payone_service');
     *   // $orderDetails is an array of Shopware order detail IDs and corresponding amounts and quantities to refund.
     *   // You can get these values e.g. from the database table s_order_details.
     *   // All given order details must belong to the same order.
     *   // Also note that the given amount can be lower than the position's actual amount.
     *   // In this case, you may repeat partial refunds until you set $finalize to true.
     *   $orderDetails = [
     *     [
     *       'id' => 1,
     *       'amount' => 3.0,
     *       'quantity' => 2
     *     ],
     *     [
     *       'id' => 2,
     *       'amount' => 5,
     *       'quantity' => 1
     *     ],
     *   ];
     *   try {
     *     return $payoneService->refundOrder($orderDetails, true, true);
     *   } catch (Exception $e){
     *     echo "Exception:" . $e->getMessage();
     *   }
     * }
     * </pre>
     *
     * @param array $orderDetailParams array of order detail ID's, amounts and quantities to refund; see full example
     * @param bool $finalize true marks the last refund operation; afterwards refunds are no longer possible
     * @param bool $includeShipment true to include shipping costs; false if they are an extra order position
     * @return bool true if the request has been approved
     * @throws Exception
     */
    public function refundOrder($orderDetailParams, $finalize = false, $includeShipment = false)
    {
        $quantities = array_combine(
            array_column($orderDetailParams, 'id'),
            array_column($orderDetailParams, 'quantity')
        );
        $orderParams = array_combine(
            array_column($orderDetailParams, 'id'),
            array_column($orderDetailParams, 'amount')
        );

        try {
            $orderDetailId = key($orderParams);
            $repository = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail');

            /** @var \Shopware\Models\Order\Detail $orderDetail */
            if (!$orderDetail = $repository->find($orderDetailId)) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('orderDetailNotFound', 'Bestell-Position nicht gefunden', true);
                throw new Exception($message);
            }

            /** @var \Shopware\Models\Order\Order $order */
            if (!$order = $orderDetail->getOrder()) {
                $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('orderNotFound', 'Bestellung nicht gefunden', true);
                throw new Exception($message);
            }

            $payment = $order->getPayment();
            $paymentName = $payment->getName();

            // check if the order used a payone payment type
            if (strpos($paymentName, 'mopt_payone__') !== 0) {
                $message = 'Refund is only possible with payone payments';
                throw new Exception($message);
            }

            $config = $this->payoneMain->getPayoneConfig($payment->getId());

            // build API request parameters
            $params = $this->payoneMain->getParamBuilder()
                ->buildCustomOrderDebit($order, $orderParams, $includeShipment);

            $invoicing = null;

            if ($config['submitBasket'] || $this->payoneMain->getPaymentHelper()->isPayoneBillsafe($paymentName)) {
                $invoicing = $this->payoneMain->getParamBuilder()->getInvoicingFromOrder(
                    $order,
                    array_column($orderDetailParams, 'id'),
                    $finalize,
                    true,
                    $includeShipment,
                    $quantities
                );
            }
            
            // call the refund service
            $response = $this->callPayoneRefundService($params, $invoicing);

            if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED) {
                // increase the sequence number
                $this->updateSequenceNumber($order, true);

                // mark positions as debited
                $this->markPositionsAsDebited($order, $orderDetailParams, $includeShipment);

                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param mixed $params
     * @param null $invoicing
     * @return Payone_Api_Response_Capture_Approved|Payone_Api_Response_Error
     */
    protected function callPayoneCaptureService($params, $invoicing = null)
    {
        $service = Shopware()->Container()->get('MoptPayoneBuilder')->buildServicePaymentCapture();
        $service->getServiceProtocol()->addRepository(
            Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog')
        );
        $request = new Payone_Api_Request_Capture($params);

        if ($invoicing) {
            $request->setInvoicing($invoicing);
        }

        if ($params['payolution_b2b'] == true) {
            $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                ['key' => 'b2b', 'data' => 'yes']
            ));
            $request->setPaydata($paydata);
        }
        return $service->capture($request);
    }

    /**
     * @param mixed $params
     * @param null $invoicing
     * @return Payone_Api_Response_Debit_Approved|Payone_Api_Response_Error
     */
    protected function callPayoneRefundService($params, $invoicing = null)
    {
        $service = Shopware()->Container()->get('MoptPayoneBuilder')->buildServicePaymentDebit();
        $service->getServiceProtocol()->addRepository(
            Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog')
        );
        $request = new Payone_Api_Request_Debit($params);

        if ($invoicing) {
            $request->setInvoicing($invoicing);
        }

        return $service->debit($request);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param array $orderDetailParams
     * @param bool $includeShipment
     */
    protected function markPositionsAsCaptured($order, $orderDetailParams, $includeShipment = false)
    {

        $orderParams = array_combine(
            array_column($orderDetailParams, 'id'),
            array_column($orderDetailParams, 'amount')
        );
        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), array_column($orderDetailParams, 'id'))) {
                continue;
            }


            $attribute = $this->payoneMain->getHelper()->getOrCreateAttribute($position);
            $amount = $orderParams[$position->getId()];
            $attribute->setMoptPayoneCaptured($amount + $attribute->getMoptPayoneCaptured());

            Shopware()->Models()->persist($attribute);
            Shopware()->Models()->flush();

            // check if shipping is included as a position
            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            $orderAttribute = $this->payoneMain->getHelper()->getOrCreateAttribute($order);
            $orderAttribute->setMoptPayoneShipCaptured($order->getInvoiceShipping());
            Shopware()->Models()->persist($orderAttribute);
            Shopware()->Models()->flush();
        }
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param array $orderDetailParams
     * @param bool $includeShipment
     */
    protected function markPositionsAsDebited($order, $orderDetailParams, $includeShipment = false)
    {

        $orderParams = array_combine(
            array_column($orderDetailParams, 'id'),
            array_column($orderDetailParams, 'amount')
        );

        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), array_column($orderDetailParams, 'id'))) {
                continue;
            }

            $attribute = $this->payoneMain->getHelper()->getOrCreateAttribute($position);
            $amount = $orderParams[$position->getId()];
            $attribute->setMoptPayoneDebit($amount + $attribute->getMoptPayoneDebit());

            Shopware()->Models()->persist($attribute);
            Shopware()->Models()->flush();

            // check if shipping is included as a position
            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            $orderAttribute = $this->payoneMain->getHelper()->getOrCreateAttribute($order);
            $orderAttribute->setMoptPayoneShipDebit($order->getInvoiceShipping());
            Shopware()->Models()->persist($orderAttribute);
            Shopware()->Models()->flush();
        }
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param array $clearingData
     */
    protected function saveClearingData($order, $clearingData)
    {
        $attribute = $this->payoneMain->getHelper()->getOrCreateAttribute($order);
        $attribute->setMoptPayoneClearingData(json_encode($clearingData));

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param bool $isAuth
     */
    protected function updateSequenceNumber($order, $isAuth = false)
    {
        $attribute = $this->payoneMain->getHelper()->getOrCreateAttribute($order);
        $newSeq = $attribute->getMoptPayoneSequencenumber() + 1;
        $attribute->setMoptPayoneSequencenumber($newSeq);
        if ($isAuth) {
            $attribute->setMoptPayoneIsAuthorized(true);
        }

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }
}
