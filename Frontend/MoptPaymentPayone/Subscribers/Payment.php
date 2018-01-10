<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

class Payment implements SubscriberInterface
{

    /**
     * di container
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    /**
     * inject di container
     *
     * @param \Shopware\Components\DependencyInjection\Container $container
     */
    public function __construct(\Shopware\Components\DependencyInjection\Container $container)
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
        return array(
            // group creditcard payments
            'sAdmin::sGetPaymentMeans::after' => 'onGetPaymentMeans',
            // save paymentdata
            'sAdmin::sValidateStep3::after' => 'onValidateStep3',
            // hook for getting dispatch basket, used to calculate correct shipment costs for credit card payments
            'sAdmin::sGetDispatchBasket::after' => 'onGetDispatchBasket',
            // group creditcards
            'Shopware_Controllers_Frontend_Checkout::shippingPaymentAction::after' => 'onShippingPaymentAction',
            // correct wrong currency when saving unfinished orders
            'Shopware_Modules_Order_SaveOrder_FilterParams' => 'onOrder_SaveOrderProcessDetails',
        );
    }

    /**
     * consumerscore check after choice if payment method
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onValidateStep3(\Enlight_Hook_HookArgs $arguments)
    {
        $returnValues = $arguments->getReturn();
        if (!empty($returnValues['sErrorMessages'])) {
            return;
        }

        $userId = Shopware()->Session()->sUserId;
        $postData = Shopware()->Front()->Request()->getPost();
        $post = $postData['moptPaymentData'];
        $post['mopt_payone__cc_Year'] = $postData['mopt_payone__cc_Year'];
        $post['mopt_payone__klarna_Year'] = $postData['mopt_payone__klarna_Year'];
        $post['mopt_payone__klarna_Month'] = $postData['mopt_payone__klarna_Month'];
        $post['mopt_payone__klarna_Day'] = $postData['mopt_payone__klarna_Day'];

        $paymentName = $returnValues['paymentData']['name'];
        $paymentId = $postData['payment'] ? $postData['payment'] : $postData['register']['payment'];
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $config = $moptPayoneMain->getPayoneConfig($paymentId);
        $session = Shopware()->Session();

        if ($config['saveTerms'] !== 0) {
            if (Shopware()->Front()->Request()->getParam('sAGB') === '1') {
                $session->moptAgbChecked = true;
            }
            if (Shopware()->Front()->Request()->getParam('sAGB') === '0') {
                $session->moptAgbChecked = false;
            }
        }

        //check if payone payment method, exit if not and delete pament data
        if (!$moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName)) {
            $moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
            unset($session->moptMandateData);
            return;
        }

        //@TODO check if still used
        if ($postData['register'] && $postData['register']['payment'] === 'mopt_payone_creditcard') {
            $paymentName = $postData['register']['payment'];
        }

        $paymentData = $moptPayoneMain->getFormHandler()
                ->processPaymentForm($paymentName, $post, $moptPayoneMain->getPaymentHelper());

        if (isset($paymentData['formData']['mopt_save_birthday_and_phone']) && $paymentData['formData']['mopt_save_birthday_and_phone']) {
            $moptPayoneMain->getPaymentHelper()->moptUpdateUserInformation($userId, $paymentData);
        }

        if (isset($paymentData['formData']['mopt_save_birthday']) && $paymentData['formData']['mopt_save_birthday']) {
            $moptPayoneMain->getPaymentHelper()->moptUpdateUserInformation($userId, $paymentData);
        }

        if (isset($paymentData['formData']['mopt_save_phone'])) {
            $moptPayoneMain->getPaymentHelper()->moptUpdateUserInformation($userId, $paymentData);
        }

        if (count($paymentData['sErrorFlag'])) {
            $error = true;
            $moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
        }

        if ($error) {
            $sErrorMessages[] = Shopware()->Snippets()
                            ->getNamespace('frontend/account/internalMessages')->get('ErrorFillIn', 'Please fill in all red fields');
            $returnValues['checkPayment']['sErrorFlag'] = $paymentData['sErrorFlag'];
            $returnValues['checkPayment']['sErrorMessages'] = $sErrorMessages;
        } else {
            //cleanup session
            unset($session->moptMandateData);

            //get user data
            $user = Shopware()->Modules()->Admin()->sGetUserData();
            $userData = $user['additional']['user'];
            $billingFormData = $user['billingaddress'];

            if ($moptPayoneMain->getPaymentHelper()->isPayoneDebitnote($returnValues['paymentData']['name'])) {
                //check if bankaccountcheck is enabled
                $bankAccountChecktype = $moptPayoneMain->getHelper()->getBankAccountCheckType($config);

                //check if manage mandate is enabled
                if ($config['mandateActive']) {
                    //perform bankaccountcheck
                    $params = $moptPayoneMain->getParamBuilder()->buildManageMandate($paymentId, $user, $paymentData['formData']);
                    $payoneServiceBuilder = $this->container->get('MoptPayoneBuilder');
                    $service = $payoneServiceBuilder->buildServiceManagementManageMandate();
                    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                        'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
                    ));
                    $request = new \Payone_Api_Request_ManageMandate($params);
                    $response = $service->managemandate($request);

                    if ($response->getStatus() == 'APPROVED') {
                        $moptMandateData = array();
                        $moptMandateData['mopt_payone__showMandateText'] = false;

                        if ($response->getMandateStatus() === 'pending') {
                            $moptMandateData['mopt_payone__showMandateText'] = true;
                            $moptMandateData['mopt_payone__mandateText'] = urldecode($response->getMandateText());
                        }

                        $moptMandateData['mopt_payone__mandateStatus'] = $response->getMandateStatus();
                        $moptMandateData['mopt_payone__mandateIdentification'] = $response->getMandateIdentification();
                        $moptMandateData['mopt_payone__creditorIdentifier'] = $response->getCreditorIdentifier();

                        $session->moptMandateData = $moptMandateData;
                    }

                    if ($response->getStatus() == 'ERROR') {
                        $returnValues['checkPayment']['sErrorMessages'][] = $moptPayoneMain
                                        ->getPaymentHelper()->moptGetErrorMessageFromErrorCodeViaSnippet('bankaccountcheck', $response->getErrorcode());
                        $session->moptPayment = $post;
                        $arguments->setReturn($returnValues);
                        return;
                    }
                } elseif ($bankAccountChecktype === 0 || $bankAccountChecktype === 1) {
                    //perform bankaccountcheck
                    $params = $moptPayoneMain->getParamBuilder()->buildBankaccountcheck($paymentId, $bankAccountChecktype, $billingFormData['countryID'], $paymentData['formData']);

                    $payoneServiceBuilder = $this->container->get('MoptPayoneBuilder');
                    $service = $payoneServiceBuilder->buildServiceVerificationBankAccountCheck();
                    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                        'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
                    ));
                    $request = new \Payone_Api_Request_BankAccountCheck($params);
                    $response = $service->check($request);

                    if ($response->getStatus() == 'ERROR' || $response->getStatus() == 'INVALID') {
                        $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid'] = true;
                        $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = $moptPayoneMain
                                ->getPaymentHelper()
                                ->moptGetErrorMessageFromErrorCodeViaSnippet('bankaccountcheck', $response->getErrorcode());

                        $session->moptPayment = $post;
                        $arguments->setReturn($returnValues);
                        return;
                    }

                    if ($response->getStatus() == 'BLOCKED') {
                        $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid'] = true;
                        $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = $moptPayoneMain
                                ->getPaymentHelper()
                                ->moptGetErrorMessageFromErrorCodeViaSnippet('bankaccountcheck', 'blocked');
                        $session->moptPayment = $post;
                        $arguments->setReturn($returnValues);
                        return;
                    }
                }
            }

            if ($config['consumerscoreActive'] && $config['consumerscoreCheckMoment'] == 1) {
                //check if consumerscore is still valid or needs to be checked
                $checkDate = new \DateTime($userData['mopt_payone_consumerscore_date']);
                if (!$moptPayoneMain->getHelper()->isConsumerScoreCheckValid($config['consumerscoreLifetime'], $checkDate)) {
                    // add flag and data to session
                    $session->moptConsumerScoreCheckNeedsUserAgreement = true;
                    $session->moptPaymentData = $paymentData;
                    $session->moptPaymentId = $paymentId;
                    //@TODO submit target
                }
            }

            //save data to table and session
            $session->moptPayment = $post;
            if (!$moptPayoneMain->getPaymentHelper()->isPayoneCreditcard($paymentId)) {
                $moptPayoneMain->getPaymentHelper()->savePaymentData($userId, $paymentData);
            }
        }

        $arguments->setReturn($returnValues);
    }

    /**
     * group creditcard payments
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onGetPaymentMeans(\Enlight_Hook_HookArgs $arguments)
    {
        $dontGroupCreditCardActions = array('addArticle', 'cart', 'changeQuantity', 'confirm');
        $request = Shopware()->Front()->Request();
        $actionName = $request->getActionName();
        $controllerName = $request->getControllerName();
        $targetAction = $request->getParam('sTargetAction');

        if ($controllerName === 'checkout') {
            return;
        }

        if (in_array($actionName, $dontGroupCreditCardActions)) {
            return;
        }

        if ($actionName == 'calculateShippingCosts' && $targetAction == 'cart') {
            return;
        }

        $paymentMeansWithGroupedCreditcard = $this->container->get('MoptPayoneMain')->getPaymentHelper()
                ->groupCreditcards($arguments->getReturn());

        if (!$paymentMeansWithGroupedCreditcard) {
            return;
        }

        $arguments->setReturn($paymentMeansWithGroupedCreditcard);
    }

    /**
     * special handling for grouped credit cards to calculate correct shipment costs
     *
     * @param \Enlight_Hook_HookArgs $arguments
     */
    public function onGetDispatchBasket(\Enlight_Hook_HookArgs $arguments)
    {
        $returnValues = $arguments->getReturn();
        $paymenthelper = $this->container->get('MoptPayoneMain')->getPaymentHelper();

        if (!$paymenthelper->isPayoneCreditcard($returnValues['paymentID'])) {
            return;
        }

        $postPaymentId = $this->container->get('front')->Request()->getPost('sPayment');
        $sessionPaymentId = $this->container->get('session')->offsetGet('sPaymentID');
        $paymentID = $arguments->get('paymentID');
        $user = $arguments->getSubject()->sGetUserData();

        if (!empty($paymentID)) {
            $paymentID = (int) $paymentID;
        } elseif (!empty($user['additional']['payment']['id'])) {
            $paymentID = (int) $user['additional']['payment']['id'];
        } elseif (!empty($postPaymentId)) {
            $paymentID = (int) $postPaymentId;
        } elseif (!empty($sessionPaymentId)) {
            $paymentID = (int) $sessionPaymentId;
        }

        $paymentname = $paymenthelper->getPaymentNameFromId($paymentID);
        if ($paymenthelper->isPayoneCreditcardNotGrouped($paymentname)) {
            $returnValues['paymentID'] = $paymentID;
            $arguments->setReturn($returnValues);
        }
    }

    /**
     * group credit cards for payment form
     *
     * @param \Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onShippingPaymentAction(\Enlight_Hook_HookArgs $arguments)
    {
        $subject = $arguments->getSubject();
        $moptPayoneMain = $this->container->get('MoptPayoneMain');

        $groupedPaymentMeans = $moptPayoneMain->getPaymentHelper()->groupCreditcards($subject->View()->sPayments);
        if ($groupedPaymentMeans) {
            $subject->View()->sPayments = $groupedPaymentMeans;
        }
    }

    /**
     * change currency to the original order currency
     *
     * @param \Enlight_Event_EventArgs
     * @return array
     */
    public function onOrder_SaveOrderProcessDetails(\Enlight_Event_EventArgs $arguments)
    {
        $orderParams = $arguments->getReturn();
        $subject = $arguments->get('subject');
        $originalOrderCurrencyId = $subject->sBasketData['sCurrencyId'];
        $moptPayoneMain = $this->container->get('MoptPayoneMain');
        $paymenthelper = $this->container->get('MoptPayoneMain')->getPaymentHelper();
        $paymentName = $paymenthelper->getPaymentNameFromId($orderParams['paymentID']);

        /** @var \Shopware\Components\Model\ModelManager $em */
        $em = Shopware()->Models();

        $originalCurrency = $em->getRepository(\Shopware\Models\Shop\Currency::class)->findOneBy(array('id' => $originalOrderCurrencyId))->toArray();

        if ($moptPayoneMain->getPaymentHelper()->isPayonePaymentMethod($paymentName) && $originalCurrency && $originalCurrency['id'] !== $subject->sSYSTEM->sCurrency['id']) {

            // change currency Object to render emails correctly
            $subject->sSYSTEM->sCurrency['id'] = $originalCurrency['id'];
            $subject->sSYSTEM->sCurrency['name'] = $originalCurrency['name'];
            $subject->sSYSTEM->sCurrency['currency'] = $originalCurrency['currency'];
            $subject->sSYSTEM->sCurrency['factor'] = $originalCurrency['factor'];
            $subject->sSYSTEM->sCurrency['symbol'] = $originalCurrency['symbol'];
            // change order params to correct order currency in backend display
            $orderParams['currency'] = $originalCurrency['currency'];
            $orderParams['currencyFactor'] = $originalCurrency['factor'];
        }
        return $orderParams;
    }
}
