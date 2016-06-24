<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneConfig;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;

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
  
  public function fromArray($array = array())
  {
    foreach ($array as $property => $value)
    {
      $this->$property = $value;
    }
  }

  public function getId()
  {
    return $this->id;
  }

  public function getPaymentId()
  {
    return $this->paymentId;
  }

  public function setPaymentId($paymentId)
  {
    $this->paymentId = $paymentId;
  }

  public function getMerchantId()
  {
    return $this->merchantId;
  }

  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }

  public function getPortalId()
  {
    return $this->portalId;
  }

  public function setPortalId($portalId)
  {
    $this->portalId = $portalId;
  }

  public function getSubaccountId()
  {
    return $this->subaccountId;
  }

  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }

  public function getApiKey()
  {
    return $this->apiKey;
  }

  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }

  public function getLiveMode()
  {
    return $this->liveMode;
  }

  public function setLiveMode($liveMode)
  {
    $this->liveMode = $liveMode;
  }

  public function getAuthorisationMethod()
  {
    return $this->authorisationMethod;
  }

  public function setAuthorisationMethod($authorisationMethod)
  {
    $this->authorisationMethod = $authorisationMethod;
  }

  public function getSubmitBasket()
  {
    return $this->submitBasket;
  }

  public function setSubmitBasket($submitBasket)
  {
    $this->submitBasket = $submitBasket;
  }

  public function getAdresscheckActive()
  {
    return $this->adresscheckActive;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function setAdresscheckActive($adresscheckActive)
  {
    $this->adresscheckActive = $adresscheckActive;
  }

  public function getAdresscheckLiveMode()
  {
    return $this->adresscheckLiveMode;
  }

  public function setAdresscheckLiveMode($adresscheckLiveMode)
  {
    $this->adresscheckLiveMode = $adresscheckLiveMode;
  }

  public function getAdresscheckBillingAdress()
  {
    return $this->adresscheckBillingAdress;
  }

  public function setAdresscheckBillingAdress($adresscheckBillingAdress)
  {
    $this->adresscheckBillingAdress = $adresscheckBillingAdress;
  }

  public function getAdresscheckShippingAdress()
  {
    return $this->adresscheckShippingAdress;
  }

  public function setAdresscheckShippingAdress($adresscheckShippingAdress)
  {
    $this->adresscheckShippingAdress = $adresscheckShippingAdress;
  }

  public function getAdresscheckAutomaticCorrection()
  {
    return $this->adresscheckAutomaticCorrection;
  }

  public function setAdresscheckAutomaticCorrection($adresscheckAutomaticCorrection)
  {
    $this->adresscheckAutomaticCorrection = $adresscheckAutomaticCorrection;
  }

  public function getAdresscheckFailureHandling()
  {
    return $this->adresscheckFailureHandling;
  }

  public function setAdresscheckFailureHandling($adresscheckFailureHandling)
  {
    $this->adresscheckFailureHandling = $adresscheckFailureHandling;
  }

  public function getAdresscheckMinBasket()
  {
    return $this->adresscheckMinBasket;
  }

  public function setAdresscheckMinBasket($adresscheckMinBasket)
  {
    $this->adresscheckMinBasket = $adresscheckMinBasket;
  }

  public function getAdresscheckMaxBasket()
  {
    return $this->adresscheckMaxBasket;
  }

  public function setAdresscheckMaxBasket($adresscheckMaxBasket)
  {
    $this->adresscheckMaxBasket = $adresscheckMaxBasket;
  }

  public function getAdresscheckLifetime()
  {
    return $this->adresscheckLifetime;
  }

  public function setAdresscheckLifetime($adresscheckLifetime)
  {
    $this->adresscheckLifetime = $adresscheckLifetime;
  }

  public function getAdresscheckFailureMessage()
  {
    return $this->adresscheckFailureMessage;
  }

  public function setAdresscheckFailureMessage($adresscheckFailureMessage)
  {
    $this->adresscheckFailureMessage = $adresscheckFailureMessage;
  }

  public function getConsumerscoreActive()
  {
    return $this->consumerscoreActive;
  }

  public function setConsumerscoreActive($consumerscoreActive)
  {
    $this->consumerscoreActive = $consumerscoreActive;
  }

  public function getConsumerscoreLiveMode()
  {
    return $this->consumerscoreLiveMode;
  }

  public function setConsumerscoreLiveMode($consumerscoreLiveMode)
  {
    $this->consumerscoreLiveMode = $consumerscoreLiveMode;
  }

  public function getConsumerscoreCheckMoment()
  {
    return $this->consumerscoreCheckMoment;
  }

  public function setConsumerscoreCheckMoment($consumerscoreCheckMoment)
  {
    $this->consumerscoreCheckMoment = $consumerscoreCheckMoment;
  }

  public function getConsumerscoreCheckMode()
  {
    return $this->consumerscoreCheckMode;
  }

  public function setConsumerscoreCheckMode($consumerscoreCheckMode)
  {
    $this->consumerscoreCheckMode = $consumerscoreCheckMode;
  }

  public function getConsumerscoreDefault()
  {
    return $this->consumerscoreDefault;
  }

  public function setConsumerscoreDefault($consumerscoreDefault)
  {
    $this->consumerscoreDefault = $consumerscoreDefault;
  }

  public function getConsumerscoreLifetime()
  {
    return $this->consumerscoreLifetime;
  }

  public function setConsumerscoreLifetime($consumerscoreLifetime)
  {
    $this->consumerscoreLifetime = $consumerscoreLifetime;
  }

  public function getConsumerscoreMinBasket()
  {
    return $this->consumerscoreMinBasket;
  }

  public function setConsumerscoreMinBasket($consumerscoreMinBasket)
  {
    $this->consumerscoreMinBasket = $consumerscoreMinBasket;
  }

  public function getConsumerscoreMaxBasket()
  {
    return $this->consumerscoreMaxBasket;
  }

  public function setConsumerscoreMaxBasket($consumerscoreMaxBasket)
  {
    $this->consumerscoreMaxBasket = $consumerscoreMaxBasket;
  }

  public function getConsumerscoreFailureHandling()
  {
    return $this->consumerscoreFailureHandling;
  }

  public function setConsumerscoreFailureHandling($consumerscoreFailureHandling)
  {
    $this->consumerscoreFailureHandling = $consumerscoreFailureHandling;
  }

  public function getConsumerscoreNoteMessage()
  {
    return $this->consumerscoreNoteMessage;
  }

  public function setConsumerscoreNoteMessage($consumerscoreNoteMessage)
  {
    $this->consumerscoreNoteMessage = $consumerscoreNoteMessage;
  }

  public function getConsumerscoreNoteActive()
  {
    return $this->consumerscoreNoteActive;
  }

  public function setConsumerscoreNoteActive($consumerscoreNoteActive)
  {
    $this->consumerscoreNoteActive = $consumerscoreNoteActive;
  }

  public function getConsumerscoreAgreementMessage()
  {
    return $this->consumerscoreAgreementMessage;
  }

  public function setConsumerscoreAgreementMessage($consumerscoreAgreementMessage)
  {
    $this->consumerscoreAgreementMessage = $consumerscoreAgreementMessage;
  }

  public function getConsumerscoreAgreementActive()
  {
    return $this->consumerscoreAgreementActive;
  }

  public function setConsumerscoreAgreementActive($consumerscoreAgreementActive)
  {
    $this->consumerscoreAgreementActive = $consumerscoreAgreementActive;
  }

  public function getConsumerscoreAbtestValue()
  {
    return $this->consumerscoreAbtestValue;
  }

  public function setConsumerscoreAbtestValue($consumerscoreAbtestValue)
  {
    $this->consumerscoreAbtestValue = $consumerscoreAbtestValue;
  }

  public function getConsumerscoreAbtestActive()
  {
    return $this->consumerscoreAbtestActive;
  }

  public function setConsumerscoreAbtestActive($consumerscoreAbtestActive)
  {
    $this->consumerscoreAbtestActive = $consumerscoreAbtestActive;
  }

  public function getPaymentSpecificData()
  {
    return $this->paymentSpecificData;
  }

  public function setPaymentSpecificData($paymentSpecificData)
  {
    $this->paymentSpecificData = $paymentSpecificData;
  }

  public function getStateAppointed()
  {
    return $this->stateAppointed;
  }

  public function setStateAppointed($stateAppointed)
  {
    $this->stateAppointed = $stateAppointed;
  }

  public function getStateCapture()
  {
    return $this->stateCapture;
  }

  public function setStateCapture($stateCapture)
  {
    $this->stateCapture = $stateCapture;
  }

  public function getStatePaid()
  {
    return $this->statePaid;
  }

  public function setStatePaid($statePaid)
  {
    $this->statePaid = $statePaid;
  }

  public function getStateUnderpaid()
  {
    return $this->stateUnderpaid;
  }

  public function setStateUnderpaid($stateUnderpaid)
  {
    $this->stateUnderpaid = $stateUnderpaid;
  }

  public function getStateCancelation()
  {
    return $this->stateCancelation;
  }

  public function setStateCancelation($stateCancelation)
  {
    $this->stateCancelation = $stateCancelation;
  }

  public function getStateRefund()
  {
    return $this->stateRefund;
  }

  public function setStateRefund($stateRefund)
  {
    $this->stateRefund = $stateRefund;
  }

  public function getStateDebit()
  {
    return $this->stateDebit;
  }

  public function setStateDebit($stateDebit)
  {
    $this->stateDebit = $stateDebit;
  }

  public function getStateReminder()
  {
    return $this->stateReminder;
  }

  public function setStateReminder($stateReminder)
  {
    $this->stateReminder = $stateReminder;
  }

  public function getStateVauthorization()
  {
    return $this->stateVauthorization;
  }

  public function setStateVauthorization($stateVauthorization)
  {
    $this->stateVauthorization = $stateVauthorization;
  }

  public function getStateVsettlement()
  {
    return $this->stateVsettlement;
  }

  public function setStateVsettlement($stateVsettlement)
  {
    $this->stateVsettlement = $stateVsettlement;
  }

  public function getStateTransfer()
  {
    return $this->stateTransfer;
  }

  public function setStateTransfer($stateTransfer)
  {
    $this->stateTransfer = $stateTransfer;
  }

  public function getStateInvoice()
  {
    return $this->stateInvoice;
  }

  public function setStateInvoice($stateInvoice)
  {
    $this->stateInvoice = $stateInvoice;
  }

  public function getMapPersonCheck()
  {
    return $this->mapPersonCheck;
  }

  public function setMapPersonCheck($mapPersonCheck)
  {
    $this->mapPersonCheck = $mapPersonCheck;
  }

  public function getMapKnowPreLastname()
  {
    return $this->mapKnowPreLastname;
  }

  public function setMapKnowPreLastname($mapKnowPreLastname)
  {
    $this->mapKnowPreLastname = $mapKnowPreLastname;
  }

  public function getMapKnowLastname()
  {
    return $this->mapKnowLastname;
  }

  public function setMapKnowLastname($mapKnowLastname)
  {
    $this->mapKnowLastname = $mapKnowLastname;
  }

  public function getMapNotKnowPreLastname()
  {
    return $this->mapNotKnowPreLastname;
  }

  public function setMapNotKnowPreLastname($mapNotKnowPreLastname)
  {
    $this->mapNotKnowPreLastname = $mapNotKnowPreLastname;
  }

  public function getMapMultiNameToAdress()
  {
    return $this->mapMultiNameToAdress;
  }

  public function setMapMultiNameToAdress($mapMultiNameToAdress)
  {
    $this->mapMultiNameToAdress = $mapMultiNameToAdress;
  }

  public function getMapUndeliverable()
  {
    return $this->mapUndeliverable;
  }

  public function setMapUndeliverable($mapUndeliverable)
  {
    $this->mapUndeliverable = $mapUndeliverable;
  }

  public function getMapPersonDead()
  {
    return $this->mapPersonDead;
  }

  public function setMapPersonDead($mapPersonDead)
  {
    $this->mapPersonDead = $mapPersonDead;
  }

  public function getMapWrongAdress()
  {
    return $this->mapWrongAdress;
  }

  public function setMapWrongAdress($mapWrongAdress)
  {
    $this->mapWrongAdress = $mapWrongAdress;
  }

  public function setData($data)
  {
    foreach ($data as $property => $value)
    {
      $this->$property = $value;
    }

    unset($this->id);
  }

  public function getCheckCc()
  {
    return $this->checkCc;
  }

  public function setCheckCc($checkCc)
  {
    $this->checkCc = $checkCc;
  }

  public function getCheckAccount()
  {
    return $this->checkAccount;
  }

  public function setCheckAccount($checkAccount)
  {
    $this->checkAccount = $checkAccount;
  }

  public function getTransAppointed()
  {
    return $this->transAppointed;
  }

  public function setTransAppointed($transAppointed)
  {
    $this->transAppointed = $transAppointed;
  }

  public function getTransCapture()
  {
    return $this->transCapture;
  }

  public function setTransCapture($transCapture)
  {
    $this->transCapture = $transCapture;
  }

  public function getTransPaid()
  {
    return $this->transPaid;
  }

  public function setTransPaid($transPaid)
  {
    $this->transPaid = $transPaid;
  }

  public function getTransUnderpaid()
  {
    return $this->transUnderpaid;
  }

  public function setTransUnderpaid($transUnderpaid)
  {
    $this->transUnderpaid = $transUnderpaid;
  }

  public function getTransCancelation()
  {
    return $this->transCancelation;
  }

  public function setTransCancelation($transCancelation)
  {
    $this->transCancelation = $transCancelation;
  }

  public function getTransRefund()
  {
    return $this->transRefund;
  }

  public function setTransRefund($transRefund)
  {
    $this->transRefund = $transRefund;
  }

  public function getTransDebit()
  {
    return $this->transDebit;
  }

  public function setTransDebit($transDebit)
  {
    $this->transDebit = $transDebit;
  }

  public function getTransReminder()
  {
    return $this->transReminder;
  }

  public function setTransReminder($transReminder)
  {
    $this->transReminder = $transReminder;
  }

  public function getTransVauthorization()
  {
    return $this->transVauthorization;
  }

  public function setTransVauthorization($transVauthorization)
  {
    $this->transVauthorization = $transVauthorization;
  }

  public function getTransVsettlement()
  {
    return $this->transVsettlement;
  }

  public function setTransVsettlement($transVsettlement)
  {
    $this->transVsettlement = $transVsettlement;
  }

  public function getTransTransfer()
  {
    return $this->transTransfer;
  }

  public function setTransTransfer($transTransfer)
  {
    $this->transTransfer = $transTransfer;
  }

  public function getTransInvoice()
  {
    return $this->transInvoice;
  }

  public function setTransInvoice($transInvoice)
  {
    $this->transInvoice = $transInvoice;
  }

  public function getShowAccountnumber()
  {
    return $this->showAccountnumber;
  }

  public function setShowAccountnumber($showAccountnumber)
  {
    $this->showAccountnumber = $showAccountnumber;
  }
  
  public function getShowBic()
  {
    return $this->showBic;
  }

  public function setShowBic($showBic)
  {
    $this->showBic = $showBic;
  }  

  public function getMandateActive()
  {
    return $this->mandateActive;
  }
  
  public function setMandateActive($mandateActive)
  {
    $this->mandateActive = $mandateActive;
  }
  
  public function getMandateDownloadEnabled()
  {
    return $this->mandateDownloadEnabled;
  }

  public function setMandateDownloadEnabled($mandateDownloadEnabled)
  {
    $this->mandateDownloadEnabled = $mandateDownloadEnabled;
  }
  
  public function getKlarnaStoreId()
  {
    return $this->klarnaStoreId;
  }

  public function setKlarnaStoreId($klarnaStoreId)
  {
    $this->klarnaStoreId = $klarnaStoreId;
  }
  
  public function getSaveTerms()
  {
    return $this->saveTerms;
  }

  public function setSaveTerms($saveTerms)
  {
    $this->saveTerms = $saveTerms;
  }
  
  public function getPaypalEcsActive()
  {
    return $this->paypalEcsActive;
  }

  public function setPaypalEcsActive($paypalEcsActive)
  {
    $this->paypalEcsActive = $paypalEcsActive;
  }
  
  public function getCreditcardMinValid()
  {
    return $this->creditcardMinValid;
  }

  public function setCreditcardMinValid($creditcardMinValid)
  {
    $this->creditcardMinValid = $creditcardMinValid;
  }
  
  public function getAdresscheckBillingCountries()
  {
    return $this->adresscheckBillingCountries;
  }

  public function setAdresscheckBillingCountries($adresscheckBillingCountries)
  {
    $this->adresscheckBillingCountries = $adresscheckBillingCountries;
  }
  
  public function getAdresscheckShippingCountries()
  {
    return $this->adresscheckShippingCountries;
  }

  public function setAdresscheckShippingCountries($adresscheckShippingCountries)
  {
    $this->adresscheckShippingCountries = $adresscheckShippingCountries;
  }
  
  public function getPayolutionCompanyName()
  {
    return $this->payolutionCompanyName;
  }

  public function setPayolutionCompanyName($payolutionCompanyName)
  {
    $this->payolutionCompanyName = $payolutionCompanyName;
  }  
 
  public function getPayolutionB2bmode()
  {
    return $this->payolutionB2bmode;
  }

  public function setPayolutionB2bMode($payolutionB2bmode)
  {
    $this->payolutionB2bmode = $payolutionB2bmode;
  }   
    
}