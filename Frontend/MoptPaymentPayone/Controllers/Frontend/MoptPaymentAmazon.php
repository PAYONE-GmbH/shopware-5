<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * Klarna payment controller
 */
class Shopware_Controllers_Frontend_MoptPaymentAmazon extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{

    private $plugin;
    private $admin;
    private $session;
    private $basket;

    /**
     * Init method that get called automatically
     *
     * Set class properties
     */
    public function init()
    {
        $this->admin = Shopware()->Modules()->Admin();
        $this->session = Shopware()->Session();
        $this->plugin = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone();
    }

    /**
     * whitelists Actions for SW 5.2 compatibility
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'finish',
        ];
    }

    public function indexAction()
    {

        if (!empty($this->Request()->getParam("access_token"))) {
            $this->session->moptPayoneAmazonAccessToken = $this->Request()->getParam("access_token");
        }

        if (!empty($this->Request()->getParam("moptAmazonError"))) {
            $this->View()->moptPayoneAmazonError = $this->Request()->getParam("moptAmazonError");
        }

        if (!empty($this->Request()->getParam("moptAmazonReadonly"))) {
            $this->View()->payoneAmazonReadOnly = 'true';
        }

        if ($this->container->get('MoptPayoneMain')->getPaymentHelper()->isAmazonPayActive()
            && ($payoneAmazonPayConfig = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayoneAmazonPayConfig())
        ) {

            $basket = $this->get('modules')->Basket()->sGetBasket();
            $userData = $this->getUserData();

            $userAdditionalArray = [];
            $userAdditionalArray['additional']['charge_vat'] = 1;
            $userAdditionalArray['additional']['payment']['id'] = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay()->getId();
            $userAdditionalArray['additional']['countryShipping'] = $userData['additional']['countryShipping'];
            $this->View()->assign('sUserData', $userAdditionalArray);


            if ($this->Request()->getParam("sDispatch")) {
                $this->setDispatch($this->Request()->getParam("sDispatch"), Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay()->getId());
            }


            $this->View()->sDispatches = $this->getDispatches(Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay()->getId());
            $this->View()->sAmount = $basket['Amount'];
            $this->View()->assign('payoneAmazonPayConfig', $payoneAmazonPayConfig);
            $this->View()->sDispatch = $this->getSelectedDispatch();
            $shippingCosts = $this->getShippingCosts();

            // basket content neccessary for minibasket

            $basket['sShippingcosts'] = $shippingCosts['brutto'];
            $basket['sShippingcostsWithTax'] = $shippingCosts['brutto'];
            $basket['sShippingcostsNet'] = $shippingCosts['netto'];
            $basket['sShippingcostsTax'] = $shippingCosts['tax'];

            $basket['AmountWithTaxNumeric'] = floatval(
                    str_replace(',', '.', $basket['Amount'])
                ) + floatval(
                    str_replace(',', '.', $shippingCosts['brutto'])
                );
            $basket['AmountNetNumeric'] = floatval(str_replace(',', '.', $basket['AmountNet']));
            $basket['sAmountNet'] = floatval($basket['AmountNetNumeric']) + floatval($shippingCosts['netto']);
            $basket['sTaxRates'] = $this->getTaxRates($basket);

            $this->View()->sShippingcosts = $shippingCosts['brutto'];
            $this->View()->sShippingcostsWithTax = $shippingCosts['brutto'];
            $this->View()->sShippingcostsNet = $shippingCosts['netto'];
            $this->View()->sShippingcostsTax = $shippingCosts['tax'];
            $this->View()->sAmount = $basket['AmountWithTaxNumeric'];
            $this->View()->sAmountNet = $basket['sAmountNet'];
            $this->View()->sAmountTax = $basket['sAmountTax'];
            $this->View()->sBasket = $basket;

            $this->session['sOrderVariables'] = new ArrayObject($this->View()->getAssign(), ArrayObject::ARRAY_AS_PROPS);
        }
    }

    public function finishAction()
    {
        $paymentId = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay()->getId();
        $this->basket = $this->get('modules')->Basket()->sGetBasket();
        $moptPayoneMain = $this->plugin->get('MoptPayoneMain');
        $payoneServiceBuilder = $this->plugin->get('MoptPayoneBuilder');
        $paramBuilder = $moptPayoneMain->getParamBuilder();
        $userData = $this->getUserData();

        $config = $moptPayoneMain->getPayoneConfig($paymentId);;

        $params = $moptPayoneMain->getParamBuilder()->buildAuthorize($paymentId);

        if ($config['authorisationMethod'] === 'Autorisierung') {
            $request = new Payone_Api_Request_Authorization($params);
            $service = $payoneServiceBuilder->buildServicePaymentAuthorize();
            $this->session->moptIsAuthorized = true;
        } else {
            $request = new Payone_Api_Request_Preauthorization($params);
            $service = $payoneServiceBuilder->buildServicePaymentPreAuthorize();
            $this->session->moptIsAuthorized = false;
        }
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amazon_reference_id', 'data' => $this->session->moptPayoneAmazonReferenceId)
        ));

        // sync / async mode according to backend configuration
        $payoneAmazonPayConfig = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayoneAmazonPayConfig();

        if ($payoneAmazonPayConfig->getAmazonMode() === 'sync') {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'amazon_timeout', 'data' => 0)
            ));
        } elseif ($payoneAmazonPayConfig->getAmazonMode() === 'async') {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'amazon_timeout', 'data' => 1440)
            ));
        } else {
            // first try sync, on failure try async
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'amazon_timeout', 'data' => 0)
            ));
        }

        $request->setPaydata($paydata);
        $request->setApiVersion("3.10");
        $request->setCurrency("EUR");
        $request->setClearingtype(Payone_Enum_ClearingType::WALLET);
        $request->setWorkorderId($this->session->moptPayoneAmazonWorkOrderId);
        $request->setWallettype(Payone_Api_Enum_WalletType::AMAZONPAY);
        $request->setReference($paramBuilder->getParamPaymentReference());

        $personalData = $paramBuilder->getPersonalData($userData);
        $request->setPersonalData($personalData);
        $deliveryData = $paramBuilder->getDeliveryData($userData);
        $request->setDeliveryData($deliveryData);

        $router = $this->Front()->Router();
        $successurl = $router->assemble(array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $errorurl = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $backurl = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $request->setSuccessurl($successurl);
        $request->setBackurl($errorurl);
        $request->setErrorurl($backurl);
        $request->setAmount(Shopware()->Session()->sOrderVariables['sAmount']);

        if ($config['submitBasket'] === true) {
            $request->setInvoicing($paramBuilder->getInvoicing($this->getBasket(), $this->getSelectedDispatch(), $this->getUserData()));
        }

        if ($config['authorisationMethod'] === 'Autorisierung') {
            $response = $service->authorize($request);
        } else {
            $response = $service->preauthorize($request);
        }

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::ERROR) {

            if ($response->getErrorCode() === '980') {
                // retry transaction in async mode

                $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
                $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                    array('key' => 'amazon_reference_id', 'data' => $this->session->moptPayoneAmazonReferenceId)
                ));
                $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                    array('key' => 'amazon_timeout', 'data' => 1440)
                ));

                // reset basket amount to prevent being multiplied with 100
                // do the same for invoice parameters

                $request->setAmount(Shopware()->Session()->sOrderVariables['sAmount']);

                if ($config['submitBasket'] === true) {
                    $request->setInvoicing($paramBuilder->getInvoicing($this->getBasket(), $this->getSelectedDispatch(), $this->getUserData()));
                }

                $request->setPaydata($paydata);

                if ($config['authorisationMethod'] === 'Autorisierung') {
                    $response = $service->authorize($request);
                } else {
                    $response = $service->preauthorize($request);
                }

                if ($response->getStatus() === Payone_Api_Enum_ResponseType::ERROR) {

                    // redirect User to normal payment selection
                    $this->forward('shippingPayment', 'checkout');
                    return;
                }

            } elseif ($response->getErrorCode() === '981') {
                // redirect to checkout so customer can choose a new payment method
                $this->redirect([
                    'controller' => 'MoptPaymentAmazon',
                    'action' => 'index',
                    'moptAmazonError' => 'declined',
                    'moptAmazonReadonly' => 'true',
                ]);
                return;
            }

        }

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::APPROVED || $response->getStatus() === 'PENDING') {

            // Save Clearing Reference as Attribute (set in session )
            $this->session->paymentReference = $request->getReference();

            Shopware()->Session()->sOrderVariables['sUserData'] = $this->getUserData();
            $txid = $response->getTxid();
            $orderNumber = $this->saveOrder(
                $txid,
                $this->session->paymentReference
            );

            // get orderId for attribute Saving
            $sql = 'SELECT `id` FROM `s_order` WHERE transactionID = ?'; //get order id
            $orderId = Shopware()->Db()->fetchOne($sql, $txid);

            // save fields as Order Attribute
            $sql = 'UPDATE `s_order_attributes` ' .
                'SET mopt_payone_txid=?, mopt_payone_is_authorized=?, mopt_payone_payment_reference=?, '
                . 'mopt_payone_order_hash=?, mopt_payone_payolution_workorder_id=?,  mopt_payone_payolution_clearing_reference=?  WHERE orderID = ?';
            Shopware()->Db()->query($sql, array($txid, $this->session->moptIsAuthorized,
                $this->session->paymentReference, $this->session->moptOrderHash, $this->session->moptPayoneAmazonWorkOrderId, $this->session->moptPayoneAmazonReferenceId, $orderId));
        } else {

            // redirect back to checkout or show error message
            $this->session->payoneErrorMessage = $moptPayoneMain->getPaymentHelper()
                ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
            $this->forward('error', 'MoptPaymentPayone');
            return;
        }

        // Register Custom Template
        $this->View()->loadTemplate("frontend/checkout/finish.tpl");

        // Fill in Order Addresses

        $orderVariables = $this->session['sOrderVariables']->getArrayCopy();

        // unset payoneAmazonPayConfig for debug Module compatibility
        unset($orderVariables['payoneAmazonPayConfig']);

        // add addresses for SW > 5.2

        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
            $orderVariables['sAddresses']['billing'] = $this->getOrderAddress($orderVariables['sOrderNumber'], 'billing');
            $orderVariables['sAddresses']['shipping'] = $this->getOrderAddress($orderVariables['sOrderNumber'], 'shipping');
            $orderVariables['sAddresses']['equal'] = $this->areAddressesEqual($orderVariables['sAddresses']['billing'], $orderVariables['sAddresses']['shipping']);
        }

        $this->View()->assign($orderVariables);

        // unset session Vars
        unset($this->session->moptPayoneAmazonAccessToken);
        unset($this->session->moptPayoneAmazonReferenceId);
        unset($this->session->moptPayoneAmazonWorkOrderId);
        // reset basket
        unset($this->session['sBasketQuantity']);
        unset($this->session['sBasketAmount']);
        // logout user to prevent autoforward to checkout/confirm when placing a second "normal" order
        $this->session->sUserId = null;
    }

    /**
     * Get all countries from database via sAdmin object
     *
     * @return array list of countries
     */
    public function getCountryList()
    {
        return Shopware()->Modules()->Admin()->sGetCountryList();
    }

