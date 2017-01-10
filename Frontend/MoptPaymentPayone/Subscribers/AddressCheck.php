<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;

/**
 * Class AddressCheck
 *
 * @package Shopware\Plugins\MoptPaymentPayone\Subscribers
 */
class AddressCheck implements SubscriberInterface
{

    /**
    * di container
    *
    * @var Container
    */
    private $container;
    
    /**
     * inject di container
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * return array with all subsribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            //risk management:Frontend extend sAdmin prepare payone risk checks
            'sAdmin::sManageRisks::before' => 'sAdmin__sManageRisks__before',
            //risk management:Frontend extend sAdmin clean up payone risk checks
            'sAdmin::sManageRisks::after' => 'sAdmin__sManageRisks__after',
            //risk management:Frontend extend sAdmin - check payone risks
            'sAdmin::executeRiskRule::replace' => 'sAdmin__executeRiskRule',
            // check if addresscheck is valid if activated and group creditcards
            'Shopware_Controllers_Frontend_Checkout::confirmAction::after' => 'onConfirmAction',
            // hook for addresscheck
            'Shopware_Modules_Admin_ValidateStep2_FilterResult' => 'onValidateStep2',
            // hook for saving addresscheck result
            'sAdmin::sUpdateBilling::after' => 'onUpdateBilling',
            // hook for saving addresscheck result during registration process
            'Shopware_Controllers_Frontend_Register::saveRegister::after' => 'onSaveRegister',
            // hook for shipmentaddresscheck
            'Shopware_Modules_Admin_ValidateStep2Shipping_FilterResult' => 'onValidateStep2ShippingAddress',
            // hook for saving shipmentaddresscheck result
            'sAdmin::sUpdateShipping::after' => 'onUpdateShipping',
            // hook for saving consumerscorecheck result
            'sAdmin::sUpdatePayment::after' => 'onUpdatePayment',
            // check if consumerscore is valid if activated
            'Shopware_Controllers_Frontend_Checkout::shippingPaymentAction::after' => 'onShippingPaymentAction',
        ];
    }
    
  /**
   * prepare payone risk checks
   *
   * @param \Enlight_Hook_HookArgs $arguments
   * @return boolean
   */
    public function sAdmin__sManageRisks__before(\Enlight_Hook_HookArgs $arguments)
    {
        Shopware()->Session()->moptRiskCheckPaymentId = $arguments->get('paymentID');

        return 0;
    }
  
  /**
   * clean up payone risk checks
   *
   * @param \Enlight_Hook_HookArgs $arguments
   * @return boolean
   */
    public function sAdmin__sManageRisks__after(\Enlight_Hook_HookArgs $arguments)
    {
        unset(Shopware()->Session()->moptRiskCheckPaymentId);

        return 0;
    }
  
    /**
     * handle rules beginning with 'sRiskMOPT_PAYONE__'
     * returns true if risk condition is fulfilled
     * arguments: $rule, $user, $basket, $value
     *
     * * @param \Enlight_Hook_HookArgs $arguments
     */
    public function sAdmin__executeRiskRule(\Enlight_Hook_HookArgs $arguments)
    {
        $rule = $arguments->get('rule');
        
        // execute parent call if rule is not payone
        if (strpos($rule, 'sRiskMOPT_PAYONE__') !== 0) {
            $arguments->setReturn(
                $arguments->getSubject()->executeParent(
                    $arguments->getMethod(),
                    $arguments->getArgs()
                )
            );
        } else {
            $paymentID = Shopware()->Session()->moptRiskCheckPaymentId;
            $moptPayoneMain = $this->container->get('MoptPayoneMain');
            $config = $moptPayoneMain->getPayoneConfig($paymentID);
            
            if (!$config['adresscheckActive'] && !$config['consumerscoreActive']) {
                $arguments->setReturn(false);
                return;
            }

            $value = $arguments->get('value');
            $basket = $arguments->get('basket');
            $user = $arguments->get('user');
            $paymentName = $moptPayoneMain->getPaymentHelper()->getPaymentNameFromId($paymentID);
            $userId = $user['additional']['user']['id'] ? $user['additional']['user']['id'] : null;
            $basketAmount = $basket['AmountNumeric'];

            // perform consumerScoreCheck if configured
            if ($this->getCustomerCheckIsNeeded($config, $userId, $basketAmount, $paymentName) &&
                $config['consumerscoreCheckMoment'] == 0
            ) {
                $userData = $user['additional']['user']; //get user data
                try {
                    $response = $this->performConsumerScoreCheck($config, $user['billingaddress'], $paymentID);
                } catch (\Exception $e) {
                    
                    if ($config['consumerscoreFailureHandling'] === 0) {
                        $arguments->setReturn(true);  
                    } else {
                        $arguments->setReturn(false);
                    }   
                    return;
                }
                $userData['moptPayoneConsumerscoreResult'] = $response->getStatus(); //update userdata with result
                $userData['moptPayoneConsumerscoreDate'] = date('Y-m-d');

                if (!$this->handleConsumerScoreCheckResult($response, $config, $userData['id'])) {
                    //abort
                    $arguments->setReturn(true);
                    return;
                }
            }
            
            if ($this->$rule($user, $value, $config, $moptPayoneMain)) {
                $arguments->setReturn(true);
                return;
            }
        }
    }

