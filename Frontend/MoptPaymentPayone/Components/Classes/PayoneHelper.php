<?php

/**
 * $Id: $
 */
class Mopt_PayoneHelper
{

    /**
     * check if Responsive Template is installed and activated for current subshop
     * snippet provided by Conexco
     *
     * @return bool
     */
    public function isResponsive()
    {
        //Is Responsive Template installed and activated?
        $sql = "SELECT 1 FROM s_core_plugins WHERE name='SwfResponsiveTemplate' AND active=1";

        $result = Shopware()->Db()->fetchOne($sql);
        if ($result != 1) {
            // Plugin is not installed
            return false;
        }

        //activated for current subshop?
        $shop = Shopware()->Shop()->getId();
        $sql = "SELECT 1 FROM s_core_config_elements scce, s_core_config_values sccv WHERE "
            . "scce.name='SwfResponsiveTemplateActive' AND scce.id=sccv.element_id AND sccv.shop_id='"
            . (int)$shop . "' AND sccv.value='b:0;'";

        $result = Shopware()->Db()->fetchOne($sql);
        if ($result == 1) {
            //deactivated
            return false;
        }
        //not deactivated => activated
        return true;
    }

    /**
     * returns Payone API value for selected addresschecktype
     *
     * @param array $config
     * @param string $addressType
     * @param string $selectedCountry
     * @return bool|string
     */
    public function getAddressChecktype($config, $addressType, $selectedCountry)
    {
        if (strtolower($addressType) === 'billing') {
            $type = 'Billing';
        } elseif (strtolower($addressType) === 'shipping') {
            $type = 'Shipping';
        } else {
            throw new \InvalidArgumentException('Invalid address type given');
        }

        if (!empty($config["adresscheck{$type}Countries"])) {
            $countries = explode(',', $config["adresscheck{$type}Countries"]);
            if (!in_array($selectedCountry, $countries)) {
                return false;
            }
        }

        if ($config['consumerscoreCheckModeB2C'] === Payone_Api_Enum_ConsumerscoreType::BONIVERSUM_VERITA && $config["adresscheck{$type}Adress"] !== 0) {
            return Payone_Api_Enum_AddressCheckType::BONIVERSUM_PERSON;
        }

        switch ($config["adresscheck{$type}Adress"]) {
            case 1:
                return Payone_Api_Enum_AddressCheckType::BASIC;
            case 2:
                return Payone_Api_Enum_AddressCheckType::PERSON;
            case 3:
                return Payone_Api_Enum_AddressCheckType::BONIVERSUM_BASIC;
            case 4:
                return Payone_Api_Enum_AddressCheckType::BONIVERSUM_PERSON;
            default:
                return false;
        }
    }

    /**
     * checks if addresscheck needs to be checked according to configuration
     * returns addresschecktype
     *
     * @param array $config
     * @param string $selectedCountry
     * @return boolean
     */
    public function isBillingAddressToBeChecked($config, $selectedCountry)
    {
        if (!$config['adresscheckActive']) {
            return false;
        }

        $session = Shopware()->Session();
        if ($session->moptAddressCheckNeedsUserVerification) {
            return false;
        }

        $billingAddressChecktype = $this->getAddressChecktype($config, 'billing', $selectedCountry);
        return ($billingAddressChecktype !== false);
    }

    /**
     * checks if addresscheck needs to be checked according to configuration
     * returns addresschecktype
     *
     * @param array $config
     * @param string $basketValue
     * @param string $selectedCountry
     * @return boolean
     */
    public function isBillingAddressToBeCheckedWithBasketValue($config, $basketValue, $selectedCountry)
    {
        if (!$config['adresscheckActive']) {
            return false;
        }

        // no check when basket value outside configured values
        if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket']) {
            return false;
        }

        $billingAddressChecktype = $this->getAddressChecktype($config, 'billing', $selectedCountry);