    /**
     * Get current selected country - if no country is selected, choose first one from list
     * of available countries
     *
     * @return array with country information
     */
    public function getSelectedState()
    {
        $session = Shopware()->Session();
        if (!empty($this->View()->sUserData['additional']['stateShipping'])) {
            $session['sState'] = (int)$this->View()->sUserData['additional']['stateShipping']['id'];
            return $this->View()->sUserData['additional']['stateShipping'];
        }
        return ["id" => $session['sState']];
    }

    /**
     * Get all dispatches available in selected country from sAdmin object
     *
     * @param null $paymentId
     * @return array|boolean list of dispatches
     */
    public function getDispatches($paymentId = null)
    {
        $country = $this->getSelectedCountry();
        $state = $this->getSelectedState();
        if (empty($country)) {
            return false;
        }
        $stateId = !empty($state['id']) ? $state['id'] : null;
        return Shopware()->Modules()->Admin()->sGetPremiumDispatches($country['id'], $paymentId, $stateId);
    }

    /**
     * Set the provided dispatch method
     *
     * @param int $dispatchId ID of the dispatch method to set
     * @param int|null $paymentId Payment id to validate
     * @return int set dispatch method id
     */
    public function setDispatch($dispatchId, $paymentId = null)
    {
        $session = Shopware()->Session();
        $supportedDispatches = $this->getDispatches($paymentId);

        // Iterate over supported dispatches, look for the provided one
        foreach ($supportedDispatches as $dispatch) {
            if ($dispatch['id'] == $dispatchId) {
                $session['sDispatch'] = $dispatchId;
                return $dispatchId;
            }
        }

        // If it was not found, we fallback to the default (head of supported)
        $defaultDispatch = array_shift($supportedDispatches);
        $session['sDispatch'] = $defaultDispatch['id'];
        return $session['sDispatch'];
    }

