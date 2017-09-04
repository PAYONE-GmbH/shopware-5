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
        $sql  = "SELECT 1 FROM s_core_config_elements scce, s_core_config_values sccv WHERE "
            . "scce.name='SwfResponsiveTemplateActive' AND scce.id=sccv.element_id AND sccv.shop_id='"
            . (int) $shop . "' AND sccv.value='b:0;'";
    
        $result  = Shopware()->Db()->fetchOne($sql);
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
     * @param array  $config
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

        if ($config['consumerscoreCheckMode'] === Payone_Api_Enum_ConsumerscoreType::BONIVERSUM_VERITA) {
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

      //no check when basket value outside configured values
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
        // ToDo make this SW 5.3 compatible

        // check if seperate shipping address is saved
        $sql        = 'SELECT `id` FROM `s_user_shippingaddress` WHERE userID = ?';
        $shippingId = Shopware()->Db()->fetchOne($sql, $userId);
        if (!$shippingId) {
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

        return is_string($config['consumerscoreCheckMode']);
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
    ) {
        // avoid multiple checks; if allowed age is zero days, set to one day
        $adresscheckLifetime = ($adresscheckLifetime > 0) ? $adresscheckLifetime : 1;
        $maxAgeTimestamp = strtotime('-' . $adresscheckLifetime . ' days');
        if (!$moptPayoneAddresscheckDate) {
            return false;
        }
        if ($moptPayoneAddresscheckResult === \Payone_Api_Enum_ResponseType::INVALID) {
            return false;
        }
        if ($moptPayoneAddresscheckDate->getTimestamp() < $maxAgeTimestamp) {
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
    ) {
        // avoid multiple checks; if allowed age is zero days, set to one day
        $adresscheckLifetime = ($adresscheckLifetime > 0) ? $adresscheckLifetime : 1;
        $maxAgeTimestamp = strtotime('-' . $adresscheckLifetime . ' days');
        if (!$moptPayoneAddresscheckDate) {
            return false;
        }
        if ($moptPayoneAddresscheckResult === \Payone_Api_Enum_ResponseType::INVALID) {
            return false;
        }
        if ($moptPayoneAddresscheckDate->getTimestamp() < $maxAgeTimestamp) {
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

        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.2.0', '>=')
        ) {
            if ($addressType === 'billing') {
                $billing   = $user->getDefaultBillingAddress();
                $attribute = $this->getOrCreateBillingAttribute($billing);
            } elseif ($addressType === 'shipping') {
                $shipping  = $user->getDefaultShippingAddress();
                $attribute = $this->getOrCreateShippingAttribute($shipping);
            }

        } else {

            if ($addressType === 'billing') {
                $billing   = $user->getBilling();
                $attribute = $this->getOrCreateBillingAttribute($billing);
            } elseif ($addressType === 'shipping') {
                $shipping  = $user->getShipping();
                $attribute = $this->getOrCreateShippingAttribute($shipping);
            }
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
        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.3.0', '>=')
        ) {
            $billing = $user->getDefaultBillingAddress();
        } else {
            $billing = $user->getBilling();
        }

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
        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.3.0', '>=')
        ) {
            $shipping = $user->getDefaultShippingAddress();
        } else {
            $shipping = $user->getShipping();
        }

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

        $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $userAttribute = $this->getOrCreateUserAttribute($user);

        $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
        $userAttribute->setMoptPayoneConsumerscoreResult($response->getStatus());
        $userAttribute->setMoptPayoneConsumerscoreColor($response->getScore());
        $userAttribute->setMoptPayoneConsumerscoreValue($response->getScorevalue());

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

        $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
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

        $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
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

        $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
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
        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.2.0', '>='))
        {
            $orderVariables =  Shopware()->Session()->sOrderVariables;

            // ordervariables are null until shippingPayment view
            if ($orderVariables !== null){
                $aOrderVars = $orderVariables->getArrayCopy();
                $addressId  = $aOrderVars['sUserData']['billingaddress']['id'];
            } else {
                $userData = Shopware()->Modules()->Admin()->sGetUserData();
                $addressId  = $userData['billingaddress']['id'];

            }

            // Update Model and Flush for SW 5.3; when using sql like below this does not happen
            $address             = Shopware()->Models()->getRepository('Shopware\Models\Customer\Address')->find($addressId);
            $address->setStreet($response->getStreet());
            $address->setZipcode($response->getZip());
            $address->setCity($response->getCity());
            Shopware()->Models()->persist($address);
            Shopware()->Models()->flush($address);
            $session = Shopware()->Session();
            $session->offsetSet('moptAddressCorrected', true);
        } else {
            $sql = 'UPDATE `s_user_billingaddress` SET street=?, zipcode=?, city=?  WHERE userID = ?';

            Shopware()->Db()->query(
                $sql,
                array(
                    $response->getStreet(),
                    $response->getZip(),
                    $response->getCity(),
                    $userId)
            );
        }

    }

  /**
   * save corrected shipping address
   *
   * @param string $userId
   * @param object $response
   */
    public function saveCorrectedShippingAddress($userId, $response)
    {
        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.2.0', '>=')
        ) {
            $orderVariables =  Shopware()->Session()->sOrderVariables;

            if ($orderVariables !== null){
                $aOrderVars = $orderVariables->getArrayCopy();
                $addressId  = $aOrderVars['sUserData']['shippingaddress']['id'];
            } else {
                $userData = Shopware()->Modules()->Admin()->sGetUserData();
                $addressId  = $userData['shippingaddress']['id'];

            }
            // Update Model and Flush for SW 5.3; when using sql like below this does not happen
            $address             = Shopware()->Models()->getRepository('Shopware\Models\Customer\Address')->find($addressId);
            $address->setStreet($response->getStreet());
            $address->setZipcode($response->getZip());
            $address->setCity($response->getCity());
            Shopware()->Models()->persist($address);
            Shopware()->Models()->flush($address);
            $session = Shopware()->Session();
            $session->offsetSet('moptAddressCorrected', true);
        } else {
            $sql = 'UPDATE `s_user_shippingaddress` SET street=?, zipcode=?, city=?  WHERE userID = ?';
            Shopware()->Db()->query(
                $sql,
                array(
                    $response->getStreet(),
                    $response->getZip(),
                    $response->getCity(),
                    $userId)
            );
        }
    }

  /**
   * reset address check data
   *
   * @param string $userId
   */
    public function resetAddressCheckData($userId)
    {

        // Todo SW 5.3
        $sql       = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
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
        $shippingAttributes['moptPayoneAddresscheckDate']   = null;

        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.2.0', '>=')
        ) {
            $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
            $shipping = $user->getDefaultShippingAddress();
            $shippingAttribute = $this->getOrCreateShippingAttribute($shipping);
            $shippingAttributes['moptPayoneAddresscheckResult'] = $shippingAttribute->getMoptPayoneAddresscheckResult();
            $shippingAttributes['moptPayoneAddresscheckDate'] = $shippingAttribute->getMoptPayoneAddresscheckDate();
            if (is_string($shippingAttributes['moptPayoneAddresscheckDate'])){
                $shippingAttributes['moptPayoneAddresscheckDate'] = DateTime::createFromFormat(
                    'Y-m-d',
                    $shippingAttribute->getMoptPayoneAddresscheckDate()
                );
            }
        } else {

            $sql = 'SELECT `id` FROM `s_user_shippingaddress` WHERE userID = ?';
            $shippingId = Shopware()->Db()->fetchOne($sql, $userId);

            $sql = 'SELECT `mopt_payone_addresscheck_result`, '
                . '`mopt_payone_addresscheck_date` FROM `s_user_shippingaddress_attributes` WHERE shippingID = ?';
            $result = Shopware()->Db()->fetchAll($sql, $shippingId);

            if ($result) {
                $shippingAttributes['moptPayoneAddresscheckResult'] = $result[0]['mopt_payone_addresscheck_result'];
                $shippingAttributes['moptPayoneAddresscheckDate'] = DateTime::createFromFormat(
                    'Y-m-d',
                    $result[0]['mopt_payone_addresscheck_date']
                );
            }
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
        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
            $repository = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer');
            $userID = $userArray['additional']['user']['id'];

            $userObject = !empty($userID) ?
                $repository->find($userID) : null;
            $billingLegacyAddress = !empty($userObject) ?
                $userObject->getBilling() : null;
            $billingLegacyAttribute = !empty($billingLegacyAddress) ?
                $this->getOrCreateBillingAttribute($billingLegacyAddress) : null;
            $moptBillingColor = !empty($billingLegacyAttribute) ?
                $billingLegacyAttribute->getMoptPayoneConsumerscoreColor() : null;

            $shippingLegacyAddress = !empty($userObject) ?
                $userObject->getShipping() : null;
            $shippingLegacyAttribute = !empty($shippingLegacyAddress) ?
                $this->getOrCreateShippingAttribute($shippingLegacyAddress) : null;
            $moptShipmentColor = !empty($shippingLegacyAttribute) ?
                $shippingLegacyAttribute->getMoptPayoneConsumerscoreColor() : null;

            $moptScoreColor = $userArray['additional']['user']['mopt_payone_consumerscore_color'];
        } else {
            $moptBillingColor = $userArray['billingaddress']['moptPayoneConsumerscoreColor'];
            $moptShipmentColor = $userArray['shippingaddress']['moptPayoneConsumerscoreColor'];
            $moptScoreColor = $userArray['additional']['user']['moptPayoneConsumerscoreColor'];
        }
        
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
    
        if (in_array($biggestScore, array(1,2,3))) {
            return $biggestScore;
        } else {
            return -3; //no checks are active for this payment method
        }
    
    }
  
  /**
   *
   * @param type $value the color value, can be NULL if not computed (e.g. user not logged in)
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
              ->findOneBy(array('customerBillingId' => $object->getId()))) {
                $attribute = new Shopware\Models\Attribute\CustomerBilling();
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
              ->findOneBy(array('customerShippingId' => $object->getId()))) {
                $attribute = new Shopware\Models\Attribute\CustomerShipping();
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
            $payoneStatus  = $attributeData->getMoptPayoneStatus();
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
                    $db  = Shopware()->Db();
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
        $db  = Shopware()->Db();
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
        $userConsumerScoreData['moptPayoneConsumerscoreDate']   = null;

        $sql    = 'SELECT `mopt_payone_consumerscore_result`, '
            . '`mopt_payone_consumerscore_date` FROM `s_user_attributes` WHERE userID = ?';
        $result = Shopware()->Db()->fetchAll($sql, $userId);

        if ($result) {
            $userConsumerScoreData['moptPayoneConsumerscoreResult'] = $result[0]['mopt_payone_consumerscore_result'];
            $userConsumerScoreData['moptPayoneConsumerscoreDate']   = DateTime::createFromFormat(
                'Y-m-d',
                $result[0]['mopt_payone_consumerscore_date']
            );
        }

        return $userConsumerScoreData;
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
        $userBillingAddressCheckData['moptPayoneAddresscheckDate']   = null;

        if (\Shopware::VERSION === '___VERSION___' ||
            version_compare(\Shopware::VERSION, '5.2.0', '>=')
        ) {
            $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
            $billing = $user->getDefaultBillingAddress();
            $billingAttribute = $this->getOrCreateBillingAttribute($billing);
            $userBillingAddressCheckData['moptPayoneAddresscheckResult'] = $billingAttribute->getMoptPayoneAddresscheckResult();
            $userBillingAddressCheckData['moptPayoneAddresscheckDate'] = $billingAttribute->getMoptPayoneAddresscheckDate();
            // Make sure this is a DateTime Object, sometimes?? string gets returned ???
            if (is_string($userBillingAddressCheckData['moptPayoneAddresscheckDate'])){
                $userBillingAddressCheckData['moptPayoneAddresscheckDate'] = DateTime::createFromFormat(
                    'Y-m-d',
                    $billingAttribute->getMoptPayoneAddresscheckDate()
                );
            }
        } else {

            $sql = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
            $billingId = Shopware()->Db()->fetchOne($sql, $userId);

            $sql = 'SELECT `mopt_payone_addresscheck_result`, `mopt_payone_addresscheck_date` '
            . 'FROM `s_user_billingaddress_attributes` WHERE billingID = ?';
            $result = Shopware()->Db()->fetchAll($sql, $billingId);

            if ($result) {
                $userBillingAddressCheckData['moptPayoneAddresscheckResult'] = $result[0]['mopt_payone_addresscheck_result'];
                $userBillingAddressCheckData['moptPayoneAddresscheckDate']   = DateTime::createFromFormat(
                    'Y-m-d',
                    $result[0]['mopt_payone_addresscheck_date']
                );
            }
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
        $sql     = 'SELECT `id` FROM s_core_countries WHERE `countryiso` LIKE "' . $iso . '";';
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
        $sql     = 'SELECT `countryiso` FROM s_core_countries WHERE `id`="' . (int)$id . '";';
        $countryIso = Shopware()->Db()->fetchOne($sql);
        return $countryIso;
    }
  
    /**
     * get state id from iso
     *
     * @param string $id
     * @return string
     */
    public function getStateFromId($countryId, $stateIso)
    {
        $sql = 'SELECT `id` FROM s_core_countries_states WHERE `shortcode` LIKE "' . $stateIso .'" '
                . 'AND `countryID` = ' . $countryId . ';';
        $stateId = Shopware()->Db()->fetchOne($sql);

        if ($stateId) {
            return $stateId;
        } else {
            return null;
        }
        
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

        return (bool) $count;
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
        /**
         * @var $config \Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay
         */
        $config =  Shopware()->Models()->find(
            'Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay',
            1
        );
        return $config;
    }


}
