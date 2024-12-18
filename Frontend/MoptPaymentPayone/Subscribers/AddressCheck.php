<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Customer\Customer;

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
            // risk management:Frontend extend sAdmin prepare payone risk checks
            'sAdmin::sManageRisks::before' => 'sAdmin__sManageRisks__before',
            // risk management:Frontend extend sAdmin clean up payone risk checks
            'sAdmin::sManageRisks::after' => 'sAdmin__sManageRisks__after',
            // risk management:Frontend extend sAdmin - check payone risks
            'sAdmin::executeRiskRule::replace' => 'sAdmin__executeRiskRule',
            // check if addresscheck is valid if activated and group creditcards
            'Shopware_Controllers_Frontend_Checkout::confirmAction::after' => 'onConfirmAction',
            // hook for saving addresscheck result during registration process
            'Shopware_Controllers_Frontend_Register::saveRegisterAction::after' => 'onSaveRegister',
            // hook for invalidating a changed address in Shopware versions > 5.2
            'Shopware_Controllers_Frontend_Address::ajaxSaveAction::after' => 'onUpdateAddress',
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
     */
    public function sAdmin__sManageRisks__before(\Enlight_Hook_HookArgs $arguments)
    {
        Shopware()->Session()->moptRiskCheckPaymentId = $arguments->get('paymentID');
    }

    /**
     * clean up payone risk checks
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function sAdmin__sManageRisks__after(\Enlight_Hook_HookArgs $arguments)
    {
        unset(Shopware()->Session()->moptRiskCheckPaymentId);
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
        $subject = $arguments->getSubject();

        // execute parent call if rule is not payone
        if (strpos($rule, 'sRiskMOPT_PAYONE__') !== 0) {
            if(method_exists($subject, $rule) ||
               count($this->container->get('events')->getListeners('shopware_modules_admin_execute_risk_rule_'.$rule)) > 0
            )
            {
                $arguments->setReturn(
                    $subject->executeParent(
                        $arguments->getMethod(),
                        $arguments->getArgs()
                    )
                );
            }
        } else {
            $paymentID = Shopware()->Session()->moptRiskCheckPaymentId;
            /** @var \Mopt_PayoneMain $moptPayoneMain */
            $moptPayoneMain = $this->container->get('MoptPayoneMain');
            $config = $moptPayoneMain->getPayoneConfig($paymentID);

            $currentPaymentId = Shopware()->Session()->sPaymentID;
            $currentPaymentName = $moptPayoneMain->getPaymentHelper()->getPaymentNameFromId($currentPaymentId);

            if(strpos($currentPaymentName, 'mopt_payone__ewallet_amazon_pay') === 0) {
                $arguments->setReturn(false);
                return;
            }

            if (!$config['adresscheckActive'] && !$config['consumerscoreActive']) {
                $arguments->setReturn(false);
                return;
            }

            $value = $arguments->get('value');
            $basket = $arguments->get('basket');
            $user = $arguments->get('user');
            $paymentName = $moptPayoneMain->getPaymentHelper()->getPaymentNameFromId($paymentID);
            $userId = $user['additional']['user']['id'] ? $user['additional']['user']['id'] : null;
            $billingAddressData = $user['billingaddress'];
            $billingAddressData['country'] = $billingAddressData['countryId'];
            $shippingAddressData = $user['shippingaddress'];
            $shippingAddressData['country'] = $billingAddressData['countryId'];
            $basketAmount = $basket['AmountNumeric'];
            $isPayonePaypal = $moptPayoneMain->getPaymentHelper()->isPayonePaypal($paymentName);
            // remove _1 ,_2 ... from duplicated payments before matching
            $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);

            if (in_array($cleanedPaymentName,\Mopt_PayoneConfig::PAYMENTS_ADDRESSCHECK_EXCLUDED)) {
                    $arguments->setReturn(false);
                    return;
            }

            $userObject = $userId ? Shopware()->Models()
                ->getRepository(\Shopware\Models\Customer\Customer::class)
                ->find($userId) : null;

            if (!$userObject) {
                $arguments->setReturn(false);
                return;
            }

            /* We need to skip both address checks if the risk rules were executed in
             * an early stage, that does not provide the Billing & Shipping objects.
             */
            $isBillingAttribWriteable = $this->isBillingAttribWriteable($userObject);
            if (!$isBillingAttribWriteable) {
                $arguments->setReturn(false);
                return;
            }

            $isShippingAttribWriteable = $this->isShippingAttribWriteable($userObject);
            if (!$isShippingAttribWriteable) {
                $arguments->setReturn(false);
                return;
            }

            // perform billingAddressCheck if configured and required
            if ($this->getBillingAddressCheckIsNeeded(
                    $config,
                    $userId,
                    $basketAmount,
                    $paymentName,
                    $billingAddressData['country']
                ) && $isBillingAttribWriteable) {
                // perform check
                $params = $moptPayoneMain
                    ->getParamBuilder()
                    ->getAddressCheckParams(
                        $billingAddressData,
                        $billingAddressData,
                        $paymentID
                    );
                $billingAddressChecktype = $moptPayoneMain->getHelper()
                    ->getAddressChecktype($config, 'billing', $billingAddressData['country']);
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
                    null,
                    false,
                    $billingAddressData
                );
                if (!empty($errors['sErrorFlag'])) {
                    $arguments->setReturn(true);
                    return;
                }
            }

            // Do not check Packstation addresses
            if ($moptPayoneMain->getHelper()->isWunschpaketActive() &&
                is_numeric($shippingAddressData['street']))
            {
                $arguments->setReturn(false);
                return;
            }

            // perform shippingAddressCheck if configured and required
            $isDifferentAddress = $shippingAddressData['id'] !== $billingAddressData['id'];
            if ($isDifferentAddress && $this->getShippingAddressCheckIsNeeded(
                    $config,
                    $userId,
                    $basketAmount,
                    $paymentName,
                    $shippingAddressData['country']
                ) && $isShippingAttribWriteable) {
                // perform check
                $params = $moptPayoneMain
                    ->getParamBuilder()
                    ->getAddressCheckParams(
                        $shippingAddressData,
                        $shippingAddressData,
                        $paymentID
                    );
                $shippingAddressChecktype = $moptPayoneMain->getHelper()
                    ->getAddressChecktype($config, 'shipping', $shippingAddressData['country']);
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
                    null,
                    false,
                    $shippingAddressData
                );
                if (!empty($errors['sErrorFlag'])) {
                    $arguments->setReturn(true);
                    return;
                }
            }

            // perform consumerScoreCheck if configured
            if ($this->getCustomerCheckIsNeeded($config, $userId, $basketAmount, $paymentName) &&
                $config['consumerscoreCheckMoment'] == 0
            ) {
                $userData = $user['additional']['user']; //get user data
                try {
                    $response = $this->performConsumerScoreCheck($config, $user['billingaddress'], $paymentID);
                    $userData['moptPayoneConsumerscoreResult'] = $response->getStatus(); //update userdata with result
                    $userData['moptPayoneConsumerscoreDate'] = date('Y-m-d');

                    if (!$this->handleConsumerScoreCheckResult($response, $config, $userData['id'])) {
                        //abort
                        $arguments->setReturn(true);
                        return;
                    }
                } catch (\Exception $e) {
                    if ($config['consumerscoreFailureHandling'] === 0) {
                        $arguments->setReturn(true);
                    } else {
                        $arguments->setReturn(false);
                    }
                    return;
                }
            }

            if ($this->$rule($user, $value, $config, $moptPayoneMain)) {
                $arguments->setReturn(true);
                return;
            }
        }
    }


    private function isBillingAttribWriteable(Customer $customer)
    {
        try {
            $billingObject = $customer->getDefaultBillingAddress();

            $moptPayoneMain = $this->container->get('MoptPayoneMain');
            $moptPayoneMain->getHelper()->getOrCreateBillingAttribute($billingObject);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    private function isShippingAttribWriteable(Customer $customer)
    {
        try {
            $shippingObject = $customer->getDefaultShippingAddress();

            $moptPayoneMain = $this->container->get('MoptPayoneMain');
            $moptPayoneMain->getHelper()->getOrCreateShippingAttribute($shippingObject);
        } catch (\Exception $e) {
            return false;
        }
        return true;
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

        $forwardOnError = true;
        $paymentId = $subject->View()->sPayment['id'];
        // If payment method ist not Payone, just perform checks
        // to get results for the risk rules but do not forward...
        if (!$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($subject->View()->sPayment['name'])) {
            $forwardOnError = false;
            $paymentId = 0;
        }

        $config = $moptPayoneMain->getPayoneConfig($paymentId);
        $basketValue = $subject->View()->sAmount;
        $userData = $subject->View()->sUserData;
        $billingAddressData = $userData['billingaddress'];
        $billingAddressData['country'] = $billingAddressData['countryID'];
        $shippingAddressData = $userData['shippingaddress'];
        $shippingAddressData['country'] = $shippingAddressData['countryID'];
        $session = Shopware()->Session();
        $userId = $session->sUserId;
        $paymentName = $moptPayoneMain->getPaymentHelper()->getPaymentNameFromId($paymentId);
        $isPayonePaypal = $moptPayoneMain->getPaymentHelper()->isPayonePaypal($paymentName);
        // remove _1 ,_2 ... from duplicated payments before matching
        $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);

        if (in_array( $cleanedPaymentName,\Mopt_PayoneConfig::PAYMENTS_ADDRESSCHECK_EXCLUDED)) {
                $arguments->setReturn(false);
                return;
        }

        // get billing address attributes
        $userBillingAddressCheckData = $moptPayoneMain->getHelper()
            ->getBillingAddresscheckDataFromUserId($userId);
        // check if addresscheck is required for billing adress

        $billingAddressCheckRequired = $this->getBillingAddressCheckIsNeeded(
            $config,
            $userId,
            $basketValue,
            $paymentName,
            $billingAddressData['country']
        );

        if ($session->moptAddressCheckNeedsUserVerification) {
            $billingAddressCheckRequired = false;
        }
        if ($billingAddressCheckRequired &&
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
            $billingAddressChecktype = $moptPayoneMain->getHelper()
                ->getAddressChecktype($config, 'billing', $billingAddressData['country']);
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
                $forwardOnError,
                $billingAddressData
            );
            if (!empty($errors['sErrorFlag'])) {
                $ret = [];
                $ret['sErrorFlag'] = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        // Handle previous INVALID Response and forward accordingly
        if (!empty($session->moptAddressError)) {
            unset($session->moptAddressError);
            $response = new \Payone_Api_Response_AddressCheck_Invalid();
            $response->setStatus('INVALID');
            $errors = $this->handleBillingAddressCheckResult(
                $response,
                $config,
                $userId,
                $subject,
                $forwardOnError,
                $billingAddressData
            );
            if (!empty($errors['sErrorFlag'])) {
                $ret = [];
                $ret['sErrorFlag'] = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        // Do not check Packstation addresses
        if ($moptPayoneMain->getHelper()->isWunschpaketActive() &&
            is_numeric($shippingAddressData['street']))
        {
            return;
        }

        // get shipping address attributes
        $shippingAttributes = $moptPayoneMain->getHelper()
            ->getShippingAddressAttributesFromUserId($userId);
        // check if addresscheck is required for shipping address
        $shippingAddressCheckRequired = $this->getShippingAddressCheckIsNeeded(
            $config,
            $userId,
            $basketValue,
            $paymentName,
            $shippingAddressData['country']
        );

        if ($session->moptAddressCheckNeedsUserVerification) {
            $shippingAddressCheckRequired = false;
        }

        if ($shippingAddressCheckRequired &&
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
            $shippingAddressChecktype = $moptPayoneMain->getHelper()
                ->getAddressChecktype($config, 'shipping', $shippingAddressData['country']);
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
                $forwardOnError,
                $shippingAddressData
            );
            if (!empty($errors['sErrorFlag'])) {
                $ret = [];
                $ret['sErrorFlag'] = $errors['sErrorFlag'];
                $ret['sErrorMessages'] = $errors['sErrorMessages'];

                $arguments->setReturn($ret);
                return;
            }
        }

        if ($this->getCustomerCheckIsNeeded($config, $userId, $basketValue, $subject->View()->sPayment['name'])) {
            // perform check if prechoice is configured
            if ($config['consumerscoreCheckMoment'] == 0) {
                try {
                    $response = $this->performConsumerScoreCheck($config, $billingAddressData, $paymentId);
                    if ($forwardOnError && !$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                        // cancel, redirect to payment choice
                        if (Shopware()->Config()->get('version') === '___VERSION___' ||
                            version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=')
                        ) {
                            $subject->forward('shippingpayment', 'checkout', null);

                        } else {
                            $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                        }
                    }
                } catch (\Exception $e) {
                }
            } else {
                // set sessionflag if after paymentchoice is configured
                $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                $session->moptPaymentId = $subject->View()->sPayment['id'];
            }
        }
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
     * invalidate all check results on address change
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onUpdateAddress(\Enlight_Hook_HookArgs $arguments)
    {
        // @see https://github.com/PAYONE-GmbH/shopware-5/issues/324
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $payoneConfig = $moptPayoneMain->getPayoneConfig();

        // Handle Ratepay billing country changes
        $paymentName = $moptPayoneMain->getPaymentHelper()->getPaymentNameFromId(Shopware()->Session()->offsetGet('sPaymentID'));
        $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);
        if ($moptPayoneMain->getPaymentHelper()->isPayoneRatepayInvoice($cleanedPaymentName) ||
            $moptPayoneMain->getPaymentHelper()->isPayoneRatepayDirectDebit($cleanedPaymentName) ||
            $moptPayoneMain->getPaymentHelper()->isPayoneRatepayInstallment($cleanedPaymentName)
        ) {
            // $params = $arguments->getSubject()->Request()->getParams();
            // $moptPayoneHelper = \Mopt_PayoneMain::getInstance()->getHelper();
            // $changedBillingCountry = $moptPayoneHelper->getCountryIsoFromId($params['address']['country']);

            $user = Shopware()->Modules()->Admin()->sGetUserData();
            $changedBillingCountry = $user['additional']['country']['countryiso'];
            $currentRatepayCountry = Shopware()->Session()->offsetGet('moptRatepayCountry');

            if ($changedBillingCountry !== $currentRatepayCountry) {
                Shopware()->Session()->offsetSet('moptBillingCountryChanged', true);
                Shopware()->Session()->offsetUnset('moptRatepayCountry');
            }
        }

        // Handle address changes for Klarna payments
        if ($moptPayoneMain->getPaymentHelper()->isPayoneKlarnaDirectDebit($cleanedPaymentName) ||
            $moptPayoneMain->getPaymentHelper()->isPayoneKlarnaInstallments($cleanedPaymentName) ||
            $moptPayoneMain->getPaymentHelper()->isPayoneKlarnaInvoice($cleanedPaymentName) ||
            $moptPayoneMain->getPaymentHelper()->isPayonePaypalExpress($cleanedPaymentName) ||
            $moptPayoneMain->getPaymentHelper()->isPayonePaypalExpressv2($cleanedPaymentName)
        ) {
            Shopware()->Session()->offsetSet('moptKlarnaAddressChanged', true);
        }

        if (!$payoneConfig['consumerscoreActive'] && !$payoneConfig['adresscheckActive'] ) {
            return;
        }

        try {
            /** @var \Mopt_PayoneMain $moptPayoneMain */
            $userId = Shopware()->Session()->sUserId;
            $moptPayoneMain = $this->container->get('MoptPayoneMain');
            $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
            $userAttribute = $moptPayoneMain->getHelper()->getOrCreateUserAttribute($user);
            $userAttribute->setMoptPayoneConsumerscoreDate(0);
            Shopware()->Models()->persist($userAttribute);
            Shopware()->Models()->flush();
            $billing = $user->getDefaultBillingAddress();

            $billingAttribute = $moptPayoneMain->getHelper()->getOrCreateBillingAttribute($billing);
            $billingAttribute->setMoptPayoneAddresscheckDate(0);
            Shopware()->Models()->persist($billingAttribute);
            Shopware()->Models()->flush();
            $shipping = $user->getDefaultShippingAddress();

            $shippingAttribute = $moptPayoneMain->getHelper()->getOrCreateShippingAttribute($shipping);
            $shippingAttribute->setMoptPayoneAddresscheckDate(0);
            Shopware()->Models()->persist($shippingAttribute);
            Shopware()->Models()->flush();
        } catch (\Exception $exception) {
            unset($exception); // Ignore errors
        }
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

        $userId = $session->sUserId;
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config = $moptPayoneMain->getPayoneConfig();

        if ($result->getStatus() === \Payone_Api_Enum_ResponseType::INVALID ||
            $result->getStatus() === \Payone_Api_Enum_ResponseType::ERROR
        ) {
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

        $userId = $session->sUserId;
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config = $moptPayoneMain->getPayoneConfig();

        if ($result->getStatus() === \Payone_Api_Enum_ResponseType::INVALID ||
            $result->getStatus() === \Payone_Api_Enum_ResponseType::ERROR
        ) {
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
        unset(Shopware()->Session()->moptPaypalExpressWorkorderId);
        unset(Shopware()->Session()->moptPaypalv2ExpressWorkorderId);
        $userId = $session->sUserId;

        if ($this->getCustomerCheckIsNeeded($config, $userId, $basketValue, false)) {
            // perform check if prechoice is configured
            if ($config['consumerscoreCheckMoment'] == 0) {
                try {
                    $response = $this->performConsumerScoreCheck($config, $billingAddressData, 0);
                    if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                        // cancel, redirect to payment choice
                        if (version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=') ||
                            Shopware()->Config()->get('version') === '___VERSION___'
                        ) {
                            $subject->forward('shippingPayment', 'checkout');
                        } else {
                            $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                        }
                    }
                } catch (\Exception $e) {
                    if ($config['consumerscoreFailureHandling'] == 0) {
                        // abort and delete payment data and set to payone prepayment
                        $moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
                        $moptPayoneMain->getPaymentHelper()->deleteCreditcardPaymentData($userId);
                        $moptPayoneMain->getPaymentHelper()->setConfiguredDefaultPaymentAsPayment($userId);
                        if (version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=') ||
                            Shopware()->Config()->get('version') === '___VERSION___'
                        ) {
                            $subject->forward('shippingPayment', 'checkout', null);
                        } else {
                            $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                        }
                        return;
                    } else {
                        // continue

                        //$subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                        return;
                    }
                }
            } else {
                // set sessionflag if after paymentchoice is configured
                $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                $session->moptPaymentId = $subject->View()->sPayment['id'];
            }

        }
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
     * @param array $params
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
     * @param array $config
     * @param array $params
     * @param \Payone_Builder $payoneServiceBuilder
     * @param \Mopt_PayoneMain $mopt_payone__main
     * @param string $billingAddressChecktype
     * @return \Payone_Api_Response_AddressCheck_Invalid|\Payone_Api_Response_AddressCheck_Valid|\Payone_Api_Response_Error
     * @throws \Exception
     */
    protected function performAddressCheck(
        array $config,
        array $params,
        \Payone_Builder $payoneServiceBuilder,
        \Mopt_PayoneMain $mopt_payone__main,
        $billingAddressChecktype
    )
    {
        /** @var \Payone_Api_Service_Verification_AddressCheck $service */
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
     * @throws \Exception
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
        $userId = Shopware()->Session()->sUserId;
        $isCompany = $moptPayoneMain->getHelper()->isCompany($userId);
        if ($isCompany) {
            $request->setAddresschecktype(\Payone_Api_Enum_AddressCheckType::NONE);
            $request->setBusinessRelation(null);
            $request->setConsumerscoretype($config['consumerscoreCheckModeB2B']);
        } else {
            $request->setAddresschecktype(
                ($config['consumerscoreCheckModeB2C'] === \Payone_Api_Enum_ConsumerscoreType::BONIVERSUM_VERITA) ?
                    \Payone_Api_Enum_AddressCheckType::BONIVERSUM_PERSON :
                    \Payone_Api_Enum_AddressCheckType::NONE
            );
            $request->setConsumerscoretype($config['consumerscoreCheckModeB2C']);
        }

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
     * @param boolean $forwardOnError
     * @param $billingAddressData
     * @return array
     */
    protected function handleBillingAddressCheckResult(
        $response,
        $config,
        $userId,
        $caller,
        $forwardOnError,
        $billingAddressData
    )
    {
        $ret = [];
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $session = Shopware()->Session();

        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::VALID) {
            $secStatus = (int)$response->getSecstatus();
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

                    case 2: // depends on user and caller
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'billing',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        if ($forwardOnError) {
                            // add errormessage
                            $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                            $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain
                                ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck');
                            $session->moptAddressCheckNeedsUserVerification = true;
                            $session->moptAddressCheckOriginalAddress = $billingAddressData;
                            $session->moptAddressCheckCorrectedAddress = serialize($response);
                            $caller->forward(
                                'confirm',
                                'checkout',
                                null,
                                [
                                    'moptAddressCheckNeedsUserVerification' => true,
                                    'moptAddressCheckOriginalAddress' => $billingAddressData,
                                    'moptAddressCheckCorrectedAddress' => serialize($response),
                                    'moptAddressCheckTarget' => 'checkout'
                                ]
                            );
                        }
                        break;
                }
            }
        } else {
            $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);
            // save Response to prevent a second Check OnConfirm
            if ($caller === null) {
                $session->moptAddressError = $response;
            }
            if ($forwardOnError) {
                switch ($config['adresscheckFailureHandling']) {
                    case 0: // cancel transaction -> redirect to payment choice
                        $caller->forward('shippingPayment', 'checkout', null);
                        break;

                    case 1: // reenter address -> redirect to address form
                        if (Shopware()->Config()->get('version') === '___VERSION___' ||
                            version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=') ||
                            Shopware()->Config()->get('version') === '___VERSION___'
                        ) {
                            $caller->forward('edit', 'moptaddresspayone', null, [
                                'id' => $billingAddressData['id'],
                                'sTarget' => 'checkout',
                                'sTargetAction' => 'confirm'
                            ]);
                        } else {
                            $caller->forward('edit', 'address', null, [
                                'id' => $billingAddressData['id'],
                                'sTarget' => 'checkout',
                                'sTargetAction' => 'confirm'
                            ]);
                        }
                        break;
                    case 2: // perform consumerscore check
                        try {
                            $response = $this->performConsumerScoreCheck(
                                $config,
                                $billingAddressData,
                                $config['paymentId']
                            );
                            $this->handleConsumerScoreCheckResult($response, $config, $userId);
                        } catch (\Exception $e) {
                        }
                        break;
                }
            }
        }
        return $ret;
    }

    /**
     * @param mixed $response
     * @param array $config
     * @param $userId
     * @param mixed $subject
     * @param boolean $forwardOnError
     * @param $shippingAddressData
     * @return array
     */
    protected function handleShippingAddressCheckResult(
        $response,
        $config,
        $userId,
        $subject,
        $forwardOnError,
        $shippingAddressData
    )
    {
        $ret = [];
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $session = Shopware()->Session();

        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::VALID) {
            $secStatus = (int)$response->getSecstatus();
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

                    case 2: // depends on user and caller
                        $moptPayoneMain->getHelper()
                            ->saveAddressCheckResult(
                                'billing',
                                $userId,
                                $response,
                                $mappedPersonStatus
                            );
                        if ($forwardOnError) {
                            // add error message
                            $ret['sErrorFlag']['mopt_payone_configured_message'] = true;
                            $ret['sErrorFlag']['mopt_payone_corrected_message'] = true;
                            $ret['sErrorMessages']['mopt_payone_configured_message'] = $moptPayoneMain
                                ->getPaymentHelper()
                                ->moptGetErrorMessageFromErrorCodeViaSnippet('addresscheck');
                            $ret['sErrorMessages']['mopt_payone_corrected_message'] = $moptPayoneMain
                                ->getPaymentHelper()
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
                                    'moptShippingAddressCheckOriginalAddress' => $shippingAddressData,
                                    'moptShippingAddressCheckCorrectedAddress' => serialize($response),
                                    'moptShippingAddressCheckTarget' => 'checkout'
                                ]
                            );
                        }
                        break;
                }
            }
        } else {
            $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $response);

            if ($forwardOnError) {
                switch ($config['adresscheckFailureHandling']) {
                    case 0: // cancel transaction -> redirect to payment choice
                        if (Shopware()->Config()->get('version') === '___VERSION___' ||
                            version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=')
                        ) {
                            $subject->forward('shippingPayment', 'checkout', null);

                        } else {
                            $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                        }
                        break;

                    case 1: // reenter address -> redirect to address form
                        if (Shopware()->Config()->get('version') === '___VERSION___' ||
                            version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=')
                        ) {
                            $subject->forward('edit', 'moptaddresspayone', null, [
                                'id' => $shippingAddressData['id'],
                                'sTarget' => 'checkout',
                                'sTargetAction' => 'confirm'
                            ]);
                            $subject->forward('edit', 'address', null, ['sTarget' => 'checkout']);
                        }
                        break;
                    case 2: // perform consumerscore check
                        try {
                            $response = $this->performConsumerScoreCheck(
                                $config,
                                $shippingAddressData,
                                $config['paymentId']
                            );

                            if (!$this->handleConsumerScoreCheckResult($response, $config, $userId)) {
                                if (Shopware()->Config()->get('version') === '___VERSION___' ||
                                    version_compare(Shopware()->Config()->get('version'), '5.3.0', '>=')
                                ) {
                                    $subject->forward('shippingpayment', 'checkout', null);
                                } else {
                                    $subject->forward('payment', 'account', null, ['sTarget' => 'checkout']);
                                }
                            }
                        } catch (\Exception $e) {
                        }
                        break;

                    case 3: // proceed
                        return [];
                }
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

            // in case Boniversum returns unknown set response to backend user-defined value
            if ($response->getScore() === 'U') {
                $response->setScore($moptPayoneMain->getHelper()->getScoreColor($config));
            }
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
     *
     * @param array $config
     * @param string $userId
     * @param string $basketAmount
     * @param string $paymentName
     * @param integer $country
     * @return boolean
     */
    protected function getBillingAddressCheckIsNeeded($config, $userId, $basketAmount, $paymentName, $country)
    {
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');

        if ($paymentName && !$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName)) {
            return false;
        }

        if (!$userId) {
            return false;
        }

        // get billing address attributes
        $userBillingAddressCheckData = $moptPayoneMain->getHelper()
            ->getBillingAddresscheckDataFromUserId($userId);
        // check if addresscheck is required for billing adress
        $billingAddressCheckRequired = $moptPayoneMain
            ->getHelper()
            ->isBillingAddressToBeCheckedWithBasketValue(
                $config,
                $basketAmount,
                $country
            );
        if (($billingAddressCheckRequired === true) &&
            ($moptPayoneMain->getHelper()
                    ->isBillingAddressCheckValid(
                        $config['adresscheckLifetime'],
                        $userBillingAddressCheckData['moptPayoneAddresscheckResult'],
                        $userBillingAddressCheckData['moptPayoneAddresscheckDate']
                    ) === false
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param array $config
     * @param string $userId
     * @param string $basketAmount
     * @param string $paymentName
     * @param integer $country
     * @return boolean
     */
    protected function getShippingAddressCheckIsNeeded($config, $userId, $basketAmount, $paymentName, $country)
    {
        /** @var \Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $this->container->get('MoptPayoneMain');

        if ($paymentName && !$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName)) {
            return false;
        }

        if (!$userId) {
            return false;
        }

        // get shipping address attributes
        $shippingAttributes = $moptPayoneMain->getHelper()
            ->getShippingAddressAttributesFromUserId($userId);
        // check if addresscheck is required for shipping address
        $shippingAddressCheckRequired = $moptPayoneMain->getHelper()
            ->isShippingAddressToBeCheckedWithBasketValue(
                $config,
                $basketAmount,
                $country,
                $userId
            );

        if (($shippingAddressCheckRequired === true) &&
            ($moptPayoneMain->getHelper()
                    ->isShippingAddressCheckValid(
                        $config['adresscheckLifetime'],
                        $shippingAttributes['moptPayoneAddresscheckResult'],
                        $shippingAttributes['moptPayoneAddresscheckDate']
                    ) === false
            )
        ) {
            return true;
        } else {
            return false;
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

        // check backend config for B2B and B2C settings
        if ($moptPayoneMain->getHelper()->isCompany($userId) && $config['consumerscoreCheckModeB2B'] === 'NO' ){
            return false;
        }

        if (! $moptPayoneMain->getHelper()->isCompany($userId) && $config['consumerscoreCheckModeB2C'] === 'NO' ){
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
}