    /**
     * @param array $config
     * @param array $params
     * @param \Payone_Api_Factory $payoneServiceBuilder
     * @param \Mopt_PayoneMain $mopt_payone__main
     * @param string $billingAddressChecktype
     * @return \Payone_Api_Response_AddressCheck_Invalid|\Payone_Api_Response_AddressCheck_Valid|\Payone_Api_Response_Error
     */
    protected function performAddressCheck(
        array $config,
        array $params,
        \Payone_Api_Factory $payoneServiceBuilder,
        \Mopt_PayoneMain $mopt_payone__main,
        $billingAddressChecktype
    ) {
        $service = $payoneServiceBuilder->buildServiceVerificationAddressCheck();
        $service->getServiceProtocol()->addRepository(
            Shopware()->Models()->getRepository(
                'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
            )
        );
        $request = new \Payone_Api_Request_AddressCheck($params);

        $request->setAddresschecktype($billingAddressChecktype);
        $request->setAid($config['subaccountId']);
        $request->setMode($mopt_payone__main->getHelper()
            ->getApiModeFromId($config['adresscheckLiveMode']));

        try {
            $response = $service->check($request);
        } catch (\Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * @param array $config
     * @param array $addressData
     * @param int $paymentID
     * @return \Payone_Api_Response_Consumerscore_Invalid|\Payone_Api_Response_Consumerscore_Valid|\Payone_Api_Response_Error
     */
    protected function performConsumerScoreCheck(array $config, array $addressData, $paymentID = 0)
    {
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        /** @var \Payone_Api_Factory $payoneServiceBuilder */
        $payoneServiceBuilder = $this->container->get('MoptPayoneBuilder');
        $params = $moptPayoneMain->getParamBuilder()
            ->getConsumerscoreCheckParams($addressData, $paymentID);
        $service = $payoneServiceBuilder->buildServiceVerificationConsumerscore();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $request = new \Payone_Api_Request_Consumerscore($params);

        $billingAddressChecktype = \Payone_Api_Enum_AddressCheckType::NONE;
        $request->setAddresschecktype($billingAddressChecktype);
        $request->setConsumerscoretype($config['consumerscoreCheckMode']);

        try {
            $response = $service->score($request);
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * @param mixed $response
     * @param $config
     * @param $userId
     * @param mixed $caller
     * @param $billingAddressData
     * @return array
     */
    protected function handleBillingAddressCheckResult($response, $config, $userId, $caller, $billingAddressData)
    {
        $ret = [];
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $session = Shopware()->Session();

        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::VALID) {
            $secStatus = (int) $response->getSecstatus();
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringValue(
                    $response->getPersonstatus(),
                    $config
                );
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringColorFromValue($mappedPersonStatus);
            // check secstatus and config
            if ($secStatus == \Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_CORRECT) {
                // valid address returned -> save result to db
                $moptPayoneMain->getHelper()
                    ->saveAddressCheckResult(
                        'billing',
                        $userId,
                        $response,
                        $mappedPersonStatus
                    );
            } else {
                // secstatus must be 20 - corrected address returned
                switch ($config['adresscheckAutomaticCorrection']) {
                    case 0: // auto correction
                        // save result to db
                        $moptPayoneMain->getHelper()
                            ->saveCorrectedBillingAddress(
                                $userId,
                                $response
                            );
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'billing',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        break;

                    case 1: // no correction
                        // save result to db
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'billing',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        break;

                    case 2: // depends on user
                        // add errormessage
                        $ret['sErrorFlag']['mopt_payone_configured_message']     = true;
                        $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain
                            ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck');
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'billing',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        $session->moptAddressCheckNeedsUserVerification = true;
                        $session->moptAddressCheckOriginalAddress = $billingAddressData;
                        $session->moptAddressCheckCorrectedAddress = serialize($response);
                        $caller->forward(
                            'confirm',
                            'checkout',
                            null,
                            [
                                'moptAddressCheckNeedsUserVerification' => true,
                                'moptAddressCheckOriginalAddress'       => $billingAddressData,
                                'moptAddressCheckCorrectedAddress'      => serialize($response),
                                'moptAddressCheckTarget'                => 'checkout'
                            ]
                        );
                        break;
                }
            }
        } else {
            $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);
            switch ($config['adresscheckFailureHandling']) {
                case 0: // cancel transaction -> redirect to payment choice
                    $caller->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                    break;

                case 1: // reenter address -> redirect to address form
                    $caller->forward('billing', 'account', null, ['sTarget' => 'checkout']);
                    break;

                case 2: // perform consumerscore check
                    $response = $this->performConsumerScoreCheck($config, $billingAddressData, $config['paymentId']);
                    $this->handleConsumerScoreCheckResult($response, $config, $userId);
                    break;
            }
        }
        return $ret;
    }

    /**
     * @param mixed $response
     * @param array $config
     * @param $userId
     * @param mixed $subject
     * @param $shippingAddressData
     * @return array
     */
    protected function handleShippingAddressCheckResult($response, $config, $userId, $subject, $shippingAddressData)
    {
        $ret = [];
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $session = Shopware()->Session();

        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::VALID) {
            $secStatus = (int) $response->getSecstatus();
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringValue($response->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringColorFromValue($mappedPersonStatus);
            // check secstatus and config
            if ($secStatus == \Payone_Api_Enum_AddressCheckSecstatus::ADDRESS_CORRECT) {
                // valid address returned -> save result to db
                $moptPayoneMain->getHelper()
                    ->saveAddressCheckResult(
                        'shipping',
                        $userId,
                        $response,
                        $mappedPersonStatus
                    );
            } else {
                // secstatus must be 20 - corrected address returned
                switch ($config['adresscheckAutomaticCorrection']) {
                    case 0: // auto correction
                        // save result to db
                        $moptPayoneMain->getHelper()
                            ->saveCorrectedShippingAddress(
                                $userId,
                                $response
                            );
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'shipping',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        break;

                    case 1: // no correction
                        // save result to db
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'shipping',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        break;

                    case 2: // depends on user
                        // add error message
                        $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                        $ret['sErrorFlag']['mopt_payone_corrected_message'] = true;
                        $ret['sErrorMessages']['mopt_payone_configured_message'] =
                            $moptPayoneMain->getPaymentHelper()
                            ->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck');
                        $ret['sErrorMessages']['mopt_payone_corrected_message'] =
                            $moptPayoneMain->getPaymentHelper()
                            ->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck', 'corrected');
                        // add decisionbox to template
                        $session->moptShippingAddressCheckNeedsUserVerification = true;
                        $session->moptShippingAddressCheckOriginalAddress = $shippingAddressData;
                        $session->moptShippingAddressCheckCorrectedAddress = serialize($response);
                        $subject->forward(
                            'confirm',
                            'checkout',
                            null,
                            [
                                'moptShippingAddressCheckNeedsUserVerification' => true,
                                'moptShippingAddressCheckOriginalAddress'       => $shippingAddressData,
                                'moptShippingAddressCheckCorrectedAddress'      => serialize($response),
                                'moptShippingAddressCheckTarget'                => 'checkout'
                            ]
                        );
                        break;
                }
            }
        } else {
            $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $response);

            switch ($config['adresscheckFailureHandling']) {
                case 0: //cancel transaction -> redirect to payment choice
                    $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                    break;

                case 1: // reenter address -> redirect to address form
                    $subject->forward('shipping', 'account', null, ['sTarget' => 'checkout']);
                    break;

                case 2: // perform consumerscore check
                    $response = $this->performConsumerScoreCheck($config, $shippingAddressData, $config['paymentId']);
                    if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                        $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                    }
                    break;

                case 3: // proceed
                    return [];
            }
        }

        return $ret;
    }