    /**
     * Get selected dispatch or select a default dispatch
     *
     * @return boolean|array
     */
    public function getSelectedDispatch()
    {
        $session = Shopware()->Session();
        if (empty($session['sCountry'])) {
            return false;
        }

        $dispatches = Shopware()->Modules()->Admin()->sGetPremiumDispatches($session['sCountry'], null, $session['sState']);
        if (empty($dispatches)) {
            unset($session['sDispatch']);
            return false;
        }

        foreach ($dispatches as $dispatch) {
            if ($dispatch['id'] == $session['sDispatch']) {
                return $dispatch;
            }
        }
        $dispatch = reset($dispatches);
        $session['sDispatch'] = (int)$dispatch['id'];
        return $dispatch;
    }

    /**
     * On any change on country, payment or dispatch recalculate shipping costs
     * and forward to cart / confirm view
     */
    public function calculateShippingCostsAction()
    {

        if ($this->Request()->getPost('sCountry')) {
            $this->session['sCountry'] = (int)$this->Request()->getPost('sCountry');
            $this->session["sState"] = 0;
            $this->session["sArea"] = Shopware()->Db()->fetchOne("
            SELECT areaID FROM s_core_countries WHERE id = ?
            ", [$this->session['sCountry']]);
        }

        if ($this->Request()->getPost('sPayment')) {
            $this->session['sPaymentID'] = (int)$this->Request()->getPost('sPayment');
        }

        if ($this->Request()->getPost('sDispatch')) {
            $this->session['sDispatch'] = (int)$this->Request()->getPost('sDispatch');
        }

        if ($this->Request()->getPost('sState')) {
            $this->session['sState'] = (int)$this->Request()->getPost('sState');
        }

        // We might change the shop context here so we need to initialize it again
        $this->get('shopware_storefront.context_service')->initializeShopContext();

        // We need an indicator in the view to expand the shipping costs pre-calculation on page load
        $this->View()->assign('calculateShippingCosts', true);

        $this->forward('index');
    }

    /**
     * Get shipping costs as an array (brutto / netto) depending on selected country / payment
     *
     * @return array
     */
    public function getShippingCosts()
    {
        $country = $this->getSelectedCountry();
        $payment = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay();
        if (empty($country) || empty($payment)) {
            return ['brutto' => 0, 'netto' => 0];
        }
        $shippingcosts = Shopware()->Modules()->Admin()->sGetPremiumShippingcosts($country);
        return empty($shippingcosts) ? ['brutto' => 0, 'netto' => 0] : $shippingcosts;
    }

    /**
     * Returns tax rates for all basket positions
     *
     * @param unknown_type $basket array returned from this->getBasket
     *
     * @return array
     */
    public function getTaxRates($basket)
    {
        $result = [];

        if (!empty($basket['sShippingcostsTax'])) {
            $basket['sShippingcostsTax'] = number_format(floatval($basket['sShippingcostsTax']), 2);

            $result[$basket['sShippingcostsTax']] = $basket['sShippingcostsWithTax'] - $basket['sShippingcostsNet'];
            if (empty($result[$basket['sShippingcostsTax']])) {
                unset($result[$basket['sShippingcostsTax']]);
            }
        } elseif ($basket['sShippingcostsWithTax']) {
            $result[number_format(floatval(Shopware()->Config()->get('sTAXSHIPPING')), 2)] = $basket['sShippingcostsWithTax'] - $basket['sShippingcostsNet'];
            if (empty($result[number_format(floatval(Shopware()->Config()->get('sTAXSHIPPING')), 2)])) {
                unset($result[number_format(floatval(Shopware()->Config()->get('sTAXSHIPPING')), 2)]);
            }
        }

        if (empty($basket['content'])) {
            ksort($result, SORT_NUMERIC);

            return $result;
        }

        foreach ($basket['content'] as $item) {
            if (!empty($item['tax_rate'])) {
            } elseif (!empty($item['taxPercent'])) {
                $item['tax_rate'] = $item['taxPercent'];
            } elseif ($item['modus'] == 2) {
                // Ticket 4842 - dynamic tax-rates
                $resultVoucherTaxMode = Shopware()->Db()->fetchOne(
                    'SELECT taxconfig FROM s_emarketing_vouchers WHERE ordercode=?
                ', [$item['ordernumber']]);
                // Old behaviour
                if (empty($resultVoucherTaxMode) || $resultVoucherTaxMode == 'default') {
                    $tax = Shopware()->Config()->get('sVOUCHERTAX');
                } elseif ($resultVoucherTaxMode == 'auto') {
                    // Automatically determinate tax
                    $tax = $this->basket->getMaxTax();
                } elseif ($resultVoucherTaxMode == 'none') {
                    // No tax
                    $tax = '0';
                } elseif (intval($resultVoucherTaxMode)) {
                    // Fix defined tax
                    $tax = Shopware()->Db()->fetchOne('
                    SELECT tax FROM s_core_tax WHERE id = ?
                    ', [$resultVoucherTaxMode]);
                }
                $item['tax_rate'] = $tax;
            } else {
                // Ticket 4842 - dynamic tax-rates
                $taxAutoMode = Shopware()->Config()->get('sTAXAUTOMODE');
                if (!empty($taxAutoMode)) {
                    $tax = $this->basket->getMaxTax();
                } else {
                    $tax = Shopware()->Config()->get('sDISCOUNTTAX');
                }
                $item['tax_rate'] = $tax;
            }

            if (empty($item['tax_rate']) || empty($item['tax'])) {
                continue;
            } // Ignore 0 % tax

            $taxKey = number_format(floatval($item['tax_rate']), 2);

            $result[$taxKey] += str_replace(',', '.', $item['tax']);
        }

        ksort($result, SORT_NUMERIC);

        return $result;
    }

    /**
     * Create temporary order in s_order_basket on confirm page
     * Used to track failed / aborted orders
     */
    public function saveTemporaryOrder()
    {
        $order = Shopware()->Modules()->Order();
        $session = Shopware()->Session();

        $order->sUserData = $this->View()->sUserData;
        $order->sComment = isset($session['sComment']) ? $session['sComment'] : '';
        $order->sBasketData = $this->View()->sBasket;
        $order->sAmount = $this->View()->sBasket['sAmount'];
        $order->sAmountWithTax = !empty($this->View()->sBasket['AmountWithTaxNumeric']) ? $this->View()->sBasket['AmountWithTaxNumeric'] : $this->View()->sBasket['AmountNumeric'];
        $order->sAmountNet = $this->View()->sBasket['AmountNetNumeric'];
        $order->sShippingcosts = $this->View()->sBasket['sShippingcosts'];
        $order->sShippingcostsNumeric = $this->View()->sBasket['sShippingcostsWithTax'];
        $order->sShippingcostsNumericNet = $this->View()->sBasket['sShippingcostsNet'];
        $order->dispatchId = $session['sDispatch'];
        $order->sNet = !$this->View()->sUserData['additional']['charge_vat'];
        $order->deviceType = $this->Request()->getDeviceType();

        $order->sDeleteTemporaryOrder();    // Delete previous temporary orders
        $order->sCreateTemporaryOrder();    // Create new temporary order
    }

    /**
     * Get current selected country - if no country is selected, choose first one from list
     * of available countries
     *
     * @return array with country information
     */
    public function getSelectedCountry()
    {
        $session = Shopware()->Session();

        if (!empty($this->View()->sUserData['additional']['countryShipping'])) {
            $session['sCountry'] = (int)$this->View()->sUserData['additional']['countryShipping']['id'];
            $session['sArea'] = (int)$this->View()->sUserData['additional']['countryShipping']['areaID'];

            return $this->View()->sUserData['additional']['countryShipping'];
        }
        $countries = $this->getCountryList();
        if (empty($countries)) {
            unset($this->session['sCountry']);
            return false;
        }
        $country = reset($countries);
        $session['sCountry'] = (int)$country['id'];
        $session['sArea'] = (int)$country['areaID'];
        $this->View()->sUserData['additional']['countryShipping'] = $country;
        return $country;
    }

    /**
     * Get complete user-data as an array to use in view
     *
     * @return array
     */
    public function getUserData()
    {
        $system = Shopware()->System();
        $admin = Shopware()->Modules()->Admin();
        $userData = $admin->sGetUserData();
        if (!empty($userData['additional']['countryShipping'])) {
            $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow("
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ", [$system->sUSERGROUP]);

            if ($this->isTaxFreeDelivery($userData)) {
                $system->sUSERGROUPDATA['tax'] = 0;
                $system->sCONFIG['sARTICLESOUTPUTNETTO'] = 1; //Old template
                Shopware()->Session()->sUserGroupData = $system->sUSERGROUPDATA;
                $userData['additional']['charge_vat'] = false;
                $userData['additional']['show_net'] = false;
                Shopware()->Session()->sOutputNet = true;
            } else {
                $userData['additional']['charge_vat'] = true;
                $userData['additional']['show_net'] = !empty($system->sUSERGROUPDATA['tax']);
                Shopware()->Session()->sOutputNet = empty($system->sUSERGROUPDATA['tax']);
            }
        }

        return $userData;
    }


    /**
     * Validates if the provided customer should get a tax free delivery
     * @param array $userData
     * @return bool
     */
    protected function isTaxFreeDelivery($userData)
    {
        if (!empty($userData['additional']['countryShipping']['taxfree'])) {
            return true;
        }

        if (empty($userData['additional']['countryShipping']['taxfree_ustid'])) {
            return false;
        }

        return !empty($userData['shippingaddress']['ustid']);
    }

    /**
     * @param int $orderNumber
     * @param string $source
     *
     * @return array
     */
    private function getOrderAddress($orderNumber, $source)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $builder */
        $builder = $this->get('dbal_connection')->createQueryBuilder();
        $context = $this->get('shopware_storefront.context_service')->getShopContext();

        $sourceTable = $source === 'billing' ? 's_order_billingaddress' : 's_order_shippingaddress';

        $address = $builder->select(['address.*'])
            ->from($sourceTable, 'address')
            ->join('address', 's_order', '', 'address.orderID = s_order.id AND s_order.ordernumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->execute()
            ->fetch();

        $countryStruct = $this->get('shopware_storefront.country_gateway')->getCountry($address['countryID'], $context);
        $stateStruct = $this->get('shopware_storefront.country_gateway')->getState($address['stateID'], $context);

        $address['country'] = json_decode(json_encode($countryStruct), true);
        $address['state'] = json_decode(json_encode($stateStruct), true);
        $address['attribute'] = $this->get('shopware_attribute.data_loader')->load($sourceTable . '_attributes', $address['id']);

        return $address;
    }

    /**
     * @param array $addressA
     * @param array $addressB
     *
     * @return bool
     */
    private function areAddressesEqual(array $addressA, array $addressB)
    {
        $unset = ['id', 'customernumber', 'phone', 'ustid'];
        foreach ($unset as $key) {
            unset($addressA[$key], $addressB[$key]);
        }

        return count(array_diff($addressA, $addressB)) == 0;
    }

    /**
     * Add voucher to cart
     *
     * At failure view variable sVoucherError will give further information
     * At success return to cart / confirm view
     */
    public function addVoucherAction()
    {
        $basketObj = $this->get('modules')->Basket();
        if ($this->Request()->isPost()) {
            $voucher = $basketObj->sAddVoucher($this->Request()->getParam('sVoucher'));
            if (!empty($voucher['sErrorMessages'])) {
                $this->View()->assign('sVoucherError', $voucher['sErrorMessages'], null, Smarty::SCOPE_ROOT);
            }
        }
        $this->forward('index');
    }


}