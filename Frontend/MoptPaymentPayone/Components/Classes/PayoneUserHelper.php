<?php

/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 12.06.17
 * Time: 10:13
 */
class Mopt_PayoneUserHelper
{

    /**
     * @var Shopware\Components\DependencyInjection\Container
     */
    protected $container;
    protected $moptPayone__helper = null;
    protected $admin;


    /**
     * init
     */
    public function __construct()
    {
        $this->moptPayone__helper = Mopt_PayoneMain::getInstance()->getHelper();
        $this->admin = Shopware()->Modules()->Admin();
    }

    public function createUserWithoutAccount($apiResponse, $paymentId)
    {
        $session = Shopware()->Session();
        $payData = $apiResponse->getPaydata()->toAssocArray();
        $register = $this->extractData($payData);
        $register["payment"]["object"]["id"] = $paymentId;

        $session['sRegister'] = $register;
        $session['sRegisterFinished'] = false;

        $newdata = $this->saveUser($register, $paymentId);
        $this->admin->sSYSTEM->_POST = $newdata['auth'];
        $this->admin->sLogin(true);
        $this->savePayment($paymentId);
    }

    /**
     * Saves a new user to the system.
     *
     * @param array $data
     */
    public function saveUser($data, $paymentId)
    {

        $plainbilling = array_merge($data['auth'], $data['billing']);
        $plainshipping = array_merge($data['auth'], $data['shipping']);
        $adressesEqual = (
            $data['billing']['firstname'] == $data['shipping']['firstname'] &&
            $data['billing']['lastname'] == $data['shipping']['lastname'] &&
            $data['billing']['city'] == $data['shipping']['city'] &&
            $data['billing']['street'] == $data['shipping']['street'] &&
            $data['billing']['zipcode'] == $data['shipping']['zipcode']
        ) ;

        //Create forms and validate the input
        $customer = new Shopware\Models\Customer\Customer();
        $form = $this->createForm('Shopware\Bundle\AccountBundle\Form\Account\PersonalFormType', $customer);
        $form->submit($plainbilling);

        $billingaddress = new Shopware\Models\Customer\Address();
        $form = $this->createForm('Shopware\Bundle\AccountBundle\Form\Account\AddressFormType', $billingaddress);
        $form->submit($plainbilling);

        if (! $adressesEqual) {
            $shippingaddress = new Shopware\Models\Customer\Address();
            $form = $this->createForm('Shopware\Bundle\AccountBundle\Form\Account\AddressFormType', $shippingaddress);
            $form->submit($plainshipping);
        } else {
            $shippingaddress = $billingaddress;
        }

        /** @var Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface $context */
        $context =  Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        /** @var Shopware\Bundle\StoreFrontBundle\Struct\Shop $shop */
        $shop = $context->getShop();

        /** @var Shopware\Bundle\AccountBundle\Service\RegisterServiceInterface $registerService */
        $registerService =  Shopware()->Container()->get('shopware_account.register_service');
        $registerService->register($shop, $customer, $billingaddress, $shippingaddress);

        // get updated password; it is md5 randomized after register
        // make sure user is the last created user in case of already registered email addresses
        $getUser = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(
            array('email' =>  $data['auth']['email']), array('lastLogin' => 'DESC')
        );
        // Update PaymentId
        $getUser->setPaymentId($paymentId);
        Shopware()->Models()->persist($getUser);
        Shopware()->Models()->flush();


        // $data['auth']['password']= $getUser->getPassword();
        $data['auth']['passwordMD5']= $getUser->getPassword();
        $data['auth']['encoderName'] = 'md5';
        return $data;
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type
     * @param mixed  $data    The initial data for the form
     * @param array  $options Options for the form
     *
     * @return Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return  Shopware()->Container()->get('shopware.form.factory')->create($type, $data, $options);
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
        $register['billing']['city']           = $personalData['billing_city'];
        $register['billing']['country']        = $this->moptPayone__helper->getCountryIdFromIso($personalData['billing_country']);
        if ($personalData['shipping_state'] !== 'Empty') {
            $register['billing']['stateID']      = $this->moptPayone__helper->getStateFromId($register['billing']['country'], $personalData['billing_state']);
        }
        $register['billing']['street']         = $personalData['billing_street'];
        $register['billing']['additionalAddressLine1'] = $personalData['billing_addressaddition'];
        $register['billing']['zipcode']        = $personalData['billing_zip'];
        $register['billing']['firstname']      = $personalData['billing_firstname'];
        $register['billing']['lastname']       = $personalData['billing_lastname'];
        $register['billing']['phone']       = $personalData['shipping_telephonenumber'];
        $register['billing']['salutation']     = 'mr';
        if (isset($personalData['billing_company']) && !empty($personalData['billing_company'])) {
            $register['billing']['company']        = $personalData['billing_company'];
        } else {
            $register['billing']['company']        = '';
            $register['personal']['customer_type'] = 'private';
        }
        $register['personal']['email']         = $personalData['email'];
        $register['personal']['firstname']     = $personalData['billing_firstname'];
        $register['personal']['lastname']      = $personalData['billing_lastname'];
        $register['personal']['salutation']    = 'mr';
        $register['personal']['skipLogin']     = 1;
        $register['shipping']['salutation']   = 'mr';
        $register['shipping']['firstname']    = $personalData['shipping_firstname'];
        $register['shipping']['lastname']     = $personalData['shipping_lastname'];
        $register['shipping']['street']       = $personalData['shipping_street'];
        $register['shipping']['additionalAddressLine1'] = $personalData['shipping_addressaddition'];
        $register['shipping']['zipcode']      = $personalData['shipping_zip'];
        $register['shipping']['city']         = $personalData['shipping_city'];
        $register['shipping']['country']      = $this->moptPayone__helper->getCountryIdFromIso($personalData['shipping_country']);
        if (isset($personalData['shipping_company']) && !empty($personalData['shipping_company'])) {
            $register['shipping']['company']        = $personalData['shipping_company'];
        } else {
            $register['shipping']['company']        = '';
            $register['personal']['customer_type'] = 'private';
        }
        $register['shipping']['department']   = '';
        $register['shipping']['phone']       = $personalData['shipping_telephonenumber'];
        $register['auth']['email']            = $personalData['email'];
        $register['auth']['password']         = md5(uniqid('', true));
        $register['auth']['accountmode']      = 1;
        $register['auth']['encoderName']      = '';
        return $register;
    }

