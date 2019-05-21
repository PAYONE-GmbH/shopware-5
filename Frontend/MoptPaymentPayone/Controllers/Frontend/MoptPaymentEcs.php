<?php

class Shopware_Controllers_Frontend_MoptPaymentEcs extends Shopware_Controllers_Frontend_Payment
{

    protected $moptPayone__serviceBuilder = null;
    /** @var Mopt_PayoneMain $moptPayone__main */
    protected $moptPayone__main = null;
    protected $moptPayone__helper = null;
    protected $moptPayone__paymentHelper = null;
    protected $admin;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();
        $this->admin = Shopware()->Modules()->Admin();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    public function initPaymentAction()
    {
        $session = Shopware()->Session();
        $paymentId = $session->moptPaypayEcsPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();

        $userData = $this->getUserData();
        $amount = $this->getBasketAmount($userData);
        
        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckout(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        // Response with new workorderid and redirect-url to paypal
        $response = $service->request($request);

        if ($response->getStatus() === Payone_Api_Enum_ResponseType::REDIRECT) {
            $session->moptPaypalEcsWorkerId = $response->getWorkorderId();
            $this->redirect($response->getRedirecturl());
        } else {
            return $this->forward('ecsAbort');
        }
    }

    /**
     * get plugin bootstrap
     *
     * @return plugin
     */
    protected function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * user returns succesfully from paypal
     * retrieve userdata now
     */
    public function ecsAction()
    {
        $session = Shopware()->Session();
        $paymentId = $session->moptPaypayEcsPaymentId;
        $paramBuilder = $this->moptPayone__main->getParamBuilder();
        
        $userData = $this->getUserData();
        $amount = $this->getBasketAmount($userData);
        
        $expressCheckoutRequestData = $paramBuilder->buildPayPalExpressCheckoutDetails(
            $paymentId,
            $this->Front()->Router(),
            $amount,
            $this->getCurrencyShortName(),
            $userData,
            $session->moptPaypalEcsWorkerId
        );

        $request = new Payone_Api_Request_Genericpayment($expressCheckoutRequestData);

        $builder = $this->moptPayone__serviceBuilder;
        $service = $builder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        
        $response = $service->request($request);
        
        if ($response->getStatus() === Payone_Api_Enum_ResponseType::OK) {
            $session = Shopware()->Session();
            $session->offsetSet('moptFormSubmitted', true);
            $this->createrOrUpdateAndForwardUser($response, $paymentId, $session);
        } elseif ($response->getStatus() === Payone_Api_Enum_ResponseType::REDIRECT) {
            $session->moptPaypalEcsWorkerId = $response->getWorkorderId();
            $this->redirect($response->getRedirecturl());
        } else {
            return $this->forward('ecsAbort');
        }
    }

    public function ecsAbortAction()
    {
        $session = Shopware()->Session();
        $session->moptPayPalEcsError = true;
        unset($session->moptPaypalEcsWorkerId);

        return $this->redirect(array('controller' => 'checkout', 'action' => 'cart'));
    }

    /**
     * get complete user-data as array to use in view
     *
     * @return array
     */
    protected function getUserData()
    {
        $system = Shopware()->System();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $countryShipping = $userData['additional']['countryShipping'];

        if (empty($countryShipping)) {
            return $userData;
        }

        $sTaxFree = (
            !empty($countryShipping['taxfree']) ||
            !empty($countryShipping['taxfree_ustid']) &&
            !empty($userData['billingaddress']['ustid'])
        );

        $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow(
            'SELECT * FROM s_core_customergroups
              WHERE groupkey = ?',
            array($system->sUSERGROUP)
        );

        $userData['additional']['show_net'] = false;
        $userData['additional']['charge_vat'] = false;
        Shopware()->Session()->offsetSet('sOutputNet', true);

        if (!empty($sTaxFree)) {
            $system->sUSERGROUPDATA['tax'] = 0;
            $this->container->get('config')->offsetSet('sARTICLESOUTPUTNETTO', 1);
            Shopware()->Session()->offsetSet('sUserGroupData', $system->sUSERGROUPDATA);
        } else {
            $userData['additional']['charge_vat'] = true;
            $userData['additional']['show_net'] = !empty($system->sUSERGROUPDATA['tax']);
            Shopware()->Session()->offsetSet('sOutputNet', empty($system->sUSERGROUPDATA['tax']));
        }

        return $userData;
    }

    /**
     * Return the full amount to pay.
     *
     * @return float
     */
    protected function getBasketAmount($userData)
    {
        $basket = $this->moptPayone__main->sGetBasket();
        
        if (empty($userData['additional']['charge_vat'])) {
            return $basket['AmountNetNumeric'];
        }

        return empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
    }

    protected function createrOrUpdateAndForwardUser($apiResponse, $paymentId, $session)
    {
        $payData = $apiResponse->getPaydata()->toAssocArray();

        if ($this->isUserLoggedIn($session)) {
            $user = $this->updateUserAddresses($payData, $session, $paymentId);
            if ($user === null) {
                return $this->ecsAbortAction();
            }
        } else {
            $this->createUserWithoutAccount($payData, $session, $paymentId);
        }

        Shopware()->Session()->sPaymentID = $session->moptPaypayEcsPaymentId;

        $user = $this->getUserData();
        //set user data
        $user['additional']['charge_vat'] = true;
        //set payment id
        $user['additional']['user']['paymentID'] = $paymentId;

        return $this->redirect(array('controller' => 'checkout', 'action' => 'confirm'));
    }

    /**
     * Checks if user is logged in
     * @param $session
     * @return bool
     */
    protected function isUserLoggedIn($session)
    {
         return (isset($session->sUserId) && !empty($session->sUserId));
    }

    /**
     * create / register user without login
     */
    protected function createUserWithoutAccount($personalData, $session, $paymentId)
    {
        $register = $this->extractData($personalData);
        $register['payment']['object']['id'] = $paymentId;

        $session['sRegister'] = $register;
        $session['sRegisterFinished'] = false;
        
        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
            $newdata = $this->saveUser($register,$paymentId);
            $this->admin->sSYSTEM->_POST = $newdata['auth'];
            $this->admin->sLogin(true);            
        } else {
            $this->admin->sSaveRegister();
        }
    }
  