        return ($billingAddressChecktype !== false);
    }

    /**
     * checks if addresscheck needs to be checked according to configuration
     * returns addresschecktype
     *
     * @param array $config
     * @param string $basketValue
     * @param string $selectedCountry
     * @return boolean
     */
    public function isShippingAddressToBeCheckedWithBasketValue($config, $basketValue, $selectedCountry, $userId)
    {
        if (!$config['adresscheckActive']) {
            return false;
        }

        // no check when basket value outside configured values
        if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket']) {
            return false;
        }

        $shippingAddressChecktype = $this->getAddressChecktype($config, 'shipping', $selectedCountry);

        return ($shippingAddressChecktype !== false);
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
     * checks if addresscheck needs to be checked according to configuration
     * returns addresschecktype
     *
     * @param array $config
     * @param string $basketValue
     * @return boolean
     */
    public function isConsumerScoreToBeCheckedWithBasketValue($config, $basketValue)
    {
        if (!$config['consumerscoreActive']) {
            return false;
        }

        // no check when basket value outside configured values
        if ($basketValue < $config['consumerscoreMinBasket'] || $basketValue > $config['consumerscoreMaxBasket']) {
            return false;
        }

        return is_string($config['consumerscoreCheckModeB2C']);
    }

    /**
     * returns Payone API value for sandbox/live mode
     *
     * @param string $id
     * @return string
     */
    public function getApiModeFromId($id)
    {
        if ($id == 1) {
            return Payone_Enum_Mode::LIVE;
        } else {
            return Payone_Enum_Mode::TEST;
        }
    }

    /**
     * get user scoring value
     *
     * @param string $personStatus
     * @param array $config
     * @return string
     */
    public function getUserScoringValue($personStatus, $config)
    {
        switch ($personStatus) {
            case Payone_Api_Enum_AddressCheckPersonstatus::NONE:
                return $config['mapPersonCheck'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PPB:
                return $config['mapKnowPreLastname'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PHB:
                return $config['mapKnowLastname'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PAB:
                return $config['mapNotKnowPreLastname'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PKI:
                return $config['mapMultiNameToAdress'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PNZ:
                return $config['mapUndeliverable'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PPV:
                return $config['mapPersonDead'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PPF:
                return $config['mapWrongAdress'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PNP:
                return $config['mapAddressCheckNotPossible'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PUG:
                return $config['mapAddressOkayBuildingUnknown'];

            case Payone_Api_Enum_AddressCheckPersonstatus::PUZ:
                return $config['mapPersonMovedAddressUnknown'];

            case Payone_Api_Enum_AddressCheckPersonstatus::UKN:
                return $config['mapUnknownReturnValue'];

            default:
                return '';
        }
    }

    /**
     * get user scoring color
     *
     * @param int $value
     * @return string
     */
    public function getUserScoringColorFromValue($value)
    {
        switch ($value) {
            case 0:
                return 'R';
                break;
            case 1:
                return 'Y';
                break;
            case 2:
                return 'G';
                break;

            default:
                break;
        }
    }

    /**
     * get user scoring value
     *
     * @param string $color
     * @return int
     */
    public function getUserScoringValueFromColor($color)
    {
        switch ($color) {
            case 'R':
                return 3;
                break;
            case 'Y':
                return 2;
                break;
            case 'G':
                return 1;
                break;
        }

        return $color;
    }

    /**
     * check if check is still valid
     *
     * @param int $adresscheckLifetime
     * @param string $moptPayoneAddresscheckResult
     * @param DateTime $moptPayoneAddresscheckDate
     * @return boolean
     */
    public function isBillingAddressCheckValid(
        $adresscheckLifetime,
        $moptPayoneAddresscheckResult,
        $moptPayoneAddresscheckDate
    )
    {
        // avoid multiple checks; if allowed age is zero days, set to one day
        $adresscheckLifetime = ($adresscheckLifetime > 0) ? $adresscheckLifetime : 1;
        $maxAgeTimestamp = strtotime('-' . $adresscheckLifetime . ' days');
        if (!$moptPayoneAddresscheckDate) {
            return false;
        }
        if ($moptPayoneAddresscheckDate->getTimestamp() < $maxAgeTimestamp) {
            return false;
        } else {
            return true;
        }
        if ($moptPayoneAddresscheckResult === \Payone_Api_Enum_ResponseType::INVALID) {
            return false;
        }
        return true;
    }

    /**
     * check if check is still valid
     *
     * @param string $adresscheckLifetime
     * @param string $moptPayoneAddresscheckResult
     * @param DateTime $moptPayoneAddresscheckDate
     * @return boolean
     */
    public function isShippingAddressCheckValid(
        $adresscheckLifetime,
        $moptPayoneAddresscheckResult,
        $moptPayoneAddresscheckDate
    )
    {
        // avoid multiple checks; if allowed age is zero days, set to one day
        $adresscheckLifetime = ($adresscheckLifetime > 0) ? $adresscheckLifetime : 1;
        $maxAgeTimestamp = strtotime('-' . $adresscheckLifetime . ' days');
        if (!$moptPayoneAddresscheckDate) {
            return false;
        }
        if ($moptPayoneAddresscheckDate->getTimestamp() < $maxAgeTimestamp) {
            return false;
        } else {
            return true;
        }

        if ($moptPayoneAddresscheckResult === \Payone_Api_Enum_ResponseType::INVALID) {
            return false;
        }
        return true;
    }

    /**
     * check if check is still valid
     *
     * @param string $consumerScoreCheckLifetime
     * @param date $moptPayoneConsumerScoreCheckDate
     * @return boolean
     */
    public function isConsumerScoreCheckValid($consumerScoreCheckLifetime, $moptPayoneConsumerScoreCheckDate)
    {
        $minDelayInSeconds = 5; // avoid multiple checks if max age is zero days
        $maxAgeTimestamp = strtotime('-' . $consumerScoreCheckLifetime . ' days');
        if (!$moptPayoneConsumerScoreCheckDate) {
            return false;
        }
        if ($moptPayoneConsumerScoreCheckDate->getTimestamp() < ($maxAgeTimestamp - $minDelayInSeconds)) {
            return false;
        }

        return true;
    }

    /**
     * save check result
     *
     * @param string $addressType 'billing' or 'shipping'
     * @param string $userId
     * @param object $response
     * @param string $mappedPersonStatus
     * @return mixed
     */
    public function saveAddressCheckResult($addressType, $userId, $response, $mappedPersonStatus)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);

        if ($addressType === 'billing') {
            $billing = $user->getDefaultBillingAddress();
            $attribute = $this->getOrCreateBillingAttribute($billing);
        } elseif ($addressType === 'shipping') {
            $shipping = $user->getDefaultShippingAddress();
            $attribute = $this->getOrCreateShippingAttribute($shipping);
        }

        $attribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
        $attribute->setMoptPayoneAddresscheckPersonstatus($response->getPersonstatus());
        $attribute->setMoptPayoneAddresscheckResult($response->getStatus());
        $attribute->setMoptPayoneConsumerscoreColor($mappedPersonStatus);

        Shopware()->Models()->persist($attribute);
        Shopware()->Models()->flush();
    }

    /**
     * save check error
     *
     * @param string $userId
     * @param object $response
     * @return mixed
     */
    public function saveBillingAddressError($userId, $response)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);

        $billing = $user->getDefaultBillingAddress();

        $billingAttribute = $this->getOrCreateBillingAttribute($billing);
        $billingAttribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
        $billingAttribute->setMoptPayoneAddresscheckPersonstatus('NONE');
        $billingAttribute->setMoptPayoneAddresscheckResult($response->getStatus());

        Shopware()->Models()->persist($billingAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save check error
     *
     * @param string $userId
     * @param object $response
     * @return mixed
     */
    public function saveShippingAddressError($userId, $response)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);

        $shipping = $user->getDefaultShippingAddress();

        $shippingAttribute = $this->getOrCreateShippingAttribute($shipping);
        $shippingAttribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
        $shippingAttribute->setMoptPayoneAddresscheckResult($response->getStatus());

        Shopware()->Models()->persist($shippingAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save check resuklt
     *
     * @param string $userId
     * @param object $response
     * @return mixed
     */
    public function saveConsumerScoreCheckResult($userId, $response)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $userAttribute = $this->getOrCreateUserAttribute($user);

        $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
        $userAttribute->setMoptPayoneConsumerscoreResult($response->getStatus());
        $userAttribute->setMoptPayoneConsumerscoreColor($response->getScore());
        $userAttribute->setMoptPayoneConsumerscoreValue($response->getScorevalue());

        Shopware()->Models()->persist($userAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save ratepay ban date
     *
     * @param string $userId
     * @return mixed
     */
    public function saveRatepayBanDate($userId)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $userAttribute = $this->getOrCreateUserAttribute($user);

        $userAttribute->setMoptPayoneRatepayBan(date('Y-m-d'));
        Shopware()->Models()->persist($userAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save check error
     *
     * @param string $userId
     * @param object $response
     * @return mixed
     */
    public function saveConsumerScoreError($userId, $response)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $userAttribute = $this->getOrCreateUserAttribute($user);

        $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
        $userAttribute->setMoptPayoneConsumerscoreResult($response->getStatus());
        // also set Score to "R" in case some previous check was "G"
        $userAttribute->setMoptPayoneConsumerscoreColor('R');

        Shopware()->Models()->persist($userAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save check denied
     *
     * @param string $userId
     * @return mixed
     */
    public function saveConsumerScoreDenied($userId)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $userAttribute = $this->getOrCreateUserAttribute($user);

        $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
        $userAttribute->setMoptPayoneConsumerscoreResult('DENIED');
        $userAttribute->setMoptPayoneConsumerscoreColor('R');
        $userAttribute->setMoptPayoneConsumerscoreValue('100');

        Shopware()->Models()->persist($userAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save check denied
     *
     * @param string $userId
     * @return mixed
     */
    public function saveConsumerScoreApproved($userId)
    {
        if (!$userId) {
            return;
        }

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $userAttribute = $this->getOrCreateUserAttribute($user);

        $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
        $userAttribute->setMoptPayoneConsumerscoreResult('APPROVED');
        $userAttribute->setMoptPayoneConsumerscoreColor('G');
        $userAttribute->setMoptPayoneConsumerscoreValue('550');

        Shopware()->Models()->persist($userAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * save corrected billing address
     *
     * @param string $userId
     * @param object $response
     */
    public function saveCorrectedBillingAddress($userId, $response)
    {
        $orderVariables = Shopware()->Session()->sOrderVariables;

        // ordervariables are null until shippingPayment view
        if ($orderVariables !== null) {
            $aOrderVars = $orderVariables->getArrayCopy();
            $addressId = $aOrderVars['sUserData']['billingaddress']['id'];
        } else {
            /** @var \Shopware\Components\Model\ModelManager $em */
            $em = Shopware()->Models();

            /** @var \Shopware\Models\Customer\Customer $customer */
            $customer = $em->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));

            /** @var \Shopware\Models\Customer\Address $address */
            $addressId = $customer->getDefaultBillingAddress()->getId();

        }

        // Update Model and Flush for SW 5.3; when using sql like below this does not happen
        $address = Shopware()->Models()->getRepository('Shopware\Models\Customer\Address')->find($addressId);
        $address->setStreet($response->getStreet());
        $address->setZipcode($response->getZip());
        $address->setCity($response->getCity());
        Shopware()->Models()->persist($address);
        Shopware()->Models()->flush($address);
        $session = Shopware()->Session();
        $session->offsetSet('moptAddressCorrected', true);
    }

    /**
     * save corrected shipping address
     *
     * @param string $userId
     * @param object $response
     */
    public function saveCorrectedShippingAddress($userId, $response)
    {
        $orderVariables = Shopware()->Session()->sOrderVariables;

        if ($orderVariables !== null) {
            $aOrderVars = $orderVariables->getArrayCopy();
            $addressId = $aOrderVars['sUserData']['shippingaddress']['id'];
        } else {
            /** @var \Shopware\Components\Model\ModelManager $em */
            $em = Shopware()->Models();

            /** @var \Shopware\Models\Customer\Customer $customer */
            $customer = $em->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array('id' => $userId));

            /** @var \Shopware\Models\Customer\Address $address */
            $addressId = $customer->getDefaultShippingAddress()->getId();

        }
        // Update Model and Flush for SW 5.3; when using sql like below this does not happen
        $address = Shopware()->Models()->getRepository('Shopware\Models\Customer\Address')->find($addressId);
        $address->setStreet($response->getStreet());
        $address->setZipcode($response->getZip());
        $address->setCity($response->getCity());
        Shopware()->Models()->persist($address);
        Shopware()->Models()->flush($address);
        $session = Shopware()->Session();
        $session->offsetSet('moptAddressCorrected', true);
    }

    /**
     * reset address check data
     *
     * @param string $userId
     */
    public function resetAddressCheckData($userId)
    {

        // Todo SW 5.3
        $sql = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
        $billingId = Shopware()->Db()->fetchOne($sql, $userId);

        $sql = 'UPDATE `s_user_billingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_result=? WHERE billingID = ?';
        Shopware()->Db()->query($sql, array('NULL', 'NULL', $billingId));
    }

    /**
     * get address attributes
     *
     * @param string $userId
     * @return array
     */
    public function getShippingAddressAttributesFromUserId($userId)
    {
        if (!$userId) {
            return;
        }

        //get shippingaddress attribute
        $shippingAttributes = array();
        $shippingAttributes['moptPayoneAddresscheckResult'] = null;
        $shippingAttributes['moptPayoneAddresscheckDate'] = null;

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $shipping = $user->getDefaultShippingAddress();
        $shippingAttribute = $this->getOrCreateShippingAttribute($shipping);
        $shippingAttributes['moptPayoneAddresscheckResult'] = $shippingAttribute->getMoptPayoneAddresscheckResult();
        $shippingAttributes['moptPayoneAddresscheckDate'] = $shippingAttribute->getMoptPayoneAddresscheckDate();
        if (is_string($shippingAttributes['moptPayoneAddresscheckDate'])) {
            $shippingAttributes['moptPayoneAddresscheckDate'] = DateTime::createFromFormat(
                'Y-m-d',
                $shippingAttribute->getMoptPayoneAddresscheckDate()
            );
        }

        return $shippingAttributes;
    }

    /**
     * get bank account check type
     *
     * @param array $config
     * @return int
     */
    public function getBankAccountCheckType($config)
    {
        switch ($config['checkAccount']) {
            case null:
                $checkType = false;
                break;
            case 0:
                $checkType = false;
                break;
            case 1:
                $checkType = 0;
                break;
            case 2:
                $checkType = 1;
                break;
            default:
                $checkType = false;
                break;
        }

        return $checkType;
    }

    /**
     * get score color from config
     *
     * @param array $config
     * @return string
     */
    public function getScoreColor($config)
    {
        switch ($config['consumerscoreBoniversumUnknown']) {
            case 0:
                $color = 'R';
                break;
            case 1:
                $color = 'Y';
                break;
            case 2:
                $color = 'G';
                break;
            default:
                $color = null;
                break;
        }

        return $color;
    }

    /**
     * get consumer score
     *
     * @param array $userArray
     * @param array $config
     * @return int
     */
    public function getScoreFromUserAccordingToPaymentConfig($userArray, $config)
    {
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer');
        $userID = $userArray['additional']['user']['id'];

        $userObject = !empty($userID) ?
            $repository->find($userID) : null;

        $billingAddress = !empty($userObject) ?
            $userObject->getDefaultBillingAddress() : null;
        $billingAttribute = !empty($billingAddress) ?
            $this->getOrCreateBillingAttribute($billingAddress) : null;
        $moptBillingColor = !empty($billingAttribute) ?
            $billingAttribute->getMoptPayoneConsumerscoreColor() : null;

        $shippingAddress = !empty($userObject) ?
            $userObject->getDefaultShippingAddress() : null;
        $shippingAttribute = !empty($shippingAddress) ?
            $this->getOrCreateShippingAttribute($shippingAddress) : null;
        $moptShipmentColor = !empty($shippingAttribute) ?
            $shippingAttribute->getMoptPayoneConsumerscoreColor() : null;
        $moptScoreColor = $userArray['additional']['user']['mopt_payone_consumerscore_color'];

        $billingColor = $this->getSpecificScoreFromUser(
            $moptBillingColor,
            $config['adresscheckActive'] && $config['adresscheckBillingAdress'] != 0
        );
        $shipmentColor = $this->getSpecificScoreFromUser(
            $moptShipmentColor,
            $config['adresscheckActive'] && $config['adresscheckShippingAdress'] != 0
        );
        $consumerScoreColor = $this->getSpecificScoreFromUser(
            $moptScoreColor,
            $config['consumerscoreActive']
        );

        $biggestScore = max($billingColor, $shipmentColor, $consumerScoreColor);

        if ($biggestScore == -1) {
            $defaultScore = (int)$config['consumerscoreDefault']; //0=R, 1=Y, 2=G
            if ((int)$config['consumerscoreDefault'] === 0) {
                $defaultScore = 2;
            }
            if ((int)$config['consumerscoreDefault'] === 2) {
                $defaultScore = 0;
            }
            $biggestScore = $defaultScore + 1; //as default return new customer default value
        }

        if (in_array($biggestScore, array(1, 2, 3))) {
            return $biggestScore;
        } else {
            return -3; //no checks are active for this payment method
        }

    }

    /**
     *
     * @param string $value the color value, can be NULL if not computed (e.g. user not logged in)
     * @param bool $condition if the score should be used
     * @return int
     */
    protected function getSpecificScoreFromUser($value, $condition)
    {
        if ($condition) {
            //get addresscheckscore
            if ($value) {
                return $this->getUserScoringValueFromColor($value);
            } else {
                return -1;
            }
        } else {
            return -2;
        }
    }

    /**
     * get or create attribute data for given object
     *
     * @param object $object
     * @return \Shopware\Models\Attribute\OrderDetail
     * @throws Exception
     */
    public function getOrCreateAttribute($object)
    {
        if (!empty($object) && $attribute = $object->getAttribute()) {
            return $attribute;
        }

        if ($object instanceof Shopware\Models\Order\Order) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Order')
                ->findOneBy(array('orderId' => $object->getId()))) {
                $attribute = new Shopware\Models\Attribute\Order();
            }
        } elseif ($object instanceof Shopware\Models\Order\Detail) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\OrderDetail')
                ->findOneBy(array('orderDetailId' => $object->getId()))) {
                $attribute = new Shopware\Models\Attribute\OrderDetail();
            }
        } else {
            throw new Exception('Unknown attribute base class');
        }

        $object->setAttribute($attribute);
        return $attribute;
    }

    /**
     * get or create attribute data for given object
     *
     * @param \Shopware\Models\Customer\Billing $object
     * @return \Shopware\Models\Attribute\CustomerBilling
     * @throws Exception
     */
    public function getOrCreateBillingAttribute($object)
    {
        if (!empty($object) && $attribute = $object->getAttribute()) {
            return $attribute;
        }

        if ($object instanceof Shopware\Models\Customer\Billing) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\CustomerBilling')
                ->findOneBy(['customerBillingId' => $object->getId()])) {
                $attribute = new Shopware\Models\Attribute\CustomerBilling();
            }
        } elseif ($object instanceof Shopware\Models\Customer\Address) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\CustomerAddress')
                ->findOneBy(['customerAddressId' => $object->getId()])) {
                $attribute = new Shopware\Models\Attribute\CustomerAddress();
            }
        } else {
            throw new Exception('Unknown attribute base class');
        }

        $object->setAttribute($attribute);
        return $attribute;
    }

    /**
     * get or create attribute data for given object
     *
     * @param \Shopware\Models\Customer\Shipping $object
     * @return \Shopware\Models\Attribute\CustomerShipping
     * @throws Exception
     */
    public function getOrCreateShippingAttribute($object)
    {
        if (!empty($object) && $attribute = $object->getAttribute()) {
            return $attribute;
        }

        if ($object instanceof Shopware\Models\Customer\Shipping) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\CustomerShipping')
                ->findOneBy(['customerShippingId' => $object->getId()])) {
                $attribute = new Shopware\Models\Attribute\CustomerShipping();
            }
        } elseif ($object instanceof Shopware\Models\Customer\Address) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\CustomerAddress')
                ->findOneBy(['customerAddressId' => $object->getId()])) {
                $attribute = new Shopware\Models\Attribute\CustomerAddress();
            }
        } else {
            throw new Exception('Unknown attribute base class');
        }

        $object->setAttribute($attribute);
        return $attribute;
    }

    /**
     * get or create attribute data for given object
     *
     * @param Customer $object
     * @return \Shopware\Models\Attribute\Customer
     * @throws Exception
     */
    public function getOrCreateUserAttribute($object)
    {
        if (!empty($object) && $attribute = $object->getAttribute()) {
            return $attribute;
        }

        if ($object instanceof Shopware\Models\Customer\Customer) {
            if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Customer')
                ->findOneBy(array('customerId' => $object->getId()))) {
                $attribute = new Shopware\Models\Attribute\Customer();
            }
        } else {
            throw new Exception('Unknown attribute base class');
        }

        $object->setAttribute($attribute);
        return $attribute;
    }

    /**
     * map transaction status
     *
     * @param object $order
     * @param array $payoneConfig
     * @param string $payoneStatus
     * @param bool $useOrm
     */
    public function mapTransactionStatus($order, $payoneConfig, $payoneStatus = null, $useOrm = true)
    {
        if ($payoneStatus === null) {
            $attributeData = $this->getOrCreateAttribute($order);
            $payoneStatus = $attributeData->getMoptPayoneStatus();
        }

        //map payone status to shopware payment-status
        $configKey = 'state' . ucfirst($payoneStatus);
        if (isset($payoneConfig[$configKey])) {
            if ($shopwareState = Shopware()->Models()->getRepository('Shopware\Models\Order\Status')
                ->find($payoneConfig[$configKey])) {
                if ($useOrm) {
                    $order->setPaymentStatus($shopwareState);
                    Shopware()->Models()->persist($order);
                    Shopware()->Models()->flush();
                } else {
                    $db = Shopware()->Db();
                    $sql = "UPDATE s_order
                  SET cleared = " . $db->quote($shopwareState->getId()) . "
                  WHERE id = " . $db->quote($order->getId());
                    $db->exec($sql);
                }
            }
        }
    }

    /**
     * map transaction status
     *
     * @param object $order
     * @param array $payoneConfig
     * @param string $payoneStatus
     * @param bool $useOrm
     */
    public function getMappedShopwarePaymentStatusId($payoneConfig, $payoneStatus = null)
    {
        //if nothing is submitted, set payment state to "open"
        if ($payoneStatus === null) {
            return 17;
        }

        //map payone status to shopware payment-status
        $configKey = 'state' . ucfirst($payoneStatus);
        if (isset($payoneConfig[$configKey])) {
            if ($shopwareState = Shopware()->Models()->getRepository('Shopware\Models\Order\Status')
                ->find($payoneConfig[$configKey])) {
                return $shopwareState->getId();
            }
        }

        //if nothing is set or found, return state "open"
        return 17;
    }

    /**
     * extract shipping cost and insert as order position
     *
     * @deprecated since version 2.1.3
     * no more extraction of shipping cost, to avoid collisions with other plugins, software, ...
     * @param object $order
     * @param array $basketData
     * @return mixed
     */
    public function extractShippingCostAsOrderPosition($order, $basketData)
    {
        //leave if no shipment costs are set
        if ($order->getInvoiceShipping() == 0) {
            return;
        }

        $dispatch = $order->getDispatch();
        if (strpos($order->getPayment()->getName(), 'mopt_payone__') !== 0) {
            return false;
        }
        //insert shipping as new order detail
        $db = Shopware()->Db();
        $sql = "INSERT INTO `s_order_details` (`id`, "
            . " `orderID`, "
            . "`ordernumber`, "
            . "`articleID`, "
            . "`articleordernumber`, "
            . "`price`, "
            . "`quantity`, "
            . "`name`, "
            . "`status`, "
            . "`shipped`, "
            . "`shippedgroup`, "
            . "`releasedate`, "
            . "`modus`, "
            . "`esdarticle`, "
            . "`taxID`, "
            . "`tax_rate`, "
            . "`config`) "
            . " VALUES ("
            . "NULL, "
            . $db->quote($order->getId()) . ", "
            . $db->quote($order->getNumber()) . ", "
            . "'0', "
            . "'SHIPPING', "
            . $db->quote($order->getInvoiceShipping()) . ", "
            . "'1', "
            . $db->quote($dispatch->getName()) . ", "
            . "'0', "
            . "'0', "
            . "'0',"
            . " NULL, "
            . "'4', "
            . "'0', "
            . "'0', "
            . $db->quote($basketData['sShippingcostsTax']) . ", "
            . " '');";
        $db->exec($sql);


        // Set shipping details to zero since these informations are stored within the basket
        // of the corresponding order.
        $sql = "UPDATE s_order
                  SET invoice_shipping = 0,
                    invoice_shipping_net = 0
                  WHERE id = " . $db->quote($order->getId());
        $db->exec($sql);
    }

    /**
     * get consumerscore check data
     *
     * @param string $userId
     * @return array
     */
    public function getConsumerScoreDataFromUserId($userId)
    {
        $userConsumerScoreData = array();
        $userConsumerScoreData['moptPayoneConsumerscoreResult'] = null;
        $userConsumerScoreData['moptPayoneConsumerscoreDate'] = null;

        $sql = 'SELECT `mopt_payone_consumerscore_result`, '
            . '`mopt_payone_consumerscore_date` FROM `s_user_attributes` WHERE userID = ?';
        $result = Shopware()->Db()->fetchAll($sql, $userId);

        if ($result) {
            $userConsumerScoreData['moptPayoneConsumerscoreResult'] = $result[0]['mopt_payone_consumerscore_result'];
            $userConsumerScoreData['moptPayoneConsumerscoreDate'] = DateTime::createFromFormat(
                'Y-m-d',
                $result[0]['mopt_payone_consumerscore_date']
            );
        }

        return $userConsumerScoreData;
    }

    /**
     * get ratepay ban date
     *
     * @param string $userId
     * @return array
     */
    public function getRatepayBanDateFromUserId($userId)
    {
        $ratepayBanDate = null;
        $sql = 'SELECT `mopt_payone_ratepay_ban` '
            . 'FROM `s_user_attributes` WHERE userID = ?';
        $result = Shopware()->Db()->fetchAll($sql, $userId);

        if ($result) {
            $ratepayBanDate = DateTime::createFromFormat(
                'Y-m-d',
                $result[0]['mopt_payone_ratepay_ban']
            );
        }

        return $ratepayBanDate;
    }


    /**
     * get address check data
     *
     * @param string $userId
     * @return array
     */
    public function getBillingAddresscheckDataFromUserId($userId)
    {
        if (!$userId) {
            return;
        }

        $userBillingAddressCheckData = array();
        $userBillingAddressCheckData['moptPayoneAddresscheckResult'] = null;
        $userBillingAddressCheckData['moptPayoneAddresscheckDate'] = null;

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $billing = $user->getDefaultBillingAddress();
        $billingAttribute = $this->getOrCreateBillingAttribute($billing);
        $userBillingAddressCheckData['moptPayoneAddresscheckResult'] = $billingAttribute->getMoptPayoneAddresscheckResult();
        $userBillingAddressCheckData['moptPayoneAddresscheckDate'] = $billingAttribute->getMoptPayoneAddresscheckDate();
        // Make sure this is a DateTime Object, sometimes?? string gets returned ???
        if (is_string($userBillingAddressCheckData['moptPayoneAddresscheckDate'])) {
            $userBillingAddressCheckData['moptPayoneAddresscheckDate'] = DateTime::createFromFormat(
                'Y-m-d',
                $billingAttribute->getMoptPayoneAddresscheckDate()
            );
        }

        return $userBillingAddressCheckData;
    }

    /**
     * get country id from iso
     *
     * @param string $iso
     * @return string
     */
    public function getCountryIdFromIso($iso)
    {
        $sql = 'SELECT `id` FROM s_core_countries WHERE `countryiso` LIKE "' . $iso . '";';
        $countryId = Shopware()->Db()->fetchOne($sql);
        return $countryId;
    }

    /**
     * get country iso from id
     *
     * @param string $id
     * @return string
     */
    public function getCountryIsoFromId($id)
    {
        $sql = 'SELECT `countryiso` FROM s_core_countries WHERE `id`="' . (int)$id . '";';
        $countryIso = Shopware()->Db()->fetchOne($sql);
        return $countryIso;
    }

    /**
     * get state id from iso
     *
     * @param string $id
     * @return string
     */
    public function getStateFromId($countryId, $stateIso, $isPaypalEcs = false)
    {
        // Paypal ECS returns their own state codes
        // see https://developer.paypal.com/docs/classic/api/state_codes/#usa
        // so try again after mapping them to the real ISO Codes
        $stateIso = ($isPaypalEcs && !empty($this->getCountryIsoFromPaypalCountryCode($countryId, $stateIso))) ? $this->getCountryIsoFromPaypalCountryCode($countryId, $stateIso)  : $stateIso;

        $sql = 'SELECT `id` FROM s_core_countries_states WHERE `shortcode` LIKE "' . $stateIso . '" '
            . 'AND `countryID` = ' . $countryId . ';';
        $stateId = Shopware()->Db()->fetchOne($sql);

        if ($stateId) {
            return $stateId;
        } else {
            return null;
        }
    }

    /**
     * get state shortcode from countryID and StateID
     *
     * @param string $countryId
     * @param string $stateId
     * @return string $stateShortcode
     */
    public function getStateShortcodeFromId($countryId, $stateId)
    {
        $sql = 'SELECT `shortcode` FROM s_core_countries_states WHERE `id`= "' . $stateId . '" '
            . 'AND `countryID` = ' . $countryId . ';';
        $stateShortcode = Shopware()->Db()->fetchOne($sql);

        if ($stateShortcode) {
            return $stateShortcode;
        } else {
            return null;
        }
    }

    /**
     * get state id from iso
     *
     * @param string $id
     * @return string
     */
    public function getCountryIsoFromPaypalCountryCode($countryId, $stateIso)
    {
        $map = [
            // JP
            '15' => [
                'AICHI-KEN' => '23',
                'AKITA-KEN' => '05',
                'AOMORI-KEN' => '02',
                'CHIBA-KEN' => '12',
                'EHIME-KEN' => '38',
                'FUKUI-KEN' => '18',
                'FUKUOKA-KEN' => '40',
                'FUKUSHIMA-KEN' => '07',
                'GIFU-KEN' => '21',
                'GUNMA-KEN' => '10',
                'HIROSHIMA-KEN' => '34',
                'HOKKAIDO' => '01',
                'HYOGO-KEN' => '28',
                'IBARAKI-KEN' => '08',
                'ISHIKAWA-KEN' => '17',
                'IWATE-KEN' => '03',
                'KAGAWA-KEN' => '37',
                'KAGOSHIMA-KEN' => '46',
                'KANAGAWA-KEN' => '14',
                'KOCHI-KEN' => '39',
                'KUMAMOTO-KEN' => '43',
                'KYOTO-FU' => '26',
                'MIE-KEN' => '24',
                'MIYAGI-KEN' => '04',
                'MIYAZAKI-KEN' => '45',
                'NAGANO-KEN' => '20',
                'NAGASAKI-KEN' => '42',
                'NARA-KEN' => '29',
                'NIIGATA-KEN' => '15',
                'OITA-KEN' => '44',
                'OKAYAMA-KEN' => '33',
                'OKINAWA-KEN' => '47',
                'OSAKA-FU' => '27',
                'SAGA-KEN' => '41',
                'SAITAMA-KEN' => '11',
                'SHIGA-KEN' => '25',
                'SHIMANE-KEN' => '32',
                'SHIZUOKA-KEN' => '22',
                'TOCHIGI-KEN' => '09',
                'TOKUSHIMA-KEN' => '36',
                'TOKYO-TO' => '13',
                'TOTTORI-KEN' => '31',
                'TOYAMA-KEN' => '16',
                'WAKAYAMA-KEN' => '30',
                'YAMAGATA-KEN' => '06',
                'YAMAGUCHI-KEN' => '35',
                'YAMANASHI-KEN' => '19',
            ],
            // MX
            '1001' => [
                'AGS' => 'AGU',
                'BC' => 'BCN',
                'BCS' => 'BCS',
                'CAMP' => 'CAM',
                'CHIS' => 'CHP',
                'CHIH' => 'CHH',
                'CDMX' => 'CMX',
                'COAH' => 'COA',
                'COL' => 'COL',
                'DGO' => 'DGO',
                'MEX' => 'MEX',
                'GTO' => 'GUA',
                'GRO' => 'GRO',
                'HGO' => 'HID',
                'JAL' => 'JAL',
                'MICH' => 'MIC',
                'MOR' => 'MOR',
                'NAY' => 'NAY',
                'NL' => 'NLE',
                'OAX' => 'OAX',
                'PUE' => 'PUE',
                'QRO' => 'QUE',
                'Q ROO' => 'ROO',
                'SLP' => 'SLP',
                'SIN' => 'SIN',
                'SON' => 'SON',
                'TAB' => 'TAB',
                'TAMPS' => 'TAM',
                'TLAX' => 'TLA',
                'VER' => 'VER',
                'YUC' => 'YUC',
                'ZAC' => 'ZAC',
            ],
            // CN
            '1002' => [
                'CN-AH' => 'AH',
                'CN-BJ' => 'BJ',
                'CN-CQ' => 'CQ',
                'CN-FJ' => 'FJ',
                'CN-GD' => 'GD',
                'CN-GS' => 'GS',
                'CN-GX' => 'GX',
                'CN-GZ' => 'GZ',
                'CN-HA' => 'HA',
                'CN-HB' => 'HB',
                'CN-HE' => 'HE',
                'CN-HI' => 'HI',
                'CN-HK' => 'HK',
                'CN-HL' => 'HL',
                'CN-HN' => 'HN',
                'CN-JL' => 'JL',
                'CN-JS' => 'JS',
                'CN-JX' => 'JX',
                'CN-LN' => 'LN',
                'CN-MO' => 'MO',
                'CN-NM' => 'NM',
                'CN-NX' => 'NX',
                'CN-QH' => 'QH',
                'CN-SC' => 'SC',
                'CN-SD' => 'SD',
                'CN-SH' => 'SH',
                'CN-SN' => 'SN',
                'CN-SX' => 'SX',
                'CN-TJ' => 'TJ',
                'CN-TW' => 'TW',
                'CN-XJ' => 'XJ',
                'CN-XZ' => 'XZ',
                'CN-YN' => 'YN',
                'CN-ZJ' => 'ZJ',
            ],
            // AR
            '1003' => [
                'CIUDAD AUTÓNOMA DE BUENOS AIRES' => 'C',
                'BUENOS AIRES' => 'B',
                'CATAMARCA' => 'K',
                'CHACO' => 'H',
                'CHUBUT' => 'U',
                'CORRIENTES' => 'W',
                'CÓRDOBA' => 'X',
                'ENTRE RÍOS' => 'E',
                'FORMOSA' => 'P',
                'JUJUY' => 'Y',
                'LA PAMPA' => 'L',
                'LA RIOJA' => 'F',
                'MENDOZA' => 'M',
                'MISIONES' => 'N',
                'NEUQUÉN' => 'Q',
                'RÍO NEGRO' => 'R',
                'SALTA' => 'A',
                'SAN JUAN' => 'J',
                'SAN LUIS' => 'D',
                'SANTA CRUZ' => 'Z',
                'SANTA FE' => 'S',
                'SANTIAGO DEL ESTERO' => 'G',
                'TIERRA DEL FUEGO' => 'V',
                'TUCUMÁN' => 'T',
            ],
            // ID
            '1004' => [
                'ID-BA' => 'C',
                'ID-BB' => 'B',
                'ID-BT' => 'K',
                'ID-BE' => 'H',
                'ID-YO' => 'U',
                'ID-JK' => 'W',
                'ID-GO' => 'X',
                'ID-JA' => 'E',
                'ID-JB' => 'P',
                'ID-JT' => 'Y',
                'ID-JI' => 'L',
                'ID-KB' => 'F',
                'ID-KS' => 'M',
                'ID-KT' => 'N',
                'ID-KI' => 'Q',
                'ID-KU' => 'R',
                'ID-KR' => 'A',
                'ID-LA' => 'J',
                'ID-MA' => 'D',
                'ID-MU' => 'Z',
                'ID-AC' => 'S',
                'ID-NB' => 'G',
                'ID-NT' => 'V',
                'ID-PA' => 'T',
                'ID-PB' => 'M',
                'ID-RI' => 'N',
                'ID-SR' => 'Q',
                'ID-SN' => 'R',
                'ID-ST' => 'A',
                'ID-SG' => 'J',
                'ID-SA' => 'D',
                'ID-SB' => 'Z',
                'ID-SS' => 'S',
                'ID-SU' => 'G',
            ],
            // IN
            /*'1005' => [
                'Andaman and Nicobar Islands' => 'AN',
                'Andhra Pradesh' => 'AP',
                'Arunachal Pradesh' => 'AR',
                'Assam' => 'AS',
                'Bihar' => 'BR',
                'Chandigarh' => 'CT',
                'Chhattisgarh' => 'CT',
                'Dadra and Nagar Haveli' => 'DN',
                'Daman and Diu' => 'DD',
                'Delhi (NCT)' => 'DL',
                'Goa' => 'GA',
                'Gujarat' => 'GJ',
                'Haryana' => 'HR',
                'Himachal Pradesh' => 'HP',
                'Jammu and Kashmir' => 'JK',
                'Jharkhand' => 'JH',
                'Karnataka' => 'KA',
                'Kerala' => 'KL',
                'Lakshadweep' => 'LD',
                'Madhya Pradesh' => 'MP',
                'Maharashtra' => 'MH',
                'Manipur' => 'MN',
                'Meghalaya' => 'ML',
                'Mizoram' => 'MZ',
                'Nagaland' => 'NL',
                'Odisha' => 'OR',
                'Puducherry' => 'PY',
                'Punjab' => 'PB',
                'Rajasthan' => 'RJ',
                'Sikkim' => 'SK',
                'Tamil Nadu' => 'TN',
                'Telangana' => 'TG',
                'Tripura' => 'TR',
                'Uttar Pradesh' => 'UP',
                'Uttarakhand' => 'UT',
                'West Bengal' => 'WB',
            ],
            */
        ];

        return $map[$countryId][$stateIso];
    }

    /**
     * retrieve and reteurn country id for selected address
     *
     * @param array $userData
     * @param bool $isShipping
     * @return string
     */
    public function getAddressCountryFromUserData($userData, $isShipping = false)
    {
        if ($isShipping && !empty($userData['register']['shipping']['country'])) {
            return $userData['register']['shipping']['country'];
        } else {
            return $userData['register']['billing']['country'];
        }
    }

    /**
     * Return whether or not a abo-article is in the basket
     *
     * @return bool
     */
    public function isAboCommerceArticleInBasket()
    {
        if (!$this->isAboCommerceActive()) {
            return false;
        }

        $entityManager = Shopware()->Models();
        $builder = $entityManager->createQueryBuilder();
        $builder->select($entityManager->getExpressionBuilder()->count('basket.id'))
            ->from('Shopware\Models\Order\Basket', 'basket')
            ->innerJoin('basket.attribute', 'attribute')
            ->where('basket.sessionId = :sessionId')
            ->andWhere('attribute.swagAboCommerceDeliveryInterval IS NOT NULL')
            ->setParameters(array('sessionId' => Shopware()->SessionID()));

        $count = $builder->getQuery()->getSingleScalarResult();

        return (bool)$count;
    }

    /**
     * check if abo commerce plugin is activated
     *
     * @return bool
     */
    protected function isAboCommerceActive()
    {
        $sql = "SELECT 1 FROM s_core_plugins WHERE name='SwagAboCommerce' AND active=1";

        $result = Shopware()->Db()->fetchOne($sql);
        if ($result != 1) {
            return false; //not installed
        }
        return true;
    }

    public function getPayoneAmazonPayConfig()
    {
        // use latest config
        $sql = "SELECT MAX(id) FROM s_plugin_mopt_payone_amazon_pay";
        $latest = Shopware()->Db()->fetchOne($sql);

        /**
         * @var $config \Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay
         */
        $config = Shopware()->Models()->find(
            'Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay',
            $latest
        );
        return $config;
    }

    public function isCompany($userId)
    {
        $customer = Shopware()->Models()
            ->getRepository('Shopware\Models\Customer\Customer')
            ->find($userId);

        $billing = $customer->getDefaultBillingAddress();

        $return = !empty($billing->getCompany()) ? true : false;
        return $return;
    }

    /**
     * check if WUnschpaket plugin is activated
     *
     * @return bool
     */
    public function isWunschpaketActive()
    {
        $sql = "SELECT 1 FROM s_core_plugins WHERE name='DHLPaWunschpaket' AND active=1";
        $result = Shopware()->Db()->fetchOne($sql);
        return ($result == 1);
    }


    /**
     * Return the latest PayPalConfig
     * @return \Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal
     */
    public function getPayonePayPalConfig()
    {
        // use latest config
        $sql = "SELECT MAX(id) FROM s_plugin_mopt_payone_paypal";
        $latest = Shopware()->Db()->fetchOne($sql);

        /**
         * @var $config \Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal
         */
        $config = Shopware()->Models()->find(
            'Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal',
            $latest
        );
        return $config;
    }

}
