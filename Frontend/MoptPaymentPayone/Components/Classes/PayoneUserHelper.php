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
    protected $moptPayone__main = null;
    protected $moptPayone__helper = null;
    protected $admin;


    /**
     * init
     */
    public function __construct()
    {
        $this->moptPayone__main = Mopt_PayoneMain::getInstance();
        $this->moptPayone__helper = Mopt_PayoneMain::getInstance()->getHelper();

        $this->admin = Shopware()->Modules()->Admin();
    }

    /**
     * @param $apiResponse
     * @param $paymentId
     * @param $session
     * @return bool $success
     * @throws Enlight_Exception
     */
    public function createUserWithoutAccount($apiResponse, $paymentId, $session)
    {
        $register = $this->extractData($apiResponse, $paymentId);

        $success = $this->checkAllowedCountries($register, $paymentId, $session);
        if (!$success) {
            return $success;
        }

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
     * @param $paymentId
     * @return array
     */
    protected function extractData(array $personalData, $paymentId)
    {
        // uncomment to simulate missing paypal phone number
        // unset($personalData['telephonenumber']);
        $isPhoneMandatory = Shopware()->Config()->get('requirePhoneField');

        // paypal express does not use the billing_ prefix so rename the keys
        // also split lastname value into firstname and lastname
        if (!isset($personalData['billing_street']) && isset($personalData['street'])) {
            $keys = [ 'street', 'city', 'country', 'zip', 'addressaddition', 'lastname', 'telephonenumber'];
            foreach ($keys AS $origKey) {
                if ($origKey !== 'lastname') {
                    $personalData['billing_' . $origKey] = (!empty($personalData[$origKey])) ? $personalData[$origKey] : $personalData['shipping_' . $origKey];
                } else {
                    $splitName = explode(' ', $personalData[$origKey]);
                    $personalData['billing_' . $origKey] = $splitName[1];
                    $personalData['billing_firstname'] = $splitName[0];
                }
                unset($personalData[$origKey]);
            }
            $personalData['shipping_telephonenumber'] = $personalData['billing_telephonenumber'];
        }
        // paydirekt express uses streetname and streetnumber as keys so we merge them to street
        // also user email is read from buyer_email instead of email
        if (isset($personalData['billing_streetname'])) {
            $keys = [ 'streetname'];
            foreach ($keys AS $origKey) {
                if ($origKey === 'streetname') {
                    $personalData['billing_street'] = $personalData['billing_streetname'] . ' ' . $personalData['billing_streetnumber'] ;
                    $personalData['shipping_street'] = $personalData['shipping_streetname'] . ' ' . $personalData['shipping_streetnumber'] ;
                    unset($personalData['billing_streetname']);
                    unset($personalData['billing_streetnumber']);
                    unset($personalData['shipping_streetname']);
                    unset($personalData['shipping_streetnumber']);
                }
            }
            $personalData['email'] = $personalData['buyer_email'];
            unset($personalData['buyer_email']);
        }
        // enable special state handling for paypal express
        $paymentHelper = Mopt_PayoneMain::getInstance()->getPaymentHelper();
        $paymentName = $paymentHelper->getPaymentNameFromId($paymentId);
        $isPaypalECS = $paymentHelper->isPayonePaypalExpress($paymentName);

        $register = array();
        $register['billing']['city']           = $personalData['billing_city'];
        $register['billing']['country']        = $this->moptPayone__helper->getCountryIdFromIso($personalData['billing_country']);
        if (!empty($personalData['billing_state']) && $personalData['billing_state'] !== 'Empty') {
            // first try to get state by countryId, then by name
            $state = $this->moptPayone__helper->getStateFromId($register['billing']['country'], $personalData['billing_state'], $isPaypalECS);
            if (empty($state)) {
                $register['billing']['state'] = $this->moptPayone__helper->getStateFromStatename($register['billing']['country'], $personalData['billing_state'], $isPaypalECS);
            } else {
                $register['billing']['state'] = $state;
            }
        }
        $register['billing']['street']         = $personalData['billing_street'];
        $register['billing']['additionalAddressLine1'] = $personalData['billing_addressaddition'];
        // Paydirekt Express
        if (! empty( $personalData['billing_additionaladdressinformation'])) {
            $register['billing']['additionalAddressLine1'] = $personalData['billing_additionaladdressinformation'];
        }
        $register['billing']['zipcode']        = $personalData['billing_zip'];
        $register['billing']['firstname']      = $personalData['billing_firstname'];
        $register['billing']['lastname']       = $personalData['billing_lastname'];
        if ($isPhoneMandatory) {
            $register['billing']['phone'] = !empty($personalData['billing_telephonenumber']) ? $personalData['billing_telephonenumber'] : '00000000';
            $register['shipping']['phone'] = !empty($personalData['shipping_telephonenumber']) ? $personalData['shipping_telephonenumber'] : '00000000';
        } else {
            $register['billing']['phone'] = $personalData['billing_telephonenumber'];
            $register['shipping']['phone'] = $personalData['shipping_telephonenumber'];
        }

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
        // Paydirekt Express
        if (! empty( $personalData['shipping_additionaladdressinformation'])) {
            $register['shipping']['additionalAddressLine1'] = $personalData['shipping_additionaladdressinformation'];
        }
        $register['shipping']['zipcode']      = $personalData['shipping_zip'];
        $register['shipping']['city']         = $personalData['shipping_city'];
        $register['shipping']['country']      = $this->moptPayone__helper->getCountryIdFromIso($personalData['shipping_country']);
        if ($personalData['shipping_state'] !== 'Empty') {
            $shippingState = $this->moptPayone__helper->getStateFromId($register['shipping']['country'], $personalData['shipping_state'], $isPaypalECS);
            if (empty($shippingState)) {
                $register['shipping']['state'] = $this->moptPayone__helper->getStateFromStatename($register['shipping']['country'], $personalData['shipping_state'], $isPaypalECS);
            } else {
                $register['shipping']['state'] = $shippingState;
            }
        }
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
        $country = $em->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('id' => $billingData['country'] ));
        $countryState = $em->getRepository('\Shopware\Models\Country\State')->findOneBy(array('id' => $billingData['state'] ));
        $billingData['country'] = $country;
        $billingData['state'] = $countryState;
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

        $countryState = Shopware()->Models()->getRepository('\Shopware\Models\Country\State')->findOneBy(array('id' => $shippingData['state']));
        $shippingData['country'] = $country;
        $shippingData['state'] = $countryState;
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
        $paymentHelper = new Mopt_PayonePaymentHelper();
        $paymentName = $paymentHelper->getPaymentNameFromId($paymentId);
        $oldUserData = $this->admin->sGetUserData();

        $session->moptPayoneUserHelperError = false;
        $register = $this->extractData($personalData, $paymentId);
        $success = $this->checkAllowedCountries($register, $paymentId, $session);
        if (!$success) {
            return $success;
        }

        // TODO: test missing changes from PR #418 Devolo Amazonpay changes
        if (strpos($paymentName, 'mopt_payone__ewallet_amazon_pay') === 0 && array_key_exists('shipping_pobox', $personalData) && !empty($personalData['shipping_pobox'])) {
            $personalData['shipping_company'] = $personalData['shipping_pobox'];
        }

        if  (strpos($paymentName, 'mopt_payone__ewallet_amazon_pay') === 0 &&
            array_key_exists('shipping_company', $personalData) &&
            !empty($personalData['shipping_company']) &&
            preg_match('~[0-9]+~', $personalData['shipping_company'])) {
            $personalData['shipping_addressaddition'] = $personalData['shipping_company'];
            unset($personalData['shipping_company']);
        }

        if  (strpos($paymentName, 'mopt_payone__ewallet_amazon_pay') === 0 &&
            array_key_exists('shipping_company', $personalData) &&
            !empty($personalData['shipping_company']) &&
            preg_match('~c/o~', $personalData['shipping_company'])) {
            $personalData['shipping_addressaddition'] = $personalData['shipping_company'];
            unset($personalData['shipping_company']);
        }

        // in some cases the api does not provide billing address data when using amazonpay
        // since the user is already logged in use existing billing address instead
        // uncomment the following lines to test this
        // unset($personalData['billing_street']);
        // unset($personalData['billing_country']);
        if (strpos($paymentName, 'mopt_payone__ewallet_amazon_pay') === 0 && empty($personalData['billing_street'])) {
            $personalData['billing_city'] = $oldUserData['billingaddress']['city'];
            $personalData['billing_country'] = $this->moptPayone__helper->getCountryIsoFromId($oldUserData['billingaddress']['country']['id']);
            $personalData['billing_street'] = $oldUserData['billingaddress']['street'];
            $personalData['billing_addressaddition'] = $oldUserData['billingaddress']['additionalAddressLine1'];
            $personalData['billing_zip'] = $oldUserData['billingaddress']['zipcode'];
            $personalData['billing_firstname'] = $oldUserData['billingaddress']['firstname'];
            $personalData['billing_lastname'] = $oldUserData['billingaddress']['lastname'];
            $personalData['billing_phone'] = $oldUserData['billingaddress']['phone'];
            $personalData['billing_company'] = $oldUserData['billingaddress']['company'];
        }

        $personalData = $this->extractData($personalData,$paymentId);

        // use old phone number in case phone number is required
        if (Shopware()->Config()->get('requirePhoneField')) {
            $personalData['billing']['phone'] = $oldUserData['billingaddress']['phone'];
        }

        $updated = $this->updateBillingAddress($personalData, $session);
        if (!$updated) {
            return null;
        }
        // amazonpay: check if original billingaddress is the same as shipping address
        // in this case add a new shipping address
        if ((strpos($paymentName, 'mopt_payone__ewallet_amazon_pay') === 0 || strpos($paymentName, 'mopt_payone__ewallet_paypal_express') === 0 || strpos($paymentName, 'mopt_payone__ewallet_paydirekt_express') === 0) &&
            $oldUserData['billingaddress']['id'] == $oldUserData['shippingaddress']['id']) {
            $this->saveSeperateShippingAddress($personalData, $session);
        } else {
            $updated = $this->updateShippingAddress($personalData, $session);
            if (!$updated) {
                return null;
            }
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

    /**
     * @param Payone_Api_Response_Genericpayment_Ok $apiResponse
     * @param int $paymentId
     * @param $session
     * @return array|bool|null $success
     * @throws Enlight_Exception
     */
    public function createOrUpdateUser($apiResponse, $paymentId, $session)
    {
        $payData = $apiResponse->getPaydata()->toAssocArray();

        if (!$this->isUserLoggedIn($session)) {
            $success = $this->createUserWithoutAccount($payData, $paymentId, $session);
        } else {
            $success = $this->updateUserAddresses($payData, $session, $paymentId);
        }
        Shopware()->Session()->sPaymentID = $paymentId;

        return $success;
    }

    /**
     * Checks if user is logged in
     * @param $session
     * @return bool
     */
    public function isUserLoggedIn($session)
    {
        return (isset($session->sUserId) && !empty($session->sUserId));
    }

    /**
     * get complete user-data as array to use in view
     *
     * @return array
     */
    public function getUserData()
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
                Shopware()->Session()->sOutputNet = (empty($system->sUSERGROUPDATA['tax']));
            }
        }

        return $userData;
    }

    /**
     * Return the full amount to pay.
     *
     * @return float
     */
    public function getBasketAmount($userData)
    {
        $basket = $this->moptPayone__main->sGetBasket();

        if (empty($userData['additional']['charge_vat'])) {
            return $basket['AmountNetNumeric'];
        }

        return empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
    }

    protected function saveSeperateShippingAddress($personalData, $session)
    {
        $userId = $session->offsetGet('sUserId');

        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));
        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->select('*')
            ->from('s_user_addresses')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $userId);

        $addresses = $queryBuilder->execute()->fetchAll(PDO::FETCH_CLASS, \Shopware\Models\Customer\Address::class);

        $country = Shopware()->Models()->getRepository('\Shopware\Models\Country\Country')->findOneBy(array('id' => $personalData['shipping']['country']));
        $countryState = Shopware()->Models()->getRepository('\Shopware\Models\Country\State')->findOneBy(array('id' => $personalData['shipping']['state']));
        $personalData['shipping']['country'] = $country;
        $personalData['shipping']['state'] = $countryState;
        $address = new \Shopware\Models\Customer\Address();
        $address->fromArray($personalData['shipping']);
        Shopware()->Container()->get('shopware_account.address_service')->create($address, $customer);
        // get newly created address and set as defaultshippingaddress
        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->select('*')
            ->from('s_user_addresses')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $userId);

        $newAddresses = $queryBuilder->execute()->fetchAll(PDO::FETCH_CLASS, \Shopware\Models\Customer\Address::class);
        foreach ($newAddresses as $checkAddress) {
            if (!in_array($checkAddress, $addresses)) {
                $newAddress = Shopware()->Models()->getRepository('Shopware\Models\Customer\Address')->findOneBy(array('id' => $checkAddress->getId()));
                $customer->setDefaultShippingAddress($newAddress);
                Shopware()->Container()->get('shopware_account.customer_service')->update($customer);
            }
        }
    }

    /**
     * checks if billing country is allowed in
     * "Configuration->Basic Settings->Shop Settings->Countries"
     * @param string $countryId
     * @return bool
     */
    public function isBillingCountryAllowed($countryId)
    {
        if (empty($countryId)) {
            return false;
        }
        $country = $this->moptPayone__main->getPaymentHelper()->getCountryFromId($countryId);
        return $country->getActive();
    }

    /**
     * checks if shipping country is allowed in
     * "Configuration->Basic Settings->Shop Settings->Countries"
     * @param string $countryId
     * @return bool
     */
    public function isShippingCountryAllowed($countryId)
    {
        if (empty($countryId)) {
            return false;
        }
        $country = $this->moptPayone__main->getPaymentHelper()->getCountryFromId($countryId);
        return $country->getAllowShipping();
    }

    /**
     * checks if shipping country is assigned to payment
     *
     * @param string $countryId
     * @param int $paymentId
     * @return bool
     */
    public function isShippingCountryAssignedToPayment($countryId, $paymentId)
    {
        $countries = $this->moptPayone__main->getPaymentHelper()
            ->moptGetCountriesAssignedToPayment($paymentId);

        if (count($countries) == 0) {
            return true;
        }

        if (in_array($countryId, array_column($countries, 'countryID'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $paymentId
     * @param int $subshopID
     * @return bool
     */
    public function isPaymentAssignedToSubshop($paymentId, $subshopID)
    {
        return $this->moptPayone__main->getPaymentHelper()->isPaymentAssignedToSubshop($paymentId, $subshopID);
    }


    /**
     * @param array $register
     * @param int $paymentId
     * @param $session
     * @return bool
     */
    public function checkAllowedCountries(array $register, $paymentId, $session)
    {
        // Check if Billing Country is enabled in backend country config
        if (!$this->isBillingCountryAllowed($register['billing']['country'])) {
            $session->moptPayoneUserHelperError = true;
            $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('moptPayoneBillingCountryNotSupported');
        }

        // Check if shipping country is allowed in backend country config
        if (!$this->isShippingCountryAllowed($register['shipping']['country'])) {
            $session->moptPayoneUserHelperError = true;
            $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('moptPayoneShippingCountryNotSupported');
        }
        // Check if Shipping Country is assigned to payment
        if (!$this->isShippingCountryAssignedToPayment($register['shipping']['country'], $paymentId)) {
            $session->moptPayoneUserHelperError = true;
            $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('moptPayoneShippingCountryNotAssigedToPayment');
        }

        // Check if the subshop is assigned to payment
        if (!$this->isPaymentAssignedToSubshop($paymentId, Shopware()->Container()->get('shop')->getId())) {
            $session->moptPayoneUserHelperError = true;
            $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('moptPayonePaymentNotAssigedToSubshop');
        }
        // check for packstation addresses for Paypal Express
        if (! $this->checkPackstationAllowed($paymentId, $register['shipping']['street'])) {
            $session->moptPayoneUserHelperError = true;
            $session->moptPayoneUserHelperErrorMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('packStationError');
        }
        return ! $session->moptPayoneUserHelperError;
    }

    private function checkPackstationAllowed($paymentId, $street)
    {
        $paymentHelper = Mopt_PayoneMain::getInstance()->getPaymentHelper();
        $paymentName = $paymentHelper->getPaymentNameFromId($paymentId);
        $isPaypalexpress = $paymentHelper->isPayonePaypalExpress($paymentName);
        $isAmazonPay = $paymentHelper->isPayoneAmazonPay($paymentName);
        $isPaydirektexpress = $paymentHelper->isPayonePaydirektExpress($paymentName);
        $paypalexpressConfig = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayonePayPalConfig(Shopware()->Shop()->getId());
        $amazonPayConfig = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayoneAmazonPayConfig(Shopware()->Shop()->getId());
        $paydirektexpressConfig = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayDirektExpressConfig(Shopware()->Shop()->getId());
        if ($isPaypalexpress && $paypalexpressConfig->getPackStationMode() === 'deny' ) {
            if (strpos(strtolower($street), 'packstation') !== false) {
                return false;
            }
        }
        if ($isAmazonPay && $amazonPayConfig->getPackStationMode() === 'deny' ) {
            if (strpos(strtolower($street), 'packstation') !== false) {
                return false;
            }
        }
        if ($isPaydirektexpress && $paydirektexpressConfig->getPackStationMode() === 'deny' ) {
            if (strpos(strtolower($street), 'packstation') !== false) {
                return false;
            }
        }
        return true;
    }
}