    /**
     * @param mixed $response
     * @param array $config
     * @param $userId
     * @return bool
     */
    protected function handleConsumerScoreCheckResult($response, $config, $userId)
    {
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');

        // handle ERROR, VALID, INVALID
        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::VALID) {
            // save result
            $moptPayoneMain->getHelper()->saveConsumerScoreCheckResult($userId, $response);
           return true;
        } else {
            // save ERROR, INVALID
            $moptPayoneMain->getHelper()->saveConsumerScoreError($userId, $response);
            return false;
        }
    }

    /**
     * Perform risk checks
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onConfirmAction(\Enlight_Hook_HookArgs $arguments)
    {
        $subject = $arguments->getSubject();
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
    
        // group credit cards for payment form
        $groupedPaymentMeans = $moptPayoneMain->getPaymentHelper()
            ->groupCreditcards($subject->View()->sPayments);
        if ($groupedPaymentMeans) {
            $subject->View()->sPayments = $groupedPaymentMeans;
        }

        // return if non payone method is choosen
        if (!$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($subject->View()->sPayment['name'])) {
            return;
        }
    
        $paymentId                      = $subject->View()->sPayment['id'];
        $config                         = $moptPayoneMain->getPayoneConfig($paymentId); //get config by payment id
        $basketValue                    = $subject->View()->sAmount;
        $userData                       = $subject->View()->sUserData;
        $billingAddressData             = $userData['billingaddress'];
        $billingAddressData['country']  = $billingAddressData['countryID'];
        $shippingAddressData            = $userData['shippingaddress'];
        $shippingAddressData['country'] = $shippingAddressData['countryID'];
        $session                        = Shopware()->Session();
        $userId                         = $session->sUserId;

        // check if addresscheck is active for billingadress and check
        $billingAddressChecktype = $moptPayoneMain
            ->getHelper()
            ->isBillingAddressToBeCheckedWithBasketValue(
                $config,
                $basketValue,
                $billingAddressData['country']
            );

        if ($session->moptAddressCheckNeedsUserVerification) {
            $billingAddressChecktype = false;
        }
        $userBillingAddressCheckData = $moptPayoneMain->getHelper()
            ->getBillingAddresscheckDataFromUserId($userId);
        if ($billingAddressChecktype &&
            !$moptPayoneMain
                ->getHelper()
                ->isBillingAddressCheckValid(
                    $config['adresscheckLifetime'],
                    $userBillingAddressCheckData['moptPayoneAddresscheckResult'],
                    $userBillingAddressCheckData['moptPayoneAddresscheckDate']
                )
        ) {
            // perform check
            $params = $moptPayoneMain
                ->getParamBuilder()
                ->getAddressCheckParams(
                    $billingAddressData,
                    $billingAddressData,
                    $paymentId
                );
            $response = $this->performAddressCheck(
                $config,
                $params,
                $this->container->get('MoptPayoneBuilder'),
                $moptPayoneMain,
                $billingAddressChecktype
            );

            // handle result
            $errors = $this->handleBillingAddressCheckResult(
                $response,
                $config,
                $userId,
                $subject,
                $billingAddressData
            );
            if (!empty($errors['sErrorFlag'])) {
                $ret['sErrorFlag']     = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        // get shippingaddress attributes
        $shippingAttributes = $moptPayoneMain->getHelper()
            ->getShippingAddressAttributesFromUserId($userId);
        // check if addresscheck is active for shippingadress and data is valid and check if necessary
        $shippingAddressChecktype = $moptPayoneMain->getHelper()
            ->isShippingAddressToBeCheckedWithBasketValue(
                $config,
                $basketValue,
                $shippingAddressData['country'],
                $userId
            );

        if ($session->moptAddressCheckNeedsUserVerification) {
            $shippingAddressChecktype = false;
        }

        if ($shippingAddressChecktype &&
            !$moptPayoneMain
                ->getHelper()
                ->isShippingAddressCheckValid(
                    $config['adresscheckLifetime'],
                    $shippingAttributes['moptPayoneAddresscheckResult'],
                    $shippingAttributes['moptPayoneAddresscheckDate']
                )
        ) {
            // perform check
            $params = $moptPayoneMain
                ->getParamBuilder()
                ->getAddressCheckParams(
                    $shippingAddressData,
                    $shippingAddressData,
                    $paymentId
                );
            $response = $this->performAddressCheck(
                $config,
                $params,
                $this->container->get('MoptPayoneBuilder'),
                $moptPayoneMain,
                $shippingAddressChecktype
            );

            // handle result
            $errors = $this->handleShippingAddressCheckResult(
                $response,
                $config,
                $userId,
                $subject,
                $shippingAddressData
            );
            if (!empty($errors['sErrorFlag'])) {
                $ret['sErrorFlag']     = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        if ($this->getCustomerCheckIsNeeded($config, $userId, $basketValue, $subject->View()->sPayment['name'])) {
            // perform check if prechoice is configured
            if ($config['consumerscoreCheckMoment'] == 0) {
                $response = $this->performConsumerScoreCheck($config, $billingAddressData, $paymentId);
                if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                    // cancel, redirect to payment choice
                    $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                }
            } else {
                // set sessionflag if after paymentchoice is configured
                $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                $session->moptPaymentId = $subject->View()->sPayment['id'];
            }
        }
    }

    /**
     *
     * @param array $config
     * @param string $userId
     * @param string $basketAmount
     * @param string $paymentName
     * @return boolean
     */
    protected function getCustomerCheckIsNeeded($config, $userId, $basketAmount, $paymentName)
    {
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        
        if ($paymentName && !$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName)) {
            return false;
        }
        
        if ($config['consumerscoreAbtestActive']) {
            $random = rand(0, $config['consumerscoreAbtestValue']);
            if ($random != 0) {
                return false;
            }
        }
        
        if (!$userId) {
            return false;
        }
        
        $amountInInterval = $moptPayoneMain->getHelper()
                ->isConsumerScoreToBeCheckedWithBasketValue($config, $basketAmount);
        $userConsumerScoreData = $moptPayoneMain->getHelper()->getConsumerScoreDataFromUserId($userId);
        $needsRecompution = !$moptPayoneMain->getHelper()
                ->isConsumerScoreCheckValid(
                    $config['consumerscoreLifetime'],
                    $userConsumerScoreData['moptPayoneConsumerscoreDate']
                );

        $userScoreDenied = ($userConsumerScoreData['moptPayoneConsumerscoreResult'] === 'DENIED');
        if ($userScoreDenied) {
            $needsRecompution = true;
        }
        
        return $amountInInterval && $needsRecompution && $config['consumerscoreActive'];
    }
  
  /**
   * billingaddress addresscheck
   *
   * @param \Enlight_Event_EventArgs $arguments
   */
    public function onValidateStep2(\Enlight_Event_EventArgs $arguments)
    {
        $ret = $arguments->getReturn();

        if (!empty($ret['sErrorMessages'])) {
            $arguments->setReturn($ret);
            return;
        }
    
        // get config data from main
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config         = $moptPayoneMain->getPayoneConfig();
        $postData       = $arguments->get('post');
        $session        = Shopware()->Session();

        // get basket value
        $basket      = Shopware()->Modules()->Basket()->sGetBasket();
        $basketValue = $basket['AmountNumeric'];

        $userId = $session->sUserId;

        // perform check if addresscheck is enabled
        $billingAddressChecktype = $moptPayoneMain->getHelper()->isBillingAddressToBeChecked(
            $config,
            $postData['register']['billing']['country']
        );

        if ($session->moptPayoneBillingAddresscheckResult) {
            $billingAddressChecktype = false;
        }

        if ($billingAddressChecktype) {
            // if nothing in basket, don't check and  just reset the validation date and result
            if (!$basketValue) {
                $moptPayoneMain->getHelper()->resetAddressCheckData($userId);
                return;
            }

            // no check when basket value outside configured values
            if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket']) {
                return;
            } else {
                $billingFormData  = $postData["register"]['billing'];
                $personalFormData = $postData["register"]['personal'];

                $params = $moptPayoneMain
                    ->getParamBuilder()
                    ->getAddressCheckParams(
                        $billingFormData,
                        $personalFormData
                    );
                $response = $this->performAddressCheck(
                    $config,
                    $params,
                    $this->container->get('MoptPayoneBuilder'),
                    $moptPayoneMain,
                    $billingAddressChecktype
                );

                // @TODO refactor, extract methods
                if ($response instanceof \Payone_Api_Response_AddressCheck_Valid) {
                    $session   = Shopware()->Session();
                    $secStatus = (int) $response->getSecstatus();
                    // check secstatus and config
                    if ($secStatus == 10) {
                        // valid address returned -> save result to session
                        $session->moptPayoneBillingAddresscheckResult = serialize($response);
                    } else {
                        // secstatus must be 20 - corrected address returned
                        switch ($config['adresscheckAutomaticCorrection']) {
                            case 0:
                                // this works only for address changes via account controller
                                $arguments->getSubject()->sSYSTEM->_POST['street'] = $response->getStreet();
                                $arguments->getSubject()->sSYSTEM->_POST['zipcode'] = $response->getZip();
                                $arguments->getSubject()->sSYSTEM->_POST['city'] = $response->getCity();
                                $session->moptPayoneBillingAddresscheckResult = serialize($response);
                                break;

                            case 1: // no correction
                                $session->moptPayoneBillingAddresscheckResult = serialize($response);
                                break;

                            case 2: // depends on user
                                // add address data to template
                                $session->moptAddressCheckNeedsUserVerification = true;
                                $session->moptAddressCheckOriginalAddress = $billingFormData;
                                $session->moptAddressCheckCorrectedAddress = serialize($response);
                                break;
                        }
                    }

                    // save corrected address or status to session in onUpdateShipping
                    $arguments->setReturn($ret);
                    return;
                }
                if ($response instanceof \Payone_Api_Response_AddressCheck_Invalid || $response instanceof \Payone_Api_Response_Error) {
                    $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);
                    $session->moptPayoneBillingAddresscheckResult = serialize($response);

                    $request = $this->container->get('Front')->Request(); // used to forward user
                    switch ($config['adresscheckFailureHandling']) {
                        case 0: // cancel transaction -> redirect to address input
                            $this->forward($request, 'billing', 'account', null, ['sTarget' => 'checkout']);
                            $arguments->setReturn($ret);
                            return;
                            break;

                        case 1: // reenter address -> redirect to address form
                            $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                            $ret['sErrorMessages']['mopt_payone_configured_message'] =
                                $moptPayoneMain->getPaymentHelper()
                                    ->moptGetErrorMessageFromErrorCodeViaSnippet(
                                        'addresscheck',
                                        $response->getErrorcode()
                                    );
                            if ($arguments->getSubject()->sCheckUser()) {
                                $this->forward($request, 'billing', 'account', null, ['sTarget' => 'checkout']);
                                $arguments->setReturn($ret);
                                return;
                            } else {
                                $this->forward($request, 'index', 'register', null, ['sTarget' => 'checkout']);
                                $arguments->setReturn($ret);
                                return;
                            }
                            break;

                        case 2: // perform consumerscore check
                            $billingFormData['countryID'] = $billingFormData['country'];
                            $response = $this->performConsumerScoreCheck($config, $billingFormData);
                            if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                                $this->forward($request, 'billing', 'account', null, ['sTarget' => 'checkout']);
                                return;
                            }
                            break;

                        case 3: // proceed
                            return;
                    }

                    $arguments->setReturn($ret);
                    return;
                }
            }
        }

        $arguments->setReturn($ret);
    }


  /**
   * save addresscheck result
   *
   * @param \Enlight_Hook_HookArgs $arguments
   */
    public function onSaveRegister(\Enlight_Hook_HookArgs $arguments)
    {
        $this->onUpdateBilling($arguments);
        $this->onUpdateShipping($arguments);
    }
  
  /**
   * save addresscheck result
   *
   * @param \Enlight_Hook_HookArgs $arguments
   */
    public function onUpdateBilling(\Enlight_Hook_HookArgs $arguments)
    {
        $session = Shopware()->Session();

        if (!($result = unserialize($session->moptPayoneBillingAddresscheckResult))) {
            return;
        }

        $userId         = $session->sUserId;
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config         = $moptPayoneMain->getPayoneConfig();

        if ($result->getStatus() === \Payone_Api_Enum_ResponseType::INVALID || $result->getStatus() === \Payone_Api_Enum_ResponseType::ERROR) {
            $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $result);
        } else {
            if ($result->getStatus() === \Payone_Api_Enum_ResponseType::VALID &&
                $result->getSecstatus() === '20' &&
                $config['adresscheckAutomaticCorrection'] === 0 &&
                Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'] === 'saveRegister'
            ) {
                $moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $result);
            }
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringValue($result->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringColorFromValue($mappedPersonStatus);
            $moptPayoneMain->getHelper()
                ->saveAddressCheckResult('billing', $userId, $result, $mappedPersonStatus);
        }

        unset($session->moptPayoneBillingAddresscheckResult);
    }

  /**
   *
   * shipmentaddress addresscheck
   *
   * @param \Enlight_Event_EventArgs $arguments
   */
    public function onValidateStep2ShippingAddress(\Enlight_Event_EventArgs $arguments)
    {
        $ret = $arguments->getReturn();

        if (!empty($ret['sErrorMessages'])) {
            $arguments->setReturn($ret);
            return;
        }

      //get config data from main
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config         = $moptPayoneMain->getPayoneConfig();
        $postData       = $arguments->get('post');
        $shippingAddressCountry = $moptPayoneMain->getHelper()
            ->getAddressCountryFromUserData($postData, true);
    
      //check if addresscheck is enabled
        if ($config['adresscheckActive']) {
            $shippingAddressChecktype = $moptPayoneMain->getHelper()
              ->getAddressChecktypeFromId(
                  $config['adresscheckShippingAdress'],
                  $config['adresscheckShippingCountries'],
                  $shippingAddressCountry
              );

          //return if shipping address checkmode is set to "no check"
            if (!$shippingAddressChecktype) {
                return;
            }

            if (isset($postData['sSelectAddress'])) {
                return;
            }

            $session          = Shopware()->Session();
            $userId           = $session->sUserId;
            $shippingFormData = $postData['register']['shipping'];
            $params           = $moptPayoneMain->getParamBuilder()
              ->getAddressCheckParams($shippingFormData, $shippingFormData);
            $response = $this->performAddressCheck(
                $config,
                $params,
                $this->container->get('MoptPayoneBuilder'),
                $moptPayoneMain,
                $shippingAddressChecktype
            );

            if ($response instanceof \Payone_Api_Response_AddressCheck_Valid) {
                $secStatus = (int) $response->getSecstatus();
                if ($secStatus === 10) {
                    // valid address returned, save result to session
                    $session->moptPayoneShippingAddresscheckResult = serialize($response);
                } else {
                    // secstatus must be 20 - corrected address returned
                    switch ($config['adresscheckAutomaticCorrection']) {
                        case 0: // auto correction
                            // this works only for address changes via account controller
                            $arguments->getSubject()->sSYSTEM->_POST['street'] = $response->getStreet();
                            $arguments->getSubject()->sSYSTEM->_POST['zipcode'] = $response->getZip();
                            $arguments->getSubject()->sSYSTEM->_POST['city'] = $response->getCity();
                            $session->moptPayoneShippingAddresscheckResult = serialize($response);
                            break;

                        case 1: // no correction
                            $session->moptPayoneShippingAddresscheckResult = serialize($response);
                            break;

                        case 2: // depends on user
                            // add address data to template
                            $session->moptShippingAddressCheckNeedsUserVerification = true;
                            $session->moptShippingAddressCheckOriginalAddress = $shippingFormData;
                            $session->moptShippingAddressCheckCorrectedAddress = serialize($response);
                            break;
                    }
                }

                // save corrected address or status to session in onUpdateShipping
                $arguments->setReturn($ret);
                return;
            }
            if ($response instanceof \Payone_Api_Response_AddressCheck_Invalid || $response instanceof \Payone_Api_Response_Error) {
                $ret['sErrorFlag']['mopt_payone_configured_message']     = true;
                $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain->getPaymentHelper()
                        ->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck', $response->getErrorcode());

                $request = $this->container->get('Front')->Request(); // used to forward user
                $session->moptPayoneShippingAddresscheckResult = serialize($response);

                switch ($config['adresscheckFailureHandling']) {
                    case 0: // cancel transaction -> redirect to payment choice
                        $arguments->setReturn($ret);
                        $this->forward($request, 'index', 'account', null, ['sTarget' => 'checkout']);
                        return;

                    case 1: // reenter address -> redirect to address form
                        if ($arguments->getSubject()->sCheckUser()) {
                            $this->forward($request, 'billing', 'account', null, ['sTarget' => 'checkout']);
                            $arguments->setReturn($ret);
                            return;
                        } else {
                            $this->forward($request, 'index', 'register', null, ['sTarget' => 'checkout']);
                            $arguments->setReturn($ret);
                            return;
                        }
                        break;

                    case 2: // perform consumerscore check
                        $shippingFormData['countryID'] = $shippingFormData['country'];
                        $response = $this->performConsumerScoreCheck($config, $shippingFormData);
                        if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                        //cancel transaction
                            $arguments->setReturn($ret);
                            $this->forward($request, 'index', 'account', null, ['sTarget' => 'checkout']);
                            return;
                        }
                        unset($ret['sErrorFlag']['mopt_payone_addresscheck']);
                        unset($ret['sErrorMessages']['mopt_payone_addresscheck']);
                        return;

                    case 3: // proceed
                        return;
                }

                $arguments->setReturn($ret);
                return;
            }
        }

        $arguments->setReturn($ret);
        return;
    }

  /**
   * save addresscheck result
   *
   * @param \Enlight_Hook_HookArgs $arguments
   */
    public function onUpdateShipping(\Enlight_Hook_HookArgs $arguments)
    {
        $session = Shopware()->Session();

        if (!($result = unserialize($session->moptPayoneShippingAddresscheckResult))) {
            return;
        }

        $userId         = $session->sUserId;
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config         = $moptPayoneMain->getPayoneConfig();

        if ($result->getStatus() === \Payone_Api_Enum_ResponseType::INVALID || $result->getStatus() === \Payone_Api_Enum_ResponseType::ERROR) {
            $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $result);
        } else {
            if ($result->getStatus() === \Payone_Api_Enum_ResponseType::VALID &&
                $result->getSecstatus() === '20' &&
                $config['adresscheckAutomaticCorrection'] === 0 &&
                Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'] === 'saveRegister'
            ) {
                $moptPayoneMain->getHelper()->saveCorrectedShippingAddress($userId, $result);
            }

            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringValue($result->getPersonstatus(), $config);
            $mappedPersonStatus = $moptPayoneMain->getHelper()
                ->getUserScoringColorFromValue($mappedPersonStatus);
            $moptPayoneMain->getHelper()
                ->saveAddressCheckResult('shipping', $userId, $result, $mappedPersonStatus);
        }
        unset($session->moptPayoneShippingAddresscheckResult);
    }


    /**
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onUpdatePayment(\Enlight_Hook_HookArgs $arguments)
    {
        $session = Shopware()->Session();

        if (!($result = unserialize($session->moptPayoneConsumerscorecheckResult))) {
            return;
        }

        $mopt_payone__main = $this->container->get('MoptPayoneMain');
        $mopt_payone__main->getHelper()->saveConsumerScoreCheckResult($session->sUserId, $result);

        unset($session->moptPayoneConsumerscorecheckResult);
    }
  
  
  /**
   * Forward the request to the given controller, module and action with the given parameters.
   * copied from Enlight_Controller_Action
   * and customized
   *
   * @param mixed $request
   * @param string $action
   * @param string $controller
   * @param string $module
   * @param array  $params
   */
    public function forward($request, $action, $controller = null, $module = null, array $params = null)
    {
        if ($params !== null) {
            $request->setParams($params);
        }
        if ($controller !== null) {
            $request->setControllerName($controller);
            if ($module !== null) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)->setDispatched(false);
    }
  
    /**
     * check if user score equals configured score to block payment method
     *
     * @param array $user
     * @param string $value
     * @param array $config
     * @param \Mopt_PayoneMain $payoneMain
     * @return bool
     */
    public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS($user, $value, $config, $payoneMain)
    {
        $scoring = $payoneMain->getHelper()->getScoreFromUserAccordingToPaymentConfig($user, $config);
        return $scoring == $value; //return true if payment has to be denied
    }

    /**
     * check if user score equals not configured score to block payment method
     *
     * @param array $user
     * @param string $value
     * @param array $config
     * @param \Mopt_PayoneMain $payoneMain
     * @return bool
     */
    public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT($user, $value, $config, $payoneMain)
    {
        return !$this->sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS($user, $value, $config, $payoneMain);
    }

    /**
     * check consumer score before payment choice if configured
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onShippingPaymentAction(\Enlight_Hook_HookArgs $arguments)
    {
        $subject = $arguments->getSubject();
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config = $moptPayoneMain->getPayoneConfig(); // get global config
        
        if (!$config['consumerscoreActive']) {
            return;
        }
        
        $basketValue = $subject->View()->sAmount;
        $userData = $subject->View()->sUserData;
        $billingAddressData = $userData['billingaddress'];
        $billingAddressData['country'] = $billingAddressData['countryID'];
        $shippingAddressData = $userData['shippingaddress'];
        $shippingAddressData['country'] = $shippingAddressData['countryID'];
        $session = Shopware()->Session();
        $userId = $session->sUserId;

        if ($this->getCustomerCheckIsNeeded($config, $userId, $basketValue, false)) {
            // perform check if prechoice is configured
            if ($config['consumerscoreCheckMoment'] == 0) {
                try {
                    $response = $this->performConsumerScoreCheck($config, $billingAddressData, 0);
                } catch (\Exception $e) {
                    if ($config['consumerscoreFailureHandling'] == 0) {
                        //abort
                        //delete payment data and set to payone prepayment
                        $moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
                        $moptPayoneMain->getPaymentHelper()->setConfiguredDefaultPaymentAsPayment($userId);
                        $this->forward('payment', 'account', null, array('sTarget' => 'checkout'));
                        return;
                    } else {
                        // continue
                        $this->forward('payment', 'account', null, array('sTarget' => 'checkout'));
                        return;
                    }
                }
                if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                    // cancel, redirect to payment choice
                    $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                }
            } else {
                // set sessionflag if after paymentchoice is configured
                $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                $session->moptPaymentId = $subject->View()->sPayment['id'];
            }
        }
    }
}
