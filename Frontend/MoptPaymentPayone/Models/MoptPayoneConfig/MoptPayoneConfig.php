<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneConfig;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_config")
 */
class MoptPayoneConfig extends ModelEntity
{

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="payment_id", type="integer", nullable=false, unique=true)
     */
    private $paymentId;

    /**
     * @ORM\Column(name="merchant_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $merchantId;

    /**
     * @ORM\Column(name="portal_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $portalId;

    /**
     * @ORM\Column(name="subaccount_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $subaccountId;

    /**
     * @ORM\Column(name="api_key", type="string", length=100, precision=0, scale=0, nullable=false)
     */
    private $apiKey;

    /**
     * @ORM\Column(name="live_mode", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $liveMode;

    /**
     * @ORM\Column(name="authorisation_method", type="string", length=100, precision=0, scale=0, nullable=false)
     */
    private $authorisationMethod;

    /**
     * @ORM\Column(name="submit_basket", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $submitBasket;

    /**
     * @ORM\Column(name="adresscheck_active", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckActive;

    /**
     * @ORM\Column(name="adresscheck_live_mode", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckLiveMode;

    /**
     * 0 = no check
     * 1 = basic check
     * 2 = person check
     *
     * @ORM\Column(name="adresscheck_billing_adress", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckBillingAdress;

    /**
     * @ORM\Column(name="adresscheck_shipping_adress", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckShippingAdress;

    /**
     * @ORM\Column(name="adresscheck_automatic_correction", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckAutomaticCorrection;

    /**
     * @ORM\Column(name="adresscheck_failure_handling", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckFailureHandling;

    /**
     * @ORM\Column(name="adresscheck_min_basket", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckMinBasket;

    /**
     * @ORM\Column(name="adresscheck_max_basket", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckMaxBasket;

    /**
     * @ORM\Column(name="adresscheck_lifetime", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $adresscheckLifetime;

    /**
     * @ORM\Column(name="adresscheck_failure_message", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $adresscheckFailureMessage;

    /**
     * @ORM\Column(name="map_person_check", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapPersonCheck;

    /**
     * @ORM\Column(name="map_know_pre_lastname", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapKnowPreLastname;

    /**
     * @ORM\Column(name="map_know_lastname", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapKnowLastname;

    /**
     * @ORM\Column(name="map_not_known_pre_lastname", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapNotKnowPreLastname;

    /**
     * @ORM\Column(name="map_multi_name_to_adress", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapMultiNameToAdress;

    /**
     * @ORM\Column(name="map_undeliverable", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapUndeliverable;

    /**
     * @ORM\Column(name="map_person_dead", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapPersonDead;

    /**
     * @ORM\Column(name="map_wrong_adress", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapWrongAdress;

    /**
     * @ORM\Column(name="map_address_check_not_possible", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapAddressCheckNotPossible;

    /**
     * @ORM\Column(name="map_address_okay_building_unknown", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapAddressOkayBuildingUnknown;

    /**
     * @ORM\Column(name="map_person_moved_address_unknown", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapPersonMovedAddressUnknown;

    /**
     * @ORM\Column(name="map_unknown_return_value", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapUnknownReturnValue;

    /**
     * @ORM\Column(name="consumerscore_active", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreActive;

    /**
     * @ORM\Column(name="consumerscore_live_mode", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreLiveMode;

    /**
     * 0 = before payment method selection
     * 1 = after payment selection
     *
     * @ORM\Column(name="consumerscore_check_moment", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreCheckMoment;

    /**
     * IH = infoscore hard characteristics
     * IA = infoscore all characteristics
     * IB = infoscore all characteristics + boniscore
     *
     * @ORM\Column(name="consumerscore_check_mode", type="string", length=4, nullable=false, unique=false)
     */
    private $consumerscoreCheckMode;

    /**
     * 0 = red
     * 1= yello
     * 2 = green
     *
     * @ORM\Column(name="consumerscore_default", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreDefault;

    /**
     * @ORM\Column(name="consumerscore_lifetime", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreLifetime;

    /**
     * @ORM\Column(name="consumerscore_min_basket", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreMinBasket;

    /**
     * @ORM\Column(name="consumerscore_max_basket", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreMaxBasket;

    /**
     * 0 = abort
     * 1 = continue
     *
     * @ORM\Column(name="consumerscore_failure_handling", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreFailureHandling;

    /**
     * @ORM\Column(name="consumerscore_note_message", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreNoteMessage;

    /**
     * @ORM\Column(name="consumerscore_note_active", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreNoteActive;

    /**
     * @ORM\Column(name="consumerscore_agreement_message", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreAgreementMessage;

    /**
     * @ORM\Column(name="consumerscore_agreement_active", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreAgreementActive;

    /**
     * @ORM\Column(name="consumerscore_abtest_value", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreAbtestValue;

    /**
     * @ORM\Column(name="consumerscore_abtest_active", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $consumerscoreAbtestActive;

    /**
     * @ORM\Column(name="payment_specific_data", type="array", nullable=true, unique=false)
     */
    private $paymentSpecificData;

    /**
     * @ORM\Column(name="state_appointed", type="integer", nullable=true, unique=false)
     */
    private $stateAppointed;

    /**
     * @ORM\Column(name="state_capture", type="integer", nullable=true, unique=false)
     */
    private $stateCapture;

    /**
     * @ORM\Column(name="state_paid", type="integer", nullable=true, unique=false)
     */
    private $statePaid;

    /**
     * @ORM\Column(name="state_underpaid", type="integer", nullable=true, unique=false)
     */
    private $stateUnderpaid;

    /**
     * @ORM\Column(name="state_cancelation", type="integer", nullable=true, unique=false)
     */
    private $stateCancelation;

    /**
     * @ORM\Column(name="state_refund", type="integer", nullable=true, unique=false)
     */
    private $stateRefund;

    /**
     * @ORM\Column(name="state_debit", type="integer", nullable=true, unique=false)
     */
    private $stateDebit;

    /**
     * @ORM\Column(name="state_reminder", type="integer", nullable=true, unique=false)
     */
    private $stateReminder;

    /**
     * @ORM\Column(name="state_vauthorization", type="integer", nullable=true, unique=false)
     */
    private $stateVauthorization;

    /**
     * @ORM\Column(name="state_vsettlement", type="integer", nullable=true, unique=false)
     */
    private $stateVsettlement;

    /**
     * @ORM\Column(name="state_transfer", type="integer", nullable=true, unique=false)
     */
    private $stateTransfer;

    /**
     * @ORM\Column(name="state_invoice", type="integer", nullable=true, unique=false)
     */
    private $stateInvoice;

    /**
     * @ORM\Column(name="check_cc", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $checkCc;

    /**
     * @ORM\Column(name="check_account", type="integer", nullable=true, unique=false)
     */
    private $checkAccount;

    /**
     * @ORM\Column(name="trans_appointed", type="text", nullable=true, unique=false)
     */
    private $transAppointed;

    /**
     * @ORM\Column(name="trans_capture", type="text", nullable=true, unique=false)
     */
    private $transCapture;

    /**
     * @ORM\Column(name="trans_paid", type="text", nullable=true, unique=false)
     */
    private $transPaid;

    /**
     * @ORM\Column(name="trans_underpaid", type="text", nullable=true, unique=false)
     */
    private $transUnderpaid;

    /**
     * @ORM\Column(name="trans_cancelation", type="text", nullable=true, unique=false)
     */
    private $transCancelation;

    /**
     * @ORM\Column(name="trans_refund", type="text", nullable=true, unique=false)
     */
    private $transRefund;

    /**
     * @ORM\Column(name="trans_debit", type="text", nullable=true, unique=false)
     */
    private $transDebit;

    /**
     * @ORM\Column(name="trans_reminder", type="text", nullable=true, unique=false)
     */
    private $transReminder;

    /**
     * @ORM\Column(name="trans_vauthorization", type="text", nullable=true, unique=false)
     */
    private $transVauthorization;

    /**
     * @ORM\Column(name="trans_vsettlement", type="text", nullable=true, unique=false)
     */
    private $transVsettlement;

    /**
     * @ORM\Column(name="trans_transfer", type="text", nullable=true, unique=false)
     */
    private $transTransfer;

    /**
     * @ORM\Column(name="trans_invoice", type="text", nullable=true, unique=false)
     */
    private $transInvoice;

    /**
     * @ORM\Column(name="show_accountnumber", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $showAccountnumber;

    /**
     * @ORM\Column(name="show_bic", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $showBic;

    /**
     * @ORM\Column(name="mandate_active", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mandateActive;

    /**
     * @ORM\Column(name="mandate_download_enabled", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $mandateDownloadEnabled;

    /**
     * @ORM\Column(name="klarna_store_id", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $klarnaStoreId;

    /**
     * @ORM\Column(name="save_terms", type="integer", nullable=true, unique=false)
     */
    private $saveTerms;

    /**
     * @ORM\Column(name="paypal_ecs_active", type="boolean", nullable=true, unique=false)
     */
    private $paypalEcsActive;

    /**
     * @ORM\Column(name="creditcard_min_valid", type="integer", nullable=true, unique=false)
     */
    private $creditcardMinValid;

    /**
     * @ORM\Column(name="adresscheck_billing_countries", type="string", length=255, unique=false, nullable=true)
     */
    private $adresscheckBillingCountries;

    /**
     * @ORM\Column(name="adresscheck_shipping_countries", type="string", length=255, unique=false, nullable=true)
     */
    private $adresscheckShippingCountries;

    /**
     * @ORM\Column(name="payolution_company_name", type="string", length=255, nullable=true, unique=false)
     */
    private $payolutionCompanyName;

    /**
     * @ORM\Column(name="payolution_b2bmode", type="boolean", nullable=true, unique=false)
     */
    private $payolutionB2bmode;

    /**
     * @ORM\Column(name="payolution_draft_user", type="string", length=255, nullable=true, unique=false)
     */
    private $payolutionDraftUser;

    /**
     * @ORM\Column(name="payolution_draft_password", type="string", length=255, nullable=true, unique=false)
     */
    private $payolutionDraftPassword;

    /**
     * @ORM\Column(name="show_sofort_iban_bic", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $showSofortIbanBic;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

    /**
     * @param $portalId
     */
    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getSubaccountId()
    {
        return $this->subaccountId;
    }

    /**
     * @param $subaccountId
     */
    public function setSubaccountId($subaccountId)
    {
        $this->subaccountId = $subaccountId;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getLiveMode()
    {
        return $this->liveMode;
    }

    /**
     * @param $liveMode
     */
    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode;
    }

    /**
     * @return mixed
     */
    public function getAuthorisationMethod()
    {
        return $this->authorisationMethod;
    }

    /**
     * @param $authorisationMethod
     */
    public function setAuthorisationMethod($authorisationMethod)
    {
        $this->authorisationMethod = $authorisationMethod;
    }

    /**
     * @return mixed
     */
    public function getSubmitBasket()
    {
        return $this->submitBasket;
    }

    /**
     * @param $submitBasket
     */
    public function setSubmitBasket($submitBasket)
    {
        $this->submitBasket = $submitBasket;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckActive()
    {
        return $this->adresscheckActive;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param $adresscheckActive
     */
    public function setAdresscheckActive($adresscheckActive)
    {
        $this->adresscheckActive = $adresscheckActive;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckLiveMode()
    {
        return $this->adresscheckLiveMode;
    }

    /**
     * @param $adresscheckLiveMode
     */
    public function setAdresscheckLiveMode($adresscheckLiveMode)
    {
        $this->adresscheckLiveMode = $adresscheckLiveMode;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckBillingAdress()
    {
        return $this->adresscheckBillingAdress;
    }

    /**
     * @param $adresscheckBillingAdress
     */
    public function setAdresscheckBillingAdress($adresscheckBillingAdress)
    {
        $this->adresscheckBillingAdress = $adresscheckBillingAdress;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckShippingAdress()
    {
        return $this->adresscheckShippingAdress;
    }

    /**
     * @param $adresscheckShippingAdress
     */
    public function setAdresscheckShippingAdress($adresscheckShippingAdress)
    {
        $this->adresscheckShippingAdress = $adresscheckShippingAdress;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckAutomaticCorrection()
    {
        return $this->adresscheckAutomaticCorrection;
    }

    /**
     * @param $adresscheckAutomaticCorrection
     */
    public function setAdresscheckAutomaticCorrection($adresscheckAutomaticCorrection)
    {
        $this->adresscheckAutomaticCorrection = $adresscheckAutomaticCorrection;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckFailureHandling()
    {
        return $this->adresscheckFailureHandling;
    }

    /**
     * @param $adresscheckFailureHandling
     */
    public function setAdresscheckFailureHandling($adresscheckFailureHandling)
    {
        $this->adresscheckFailureHandling = $adresscheckFailureHandling;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckMinBasket()
    {
        return $this->adresscheckMinBasket;
    }

    /**
     * @param $adresscheckMinBasket
     */
    public function setAdresscheckMinBasket($adresscheckMinBasket)
    {
        $this->adresscheckMinBasket = $adresscheckMinBasket;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckMaxBasket()
    {
        return $this->adresscheckMaxBasket;
    }

    /**
     * @param $adresscheckMaxBasket
     */
    public function setAdresscheckMaxBasket($adresscheckMaxBasket)
    {
        $this->adresscheckMaxBasket = $adresscheckMaxBasket;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckLifetime()
    {
        return $this->adresscheckLifetime;
    }

    /**
     * @param $adresscheckLifetime
     */
    public function setAdresscheckLifetime($adresscheckLifetime)
    {
        $this->adresscheckLifetime = $adresscheckLifetime;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckFailureMessage()
    {
        return $this->adresscheckFailureMessage;
    }

    /**
     * @param $adresscheckFailureMessage
     */
    public function setAdresscheckFailureMessage($adresscheckFailureMessage)
    {
        $this->adresscheckFailureMessage = $adresscheckFailureMessage;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreActive()
    {
        return $this->consumerscoreActive;
    }

    /**
     * @param $consumerscoreActive
     */
    public function setConsumerscoreActive($consumerscoreActive)
    {
        $this->consumerscoreActive = $consumerscoreActive;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreLiveMode()
    {
        return $this->consumerscoreLiveMode;
    }

    /**
     * @param $consumerscoreLiveMode
     */
    public function setConsumerscoreLiveMode($consumerscoreLiveMode)
    {
        $this->consumerscoreLiveMode = $consumerscoreLiveMode;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreCheckMoment()
    {
        return $this->consumerscoreCheckMoment;
    }

    /**
     * @param $consumerscoreCheckMoment
     */
    public function setConsumerscoreCheckMoment($consumerscoreCheckMoment)
    {
        $this->consumerscoreCheckMoment = $consumerscoreCheckMoment;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreCheckMode()
    {
        return $this->consumerscoreCheckMode;
    }

    /**
     * @param $consumerscoreCheckMode
     */
    public function setConsumerscoreCheckMode($consumerscoreCheckMode)
    {
        $this->consumerscoreCheckMode = $consumerscoreCheckMode;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreDefault()
    {
        return $this->consumerscoreDefault;
    }

    /**
     * @param $consumerscoreDefault
     */
    public function setConsumerscoreDefault($consumerscoreDefault)
    {
        $this->consumerscoreDefault = $consumerscoreDefault;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreLifetime()
    {
        return $this->consumerscoreLifetime;
    }

    /**
     * @param $consumerscoreLifetime
     */
    public function setConsumerscoreLifetime($consumerscoreLifetime)
    {
        $this->consumerscoreLifetime = $consumerscoreLifetime;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreMinBasket()
    {
        return $this->consumerscoreMinBasket;
    }

    /**
     * @param $consumerscoreMinBasket
     */
    public function setConsumerscoreMinBasket($consumerscoreMinBasket)
    {
        $this->consumerscoreMinBasket = $consumerscoreMinBasket;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreMaxBasket()
    {
        return $this->consumerscoreMaxBasket;
    }

    /**
     * @param $consumerscoreMaxBasket
     */
    public function setConsumerscoreMaxBasket($consumerscoreMaxBasket)
    {
        $this->consumerscoreMaxBasket = $consumerscoreMaxBasket;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreFailureHandling()
    {
        return $this->consumerscoreFailureHandling;
    }

    /**
     * @param $consumerscoreFailureHandling
     */
    public function setConsumerscoreFailureHandling($consumerscoreFailureHandling)
    {
        $this->consumerscoreFailureHandling = $consumerscoreFailureHandling;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreNoteMessage()
    {
        return $this->consumerscoreNoteMessage;
    }

    /**
     * @param $consumerscoreNoteMessage
     */
    public function setConsumerscoreNoteMessage($consumerscoreNoteMessage)
    {
        $this->consumerscoreNoteMessage = $consumerscoreNoteMessage;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreNoteActive()
    {
        return $this->consumerscoreNoteActive;
    }

    /**
     * @param $consumerscoreNoteActive
     */
    public function setConsumerscoreNoteActive($consumerscoreNoteActive)
    {
        $this->consumerscoreNoteActive = $consumerscoreNoteActive;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreAgreementMessage()
    {
        return $this->consumerscoreAgreementMessage;
    }

    /**
     * @param $consumerscoreAgreementMessage
     */
    public function setConsumerscoreAgreementMessage($consumerscoreAgreementMessage)
    {
        $this->consumerscoreAgreementMessage = $consumerscoreAgreementMessage;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreAgreementActive()
    {
        return $this->consumerscoreAgreementActive;
    }

    /**
     * @param $consumerscoreAgreementActive
     */
    public function setConsumerscoreAgreementActive($consumerscoreAgreementActive)
    {
        $this->consumerscoreAgreementActive = $consumerscoreAgreementActive;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreAbtestValue()
    {
        return $this->consumerscoreAbtestValue;
    }

    /**
     * @param $consumerscoreAbtestValue
     */
    public function setConsumerscoreAbtestValue($consumerscoreAbtestValue)
    {
        $this->consumerscoreAbtestValue = $consumerscoreAbtestValue;
    }

    /**
     * @return mixed
     */
    public function getConsumerscoreAbtestActive()
    {
        return $this->consumerscoreAbtestActive;
    }

    /**
     * @param $consumerscoreAbtestActive
     */
    public function setConsumerscoreAbtestActive($consumerscoreAbtestActive)
    {
        $this->consumerscoreAbtestActive = $consumerscoreAbtestActive;
    }

    /**
     * @return mixed
     */
    public function getPaymentSpecificData()
    {
        return $this->paymentSpecificData;
    }

    /**
     * @param $paymentSpecificData
     */
    public function setPaymentSpecificData($paymentSpecificData)
    {
        $this->paymentSpecificData = $paymentSpecificData;
    }

    /**
     * @return mixed
     */
    public function getStateAppointed()
    {
        return $this->stateAppointed;
    }

    /**
     * @param $stateAppointed
     */
    public function setStateAppointed($stateAppointed)
    {
        $this->stateAppointed = $stateAppointed;
    }

    /**
     * @return mixed
     */
    public function getStateCapture()
    {
        return $this->stateCapture;
    }

    /**
     * @param $stateCapture
     */
    public function setStateCapture($stateCapture)
    {
        $this->stateCapture = $stateCapture;
    }

    /**
     * @return mixed
     */
    public function getStatePaid()
    {
        return $this->statePaid;
    }

    /**
     * @param $statePaid
     */
    public function setStatePaid($statePaid)
    {
        $this->statePaid = $statePaid;
    }

    /**
     * @return mixed
     */
    public function getStateUnderpaid()
    {
        return $this->stateUnderpaid;
    }

    /**
     * @param $stateUnderpaid
     */
    public function setStateUnderpaid($stateUnderpaid)
    {
        $this->stateUnderpaid = $stateUnderpaid;
    }

    /**
     * @return mixed
     */
    public function getStateCancelation()
    {
        return $this->stateCancelation;
    }

    /**
     * @param $stateCancelation
     */
    public function setStateCancelation($stateCancelation)
    {
        $this->stateCancelation = $stateCancelation;
    }

    /**
     * @return mixed
     */
    public function getStateRefund()
    {
        return $this->stateRefund;
    }

    /**
     * @param $stateRefund
     */
    public function setStateRefund($stateRefund)
    {
        $this->stateRefund = $stateRefund;
    }

    /**
     * @return mixed
     */
    public function getStateDebit()
    {
        return $this->stateDebit;
    }

    /**
     * @param $stateDebit
     */
    public function setStateDebit($stateDebit)
    {
        $this->stateDebit = $stateDebit;
    }

    /**
     * @return mixed
     */
    public function getStateReminder()
    {
        return $this->stateReminder;
    }

    /**
     * @param $stateReminder
     */
    public function setStateReminder($stateReminder)
    {
        $this->stateReminder = $stateReminder;
    }

    /**
     * @return mixed
     */
    public function getStateVauthorization()
    {
        return $this->stateVauthorization;
    }

    /**
     * @param $stateVauthorization
     */
    public function setStateVauthorization($stateVauthorization)
    {
        $this->stateVauthorization = $stateVauthorization;
    }

    /**
     * @return mixed
     */
    public function getStateVsettlement()
    {
        return $this->stateVsettlement;
    }

    /**
     * @param $stateVsettlement
     */
    public function setStateVsettlement($stateVsettlement)
    {
        $this->stateVsettlement = $stateVsettlement;
    }

    /**
     * @return mixed
     */
    public function getStateTransfer()
    {
        return $this->stateTransfer;
    }

    /**
     * @param $stateTransfer
     */
    public function setStateTransfer($stateTransfer)
    {
        $this->stateTransfer = $stateTransfer;
    }

    /**
     * @return mixed
     */
    public function getStateInvoice()
    {
        return $this->stateInvoice;
    }

    /**
     * @param $stateInvoice
     */
    public function setStateInvoice($stateInvoice)
    {
        $this->stateInvoice = $stateInvoice;
    }

    /**
     * @return mixed
     */
    public function getMapPersonCheck()
    {
        return $this->mapPersonCheck;
    }

    /**
     * @param $mapPersonCheck
     */
    public function setMapPersonCheck($mapPersonCheck)
    {
        $this->mapPersonCheck = $mapPersonCheck;
    }

    /**
     * @return mixed
     */
    public function getMapKnowPreLastname()
    {
        return $this->mapKnowPreLastname;
    }

    /**
     * @param $mapKnowPreLastname
     */
    public function setMapKnowPreLastname($mapKnowPreLastname)
    {
        $this->mapKnowPreLastname = $mapKnowPreLastname;
    }

    /**
     * @return mixed
     */
    public function getMapKnowLastname()
    {
        return $this->mapKnowLastname;
    }

    /**
     * @param $mapKnowLastname
     */
    public function setMapKnowLastname($mapKnowLastname)
    {
        $this->mapKnowLastname = $mapKnowLastname;
    }

    /**
     * @return mixed
     */
    public function getMapNotKnowPreLastname()
    {
        return $this->mapNotKnowPreLastname;
    }

    /**
     * @param $mapNotKnowPreLastname
     */
    public function setMapNotKnowPreLastname($mapNotKnowPreLastname)
    {
        $this->mapNotKnowPreLastname = $mapNotKnowPreLastname;
    }

    /**
     * @return mixed
     */
    public function getMapMultiNameToAdress()
    {
        return $this->mapMultiNameToAdress;
    }

    /**
     * @param $mapMultiNameToAdress
     */
    public function setMapMultiNameToAdress($mapMultiNameToAdress)
    {
        $this->mapMultiNameToAdress = $mapMultiNameToAdress;
    }

    /**
     * @return mixed
     */
    public function getMapUndeliverable()
    {
        return $this->mapUndeliverable;
    }

    /**
     * @param $mapUndeliverable
     */
    public function setMapUndeliverable($mapUndeliverable)
    {
        $this->mapUndeliverable = $mapUndeliverable;
    }

    /**
     * @return mixed
     */
    public function getMapPersonDead()
    {
        return $this->mapPersonDead;
    }

    /**
     * @param $mapPersonDead
     */
    public function setMapPersonDead($mapPersonDead)
    {
        $this->mapPersonDead = $mapPersonDead;
    }

    /**
     * @return mixed
     */
    public function getMapWrongAdress()
    {
        return $this->mapWrongAdress;
    }

    /**
     * @param $mapWrongAdress
     */
    public function setMapWrongAdress($mapWrongAdress)
    {
        $this->mapWrongAdress = $mapWrongAdress;
    }

    /**
     * @return mixed
     */
    public function getMapAddressCheckNotPossible()
    {
        return $this->mapAddressCheckNotPossible;
    }

    /**
     * @param $mapAddressCheckNotPossible
     */
    public function setMapAddressCheckNotPossible($mapAddressCheckNotPossible)
    {
        $this->mapAddressCheckNotPossible = $mapAddressCheckNotPossible;
    }

    /**
     * @return mixed
     */
    public function getMapAddressOkayBuildingUnknown()
    {
        return $this->mapAddressOkayBuildingUnknown;
    }

    /**
     * @param $mapAddressOkayBuildingUnknown
     */
    public function setMapAddressOkayBuildingUnknown($mapAddressOkayBuildingUnknown)
    {
        $this->mapAddressOkayBuildingUnknown = $mapAddressOkayBuildingUnknown;
    }

    /**
     * @return mixed
     */
    public function getMapPersonMovedAddressUnknown()
    {
        return $this->mapPersonMovedAddressUnknown;
    }

    /**
     * @param $mapPersonMovedAddressUnknown
     */
    public function setMapPersonMovedAddressUnknown($mapPersonMovedAddressUnknown)
    {
        $this->mapPersonMovedAddressUnknown = $mapPersonMovedAddressUnknown;
    }

    /**
     * @return mixed
     */
    public function getMapUnknownReturnValue()
    {
        return $this->mapUnknownReturnValue;
    }

    /**
     * @param $mapUnknownReturnValue
     */
    public function setMapUnknownReturnValue($mapUnknownReturnValue)
    {
        $this->mapUnknownReturnValue = $mapUnknownReturnValue;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        foreach ($data as $property => $value) {
            $this->$property = $value;
        }

        unset($this->id);
    }

    /**
     * @return mixed
     */
    public function getCheckCc()
    {
        return $this->checkCc;
    }

    /**
     * @param $checkCc
     */
    public function setCheckCc($checkCc)
    {
        $this->checkCc = $checkCc;
    }

    /**
     * @return mixed
     */
    public function getCheckAccount()
    {
        return $this->checkAccount;
    }

    /**
     * @param $checkAccount
     */
    public function setCheckAccount($checkAccount)
    {
        $this->checkAccount = $checkAccount;
    }

    /**
     * @return mixed
     */
    public function getTransAppointed()
    {
        return $this->transAppointed;
    }

    /**
     * @param $transAppointed
     */
    public function setTransAppointed($transAppointed)
    {
        $this->transAppointed = $transAppointed;
    }

    /**
     * @return mixed
     */
    public function getTransCapture()
    {
        return $this->transCapture;
    }

    /**
     * @param $transCapture
     */
    public function setTransCapture($transCapture)
    {
        $this->transCapture = $transCapture;
    }

    /**
     * @return mixed
     */
    public function getTransPaid()
    {
        return $this->transPaid;
    }

    /**
     * @param $transPaid
     */
    public function setTransPaid($transPaid)
    {
        $this->transPaid = $transPaid;
    }

    /**
     * @return mixed
     */
    public function getTransUnderpaid()
    {
        return $this->transUnderpaid;
    }

    /**
     * @param $transUnderpaid
     */
    public function setTransUnderpaid($transUnderpaid)
    {
        $this->transUnderpaid = $transUnderpaid;
    }

    /**
     * @return mixed
     */
    public function getTransCancelation()
    {
        return $this->transCancelation;
    }

    /**
     * @param $transCancelation
     */
    public function setTransCancelation($transCancelation)
    {
        $this->transCancelation = $transCancelation;
    }

    /**
     * @return mixed
     */
    public function getTransRefund()
    {
        return $this->transRefund;
    }

    /**
     * @param $transRefund
     */
    public function setTransRefund($transRefund)
    {
        $this->transRefund = $transRefund;
    }

    /**
     * @return mixed
     */
    public function getTransDebit()
    {
        return $this->transDebit;
    }

    /**
     * @param $transDebit
     */
    public function setTransDebit($transDebit)
    {
        $this->transDebit = $transDebit;
    }

    /**
     * @return mixed
     */
    public function getTransReminder()
    {
        return $this->transReminder;
    }

    /**
     * @param $transReminder
     */
    public function setTransReminder($transReminder)
    {
        $this->transReminder = $transReminder;
    }

    /**
     * @return mixed
     */
    public function getTransVauthorization()
    {
        return $this->transVauthorization;
    }

    /**
     * @param $transVauthorization
     */
    public function setTransVauthorization($transVauthorization)
    {
        $this->transVauthorization = $transVauthorization;
    }

    /**
     * @return mixed
     */
    public function getTransVsettlement()
    {
        return $this->transVsettlement;
    }

    /**
     * @param $transVsettlement
     */
    public function setTransVsettlement($transVsettlement)
    {
        $this->transVsettlement = $transVsettlement;
    }

    /**
     * @return mixed
     */
    public function getTransTransfer()
    {
        return $this->transTransfer;
    }

    /**
     * @param $transTransfer
     */
    public function setTransTransfer($transTransfer)
    {
        $this->transTransfer = $transTransfer;
    }

    /**
     * @return mixed
     */
    public function getTransInvoice()
    {
        return $this->transInvoice;
    }

    /**
     * @param $transInvoice
     */
    public function setTransInvoice($transInvoice)
    {
        $this->transInvoice = $transInvoice;
    }

    /**
     * @return mixed
     */
    public function getShowAccountnumber()
    {
        return $this->showAccountnumber;
    }

    /**
     * @param $showAccountnumber
     */
    public function setShowAccountnumber($showAccountnumber)
    {
        $this->showAccountnumber = $showAccountnumber;
    }

    /**
     * @return mixed
     */
    public function getShowBic()
    {
        return $this->showBic;
    }

    /**
     * @param $showBic
     */
    public function setShowBic($showBic)
    {
        $this->showBic = $showBic;
    }

    /**
     * @return mixed
     */
    public function getMandateActive()
    {
        return $this->mandateActive;
    }

    /**
     * @param $mandateActive
     */
    public function setMandateActive($mandateActive)
    {
        $this->mandateActive = $mandateActive;
    }

    /**
     * @return mixed
     */
    public function getMandateDownloadEnabled()
    {
        return $this->mandateDownloadEnabled;
    }

    /**
     * @param $mandateDownloadEnabled
     */
    public function setMandateDownloadEnabled($mandateDownloadEnabled)
    {
        $this->mandateDownloadEnabled = $mandateDownloadEnabled;
    }

    /**
     * @return mixed
     */
    public function getKlarnaStoreId()
    {
        return $this->klarnaStoreId;
    }

    /**
     * @param $klarnaStoreId
     */
    public function setKlarnaStoreId($klarnaStoreId)
    {
        $this->klarnaStoreId = $klarnaStoreId;
    }

    /**
     * @return mixed
     */
    public function getSaveTerms()
    {
        return $this->saveTerms;
    }

    /**
     * @param $saveTerms
     */
    public function setSaveTerms($saveTerms)
    {
        $this->saveTerms = $saveTerms;
    }

    /**
     * @return mixed
     */
    public function getPaypalEcsActive()
    {
        return $this->paypalEcsActive;
    }

    /**
     * @param $paypalEcsActive
     */
    public function setPaypalEcsActive($paypalEcsActive)
    {
        $this->paypalEcsActive = $paypalEcsActive;
    }

    /**
     * @return mixed
     */
    public function getCreditcardMinValid()
    {
        return $this->creditcardMinValid;
    }

    /**
     * @param $creditcardMinValid
     */
    public function setCreditcardMinValid($creditcardMinValid)
    {
        $this->creditcardMinValid = $creditcardMinValid;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckBillingCountries()
    {
        return $this->adresscheckBillingCountries;
    }

    /**
     * @param $adresscheckBillingCountries
     */
    public function setAdresscheckBillingCountries($adresscheckBillingCountries)
    {
        $this->adresscheckBillingCountries = $adresscheckBillingCountries;
    }

    /**
     * @return mixed
     */
    public function getAdresscheckShippingCountries()
    {
        return $this->adresscheckShippingCountries;
    }

    /**
     * @param $adresscheckShippingCountries
     */
    public function setAdresscheckShippingCountries($adresscheckShippingCountries)
    {
        $this->adresscheckShippingCountries = $adresscheckShippingCountries;
    }

    /**
     * @return mixed
     */
    public function getPayolutionCompanyName()
    {
        return $this->payolutionCompanyName;
    }

    /**
     * @param $payolutionCompanyName
     */
    public function setPayolutionCompanyName($payolutionCompanyName)
    {
        $this->payolutionCompanyName = $payolutionCompanyName;
    }

    /**
     * @return mixed
     */
    public function getPayolutionB2bmode()
    {
        return $this->payolutionB2bmode;
    }

    /**
     * @param $payolutionB2bmode
     */
    public function setPayolutionB2bMode($payolutionB2bmode)
    {
        $this->payolutionB2bmode = $payolutionB2bmode;
    }

    /**
     * @return mixed
     */
    public function getPayolutionDraftUser()
    {
        return $this->payolutionDraftUser;
    }

    /**
     * @param $payolutionDraftUser
     */
    public function setPayolutionDraftUser($payolutionDraftUser)
    {
        $this->payolutionDraftUser = $payolutionDraftUser;
    }

    /**
     * @return mixed
     */
    public function getPayolutionDraftPassword()
    {
        return $this->payolutionDraftPassword;
    }

    /**
     * @param $payolutionDraftPassword
     */
    public function setPayolutionDraftPassword($payolutionDraftPassword)
    {
        $this->payolutionDraftPassword = $payolutionDraftPassword;
    }

    /**
     * @return mixed
     */
    public function getShowSofortIbanBic()
    {
        return $this->showSofortIbanBic;
    }

    /**
     * @param $showSofortIbanBic
     */
    public function setShowSofortIbanBic($showSofortIbanBic)
    {
        $this->showSofortIbanBic = $showSofortIbanBic;
    }
}