    protected function updateUserAddresses($personalData, $session, $paymentId)
    {
        $personalData = $this->extractData($personalData);
        // use old phone number in case phone number is required
        if (Shopware()->Config()->get('requirePhoneField')) {
            $oldUserData = $this->admin->sGetUserData();
            $personalData['billing']['phone'] = $oldUserData['billingaddress']['phone'];
        }
        $updated = $this->updateBillingAddress($personalData, $session);
        if (!$updated) {
            return null;
        }
        $updated = $this->updateShippingAddress($personalData, $session);
        if (!$updated) {
            return null;
        }        
        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
            $this->updateCustomer($personalData, $paymentId);
        }         
        return $personalData;
    }
  
    protected function updateBillingAddress($personalData, $session)
    {
        $userId = $session->offsetGet('sUserId');
        $countryData = $this->admin->sGetCountryList();
        $countryIds = array();
        foreach ($countryData as $key => $country) {
            $countryIds[$key] = $country['id'];
        }
        $this->admin->sSYSTEM->_POST  = $personalData['billing'];
        $rules = array(
                'salutation'=>array('required'=>1),
                'firstname'=>array('required'=>1),
                'lastname'=>array('required'=>1),
                'street'=>array('required'=>1),
                'zipcode'=>array('required'=>1),
                'city'=>array('required'=>1),
                'phone'=>array('required'=> intval(Shopware()->Config()->get('requirePhoneField'))),
                'country'=>array('required' => 1, 'in' => $countryIds)
            );
        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {            
            $this->updateBilling($userId, $personalData['billing']);
            return true;                        
        } else {            
            $checkData = $this->admin->sValidateStep2($rules, true);
            if (!empty($checkData['sErrorMessages'])) {
                $this->View()->sErrorFlag = $checkData['sErrorFlag'];
                $this->View()->sErrorMessages = $checkData['sErrorMessages'];
                return false;
            } else {
                $this->admin->sUpdateBilling();
                return true;
            }
        }
    }
  
    protected function updateShippingAddress($personalData, $session)
    {
        $userId = $session->offsetGet('sUserId');
        $rules = array(
        'salutation'=>array('required'=>1),
        'firstname'=>array('required'=>1),
        'lastname'=>array('required'=>1),
        'street'=>array('required'=>1),
        'zipcode'=>array('required'=>1),
        'city'=>array('required'=>1)
        );
        $this->admin->sSYSTEM->_POST = $personalData['shipping'];
        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {            
            $this->updateShipping($userId, $personalData['billing']);
            return true;                        
        } else {      
            $checkData = $this->admin->sValidateStep2ShippingAddress($rules, true);
            if (!empty($checkData['sErrorMessages'])) {
                $this->View()->sErrorFlag = $checkData['sErrorFlag'];
                $this->View()->sErrorMessages = $checkData['sErrorMessages'];
                return false;
            } else {
                $this->admin->sUpdateShipping();
                return true;
            }
        }  
    }
  
  /**
   * get user-data as array from response
   *
   * @param array $personalData
   * @return array
   */
    protected function extractData($personalData)
    {
        $register = array();
        $register['billing']['city']           = $personalData['shipping_city'];
        $register['billing']['country']        = $this->moptPayone__helper->getCountryIdFromIso($personalData['shipping_country']);
        if ($personalData['shipping_state'] !== 'Empty') {
            $register['billing']['state']      = $this->moptPayone__helper->getStateFromId($register['billing']['country'], $personalData['shipping_state'], true);
        }
        $register['billing']['street']         = $personalData['shipping_street'];
        $register['billing']['additionalAddressLine1'] = $personalData['shipping_addressaddition'];
        $register['billing']['zipcode']        = $personalData['shipping_zip'];
        $register['billing']['firstname']      = $personalData['shipping_firstname'];
        $register['billing']['lastname']       = $personalData['shipping_lastname'];
        $register['billing']['salutation']     = 'mr';
        if (isset($personalData['shipping_company']) && !empty($personalData['shipping_company'])) {
            $register['billing']['company']        = $personalData['shipping_company'];
        } else {
            $register['billing']['company']        = '';
            $register['personal']['customer_type'] = 'private';
        }
        $register['personal']['email']         = $personalData['email'];
        $register['personal']['firstname']     = $personalData['shipping_firstname'];
        $register['personal']['lastname']      = $personalData['shipping_lastname'];
        $register['personal']['salutation']    = 'mr';
        $register['personal']['skipLogin']     = 1;
        $register['shipping']['salutation']   = 'mr';
        $register['shipping']['firstname']    = $register['billing']['firstname'];
        $register['shipping']['lastname']     = $register['billing']['lastname'];
        $register['shipping']['street']       = $register['billing']['street'];
        $register['shipping']['additionalAddressLine1'] = $personalData['shipping_addressaddition'];
        $register['shipping']['zipcode']      = $register['billing']['zipcode'];
        $register['shipping']['city']         = $register['billing']['city'];
        $register['shipping']['country']      = $register['billing']['country'];
        if ($personalData['shipping_state'] !== 'Empty') {
            $register['shipping']['state']  = $register['billing']['state'];
        }
        $register['shipping']['company']      = $register['billing']['company'];
        $register['shipping']['department']   = '';
        $register['auth']['email']            = $personalData['email'];
        $register['auth']['password']         = md5(uniqid('', true));
        $register['auth']['accountmode']      = 1;
        $register['auth']['encoderName']      = '';
        return $register;
    }
    
    /**
     * Saves a new user to the system.
     *
     * @param array $data
     */
    private function saveUser($data, $paymentId)
    {

        $plain = array_merge($data['auth'], $data['billing']);

        //Create forms and validate the input
        $customer = new Shopware\Models\Customer\Customer();
        $form = $this->createForm('Shopware\Bundle\AccountBundle\Form\Account\PersonalFormType', $customer);
        $form->submit($plain);

        $address = new Shopware\Models\Customer\Address();
        $form = $this->createForm('Shopware\Bundle\AccountBundle\Form\Account\AddressFormType', $address);
        $form->submit($plain);

        
        /** @var Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface $context */
        $context = $this->get('shopware_storefront.context_service')->getShopContext();

        /** @var Shopware\Bundle\StoreFrontBundle\Struct\Shop $shop */
        $shop = $context->getShop();

        /** @var Shopware\Bundle\AccountBundle\Service\RegisterServiceInterface $registerService */
        $registerService = $this->get('shopware_account.register_service');
        $registerService->register($shop, $customer, $address, $address);

        // get updated password; it is md5 randomized after register
        // make sure user is the last created user in case of already registered email addresses
	    $getUser = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(
		    array('email' =>  $data['auth']['email']), array('lastLogin' => 'DESC')
		);
        // Update PaymentId 
        $getUser->setPaymentId($paymentId);
        Shopware()->Models()->persist($getUser);
        Shopware()->Models()->flush();
        

       $data['auth']['password']= $getUser->getPassword();
       $data['auth']['passwordMD5']= $getUser->getPassword();
       $data['auth']['encoderName'] = 'md5';
       return $data;
    }

    /**
     * Updates the billing address
     *
     * @param int $userId
     * @param array $billingData
     */
    private function updateBilling($userId, $billingData)
    {
        /** @var \Shopware\Components\Model\ModelManager $em */
        $em = $this->get('models');

        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = $em->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));

        /** @var \Shopware\Models\Customer\Address $address */
        $address = $customer->getDefaultBillingAddress();
        
         /** @var \Shopware\Models\Country\Country $country */
        $country = $em->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('id' => $billingData['country'] ));
        $countryState = $em->getRepository('\Shopware\Models\Country\State')->findOneBy(array('id' => $billingData['state'] ));
        $billingData['country'] = $country;
        $billingData['state'] = $countryState;
        $address->fromArray($billingData);

        $this->get('shopware_account.address_service')->update($address);
    }
    
    /**
     * Updates the shipping address
     *
     * @param int $userId
     * @param array $shippingData
     */
    private function updateShipping($userId, $shippingData)
    {
        /** @var \Shopware\Components\Model\ModelManager $em */
        $em = $this->get('models');

        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = $em->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));

        /** @var \Shopware\Models\Customer\Address $address */
        $address = $customer->getDefaultShippingAddress();
        
         /** @var \Shopware\Models\Country\Country $country */
        $country = $em->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('id' => $shippingData['country'] ));
        $countryState = $em->getRepository('\Shopware\Models\Country\State')->findOneBy(array('id' => $shippingData['state'] ));
        $shippingData['country'] = $country;
        $shippingData['state'] = $countryState;
        $address->fromArray($shippingData);

        $this->get('shopware_account.address_service')->update($address);
    } 
    
    /**
     * Endpoint for changing the main profile data
     */
    public function updateCustomer($data, $paymentId)
    {
        unset ($data['shipping']);
        unset ($data['billing']);        
        
        $userId = $this->get('session')->get('sUserId');

        $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(
		array('id' =>  $userId)
		);
        $customer->fromArray($data);
        $customer->setPaymentId($paymentId);
        Shopware()->Container()->get('shopware_account.customer_service')->update($customer);
    }
}