    /**
     * Helper method to set the selected payment-method into the session to change it in the customer-account after logging in
     *
     * @param $paymentId
     * @throws Enlight_Exception
     */
    public function savePayment($paymentId)
    {
        $admin = Shopware()->Modules()->Admin();
        $admin->sSYSTEM->_POST['sPayment'] = $paymentId;
        $admin->sUpdatePayment($paymentId);

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
        $em = Shopware()->Models();

        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = $em->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));

        /** @var \Shopware\Models\Customer\Address $address */
        $address = $customer->getDefaultBillingAddress();

        /** @var \Shopware\Models\Country\Country $country */
        // $country = $address->getCountry();
        $country = $em->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('id' => $billingData['country'] ));
        $billingData['country'] = $country;
        $address->fromArray($billingData);

        Shopware()->Container()->get('shopware_account.address_service')->update($address);
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
        $em = Shopware()->Models();

        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = $em->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));

        /** @var \Shopware\Models\Customer\Address $address */
        $address = $customer->getDefaultShippingAddress();

        /** @var \Shopware\Models\Country\Country $country */
//      $country = $address->getCountry();
        $country = $em->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('id' => $shippingData['country'] ));

        $shippingData['country'] = $country;
        $address->fromArray($shippingData);

        Shopware()->Container()->get('shopware_account.address_service')->update($address);
    }

    /**
     * Endpoint for changing the main profile data
     */
    public function updateCustomer($data, $paymentId)
    {
        unset ($data['shipping']);
        unset ($data['billing']);

        $userId = Shopware()->Container()->get('session')->get('sUserId');

        $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(
            array('id' =>  $userId)
        );
        $customer->fromArray($data);
        Shopware()->Container()->get('shopware_account.customer_service')->update($customer);
        $this->savePayment($paymentId);
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
        $this->updateCustomer($personalData, $paymentId);

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
        $this->updateBilling($userId, $personalData['billing']);
        return true;
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
        $this->updateShipping($userId, $personalData['shipping']);
        return true;
    }

    public function createrOrUpdateUser($apiResponse, $paymentId, $session)
    {
        $payData = $apiResponse->getPaydata()->toAssocArray();

        if (!$this->isUserLoggedIn($session)) {
            $this->createUserWithoutAccount($apiResponse, $paymentId);
        } else {
            $user = $this->updateUserAddresses($payData, $session, $paymentId);
        }
        $user = $this->getUserData();
        $user['sUserData']['additional']['charge_vat'] = true;
        $user['sUserData']['additional']['user']['paymentID'] = $paymentId;
        $user['additional']['charge_vat'] = true;
        $user['additional']['user']['paymentID'] = $paymentId;
        $user['additional']['user']['payment']['id'] = $paymentId;
    }

    protected function isUserLoggedIn($session)
    {
        return (isset($session->sUserId) && !empty($session->sUserId));
    }

    /**
     * get complete user-data as array to use in view
     *
     * @return array
     */
    protected function getUserData()
    {
        $system = Shopware()->System();
        $userData = $this->admin->sGetUserData();
        if (!empty($userData['additional']['countryShipping'])) {
            $sTaxFree = false;
            if (!empty($userData['additional']['countryShipping']['taxfree'])) {
                $sTaxFree = true;
            } elseif (!empty($userData['additional']['countryShipping']['taxfree_ustid'])
                && !empty($userData['billingaddress']['ustid'])
            ) {
                $sTaxFree = true;
            }

            $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow("
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ", array($system->sUSERGROUP));

            if (!empty($sTaxFree)) {
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

}