<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneCreditcardConfig;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_creditcard_config")
 */
class MoptPayoneCreditcardConfig extends ModelEntity
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $errorLocaleId
     *
     * @ORM\Column(name="error_locale_id", type="integer")
     */
    protected $errorLocaleId;

    /**
     * @var Locale $locale
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Locale")
     * @ORM\JoinColumn(name="error_locale_id", referencedColumnName="id")
     */
    private $locale;

    /**
     * @var integer $shopId
     * @ORM\Column(name="shop_id", type="integer", unique=true)
     */
    protected $shopId;

    /**
     * @var Shop $shop
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     */
    private $shop;

    /**
     * @var bool $showErrors
     * @ORM\Column(name="show_errors", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $showErrors;

    /**
     * @var bool $isDefault
     * @ORM\Column(name="is_default", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $isDefault;

    /**
     * @var integer $integrationType
     * @ORM\Column(name="integration_type", type="integer", unique=false)
     */
    protected $integrationType;

    /**
     * @var string $standardInputCss
     * @ORM\Column(name="standard_input_css", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $standardInputCss;

    /**
     * @var string $standardInputCssSelected
     * @ORM\Column(name="standard_input_css_selected", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $standardInputCssSelected;

    /**
     * @var string $standardIframeHeight
     * @ORM\Column(name="standard_iframe_height", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $standardIframeHeight;

    /**
     * @var string $standardIframeWidth
     * @ORM\Column(name="standard_iframe_width", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $standardIframeWidth;

    /**
     * @var integer $cardnoInputChars
     *
     * @ORM\Column(name="cardno_input_chars", type="integer", unique=false, nullable=true)
     */
    protected $cardnoInputChars;

    /**
     * @var integer $cardnoInputCharsMax
     *
     * @ORM\Column(name="cardno_input_chars_max", type="integer", unique=false, nullable=true)
     */
    protected $cardnoInputCharsMax;

    /**
     * @var string $cardnoInputCss
     * @ORM\Column(name="cardno_input_css", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardnoInputCss;

    /**
     * @var string $cardnoCustomIframe
     * @ORM\Column(name="cardno_custom_iframe", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardnoCustomIframe;

    /**
     * @var string $cardnoIframeHeight
     * @ORM\Column(name="cardno_iframe_height", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardnoIframeHeight;

    /**
     * @var string $cardnoIframeWidth
     * @ORM\Column(name="cardno_iframe_width", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardnoIframeWidth;

    /**
     * @var bool $cardnoCustomStyle
     * @ORM\Column(name="cardno_custom_style", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardnoCustomStyle;

    /**
     * @var string $cardnoFieldType
     * @ORM\Column(name="cardno_field_type", type="string", length=100, nullable=true, unique=false)
     */
    private $cardnoFieldType;

    /**
     * @var integer $cardcvcInputChars
     *
     * @ORM\Column(name="cardcvc_input_chars", type="integer", unique=false, nullable=true)
     */
    protected $cardcvcInputChars;

    /**
     * @var integer $cardcvcInputCharsMax
     *
     * @ORM\Column(name="cardcvc_input_chars_max", type="integer", unique=false, nullable=true)
     */
    protected $cardcvcInputCharsMax;

    /**
     * @var string $cardcvcInputCss
     * @ORM\Column(name="cardcvc_input_css", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardcvcInputCss;

    /**
     * @var string $cardcvcCustomIframe
     * @ORM\Column(name="cardcvc_custom_iframe", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardcvcCustomIframe;

    /**
     * @var string $cardcvcIframeHeight
     * @ORM\Column(name="cardcvc_iframe_height", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardcvcIframeHeight;

    /**
     * @var string $cardcvcIframeWidth
     * @ORM\Column(name="cardcvc_iframe_width", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardcvcIframeWidth;

    /**
     * @var bool $cardcvcCustomStyle
     * @ORM\Column(name="cardcvc_custom_style", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardcvcCustomStyle;

    /**
     * @var string $cardcvcFieldType
     * @ORM\Column(name="cardcvc_field_type", type="string", length=100, nullable=true, unique=false)
     */
    private $cardcvcFieldType;

    /**
     * @var integer $cardmonthInputChars
     *
     * @ORM\Column(name="cardmonth_input_chars", type="integer", unique=false, nullable=true)
     */
    protected $cardmonthInputChars;

    /**
     * @var integer $cardmonthInputCharsMax
     *
     * @ORM\Column(name="cardmonth_input_chars_max", type="integer", unique=false, nullable=true)
     */
    protected $cardmonthInputCharsMax;

    /**
     * @var string $cardmonthInputCss
     * @ORM\Column(name="cardmonth_input_css", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardmonthInputCss;

    /**
     * @var string $cardmonthCustomIframe
     * @ORM\Column(name="cardmonth_custom_iframe", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardmonthCustomIframe;

    /**
     * @var string $cardmonthIframeHeight
     * @ORM\Column(name="cardmonth_iframe_height", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardmonthIframeHeight;

    /**
     * @var string $cardmonthIframeWidth
     * @ORM\Column(name="cardmonth_iframe_width", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardmonthIframeWidth;

    /**
     * @var bool $cardmonthCustomStyle
     * @ORM\Column(name="cardmonth_custom_style", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardmonthCustomStyle;

    /**
     * @var string $cardmonthFieldType
     * @ORM\Column(name="cardmonth_field_type", type="string", length=100, nullable=true, unique=false)
     */
    private $cardmonthFieldType;

    /**
     * @var integer $cardyearInputChars
     *
     * @ORM\Column(name="cardyear_input_chars", type="integer", unique=false, nullable=true)
     */
    protected $cardyearInputChars;

    /**
     * @var integer $cardyearInputCharsMax
     *
     * @ORM\Column(name="cardyear_input_chars_max", type="integer", unique=false, nullable=true)
     */
    protected $cardyearInputCharsMax;

    /**
     * @var string $cardyearInputCss
     * @ORM\Column(name="cardyear_input_css", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardyearInputCss;

    /**
     * @var string $cardyearCustomIframe
     * @ORM\Column(name="cardyear_custom_iframe", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardyearCustomIframe;

    /**
     * @var string $cardyearIframeHeight
     * @ORM\Column(name="cardyear_iframe_height", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardyearIframeHeight;

    /**
     * @var string $cardyearIframeWidth
     * @ORM\Column(name="cardyear_iframe_width", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardyearIframeWidth;

    /**
     * @var bool $cardyearCustomStyle
     * @ORM\Column(name="cardyear_custom_style", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $cardyearCustomStyle;

    /**
     * @var string $cardyearFieldType
     * @ORM\Column(name="cardyear_field_type", type="string", length=100, nullable=true, unique=false)
     */
    private $cardyearFieldType;

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
     * @ORM\Column(name="check_cc", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $checkCc;

    /**
     * @ORM\Column(name="creditcard_min_valid", type="integer", nullable=true, unique=false)
     */
    private $creditcardMinValid;

    /**
     * @var bool $enableAutoCardtypeDetection
     * @ORM\Column(name="auto_cardtype_detection", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $enableAutoCardtypeDetection;
    
    /**
     * @ORM\Column(name="default_translation_iframe_month1", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth1;    

    /**
     * @ORM\Column(name="default_translation_iframe_month2", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth2;
    
    /**
     * @ORM\Column(name="default_translation_iframe_month3", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth3;   

    /**
     * @ORM\Column(name="default_translation_iframe_month4", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth4;       
    
    /**
     * @ORM\Column(name="default_translation_iframe_month5", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth5;  
    
    /**
     * @ORM\Column(name="default_translation_iframe_month6", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth6;       
    
    /**
     * @ORM\Column(name="default_translation_iframe_month7", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth7;    

    /**
     * @ORM\Column(name="default_translation_iframe_month8", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth8;
    
    /**
     * @ORM\Column(name="default_translation_iframe_month9", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth9;   

    /**
     * @ORM\Column(name="default_translation_iframe_month10", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth10;       
    
    /**
     * @ORM\Column(name="default_translation_iframe_month11", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth11;  
    
    /**
     * @ORM\Column(name="default_translation_iframe_month12", type="string", length=60, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeMonth12;        
    
    /**
     * @ORM\Column(name="default_translation_iframeinvalid_cardpan", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeinvalidCardpan;    

    /**
     * @ORM\Column(name="default_translation_iframeinvalid_cvc", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeinvalidCvc;
    
    /**
     * @ORM\Column(name="default_translation_iframeinvalid_pan_for_cardtype", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeinvalidPanForCardtype;   

    /**
     * @ORM\Column(name="default_translation_iframeinvalid_cardtype", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeinvalidCardtype;       
    
    /**
     * @ORM\Column(name="default_translation_iframeinvalid_expire_date", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeinvalidExpireDate;  
    
    /**
     * @ORM\Column(name="default_translation_iframeinvalid_issue_number", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeinvalidIssueNumber;       
    
    /**
     * @ORM\Column(name="default_translation_iframetransaction_rejected", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframetransactionRejected;   
    
    /**
     * @ORM\Column(name="default_translation_iframe_cardpan", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeCardpan;       
    
    /**
     * @ORM\Column(name="default_translation_iframe_cvc", type="string", length=255, precision=0, scale=0, nullable=false)
     */
    private $defaultTranslationIframeCvc;      

    public function __construct()
    {
        $this->creditcardConfigs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * add creditcard config to collection
     *
     * @param \Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig $creditcardConfig
     */
    public function addCreditcardConfig(\Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal $creditcardConfig)
    {
        $this->creditcardConfigs[] = $creditcardConfig;
    }

    /**
     * Set creditcard config collection
     *
     * @param $creditcardConfigs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setCreditcardConfigs($creditcardConfigs)
    {
        $this->creditcardConfigs = $creditcardConfigs;
        return $this;
    }

    /**
     * Get creditcard config collection
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditcardConfigs()
    {
        return $this->creditcardConfigs;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getShowErrors()
    {
        return $this->showErrors;
    }

    public function setShowErrors($showErrors)
    {
        $this->showErrors = $showErrors;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function getIntegrationType()
    {
        return $this->integrationType;
    }

    public function getStandardInputCss()
    {
        return $this->standardInputCss;
    }

    public function getStandardInputCssSelected()
    {
        return $this->standardInputCssSelected;
    }

    public function getStandardIframeHeight()
    {
        return $this->standardIframeHeight;
    }

    public function getStandardIframeWidth()
    {
        return $this->standardIframeWidth;
    }

    public function getCardnoInputChars()
    {
        return $this->cardnoInputChars;
    }

    public function getCardnoInputCharsMax()
    {
        return $this->cardnoInputCharsMax;
    }

    public function getCardnoInputCss()
    {
        return $this->cardnoInputCss;
    }

    public function getCardnoCustomIframe()
    {
        return $this->cardnoCustomIframe;
    }

    public function getCardnoIframeHeight()
    {
        return $this->cardnoIframeHeight;
    }

    public function getCardnoIframeWidth()
    {
        return $this->cardnoIframeWidth;
    }

    public function getCardnoCustomStyle()
    {
        return $this->cardnoCustomStyle;
    }

    public function getCardnoFieldType()
    {
        return $this->cardnoFieldType;
    }

    public function getCardcvcInputChars()
    {
        return $this->cardcvcInputChars;
    }

    public function getCardcvcInputCharsMax()
    {
        return $this->cardcvcInputCharsMax;
    }

    public function getCardcvcInputCss()
    {
        return $this->cardcvcInputCss;
    }

    public function getCardcvcCustomIframe()
    {
        return $this->cardcvcCustomIframe;
    }

    public function getCardcvcIframeHeight()
    {
        return $this->cardcvcIframeHeight;
    }

    public function getCardcvcIframeWidth()
    {
        return $this->cardcvcIframeWidth;
    }

    public function getCardcvcCustomStyle()
    {
        return $this->cardcvcCustomStyle;
    }

    public function getCardcvcFieldType()
    {
        return $this->cardcvcFieldType;
    }

    public function getCardmonthInputChars()
    {
        return $this->cardmonthInputChars;
    }

    public function getCardmonthInputCharsMax()
    {
        return $this->cardmonthInputCharsMax;
    }

    public function getCardmonthInputCss()
    {
        return $this->cardmonthInputCss;
    }

    public function getCardmonthCustomIframe()
    {
        return $this->cardmonthCustomIframe;
    }

    public function getCardmonthIframeHeight()
    {
        return $this->cardmonthIframeHeight;
    }

    public function getCardmonthIframeWidth()
    {
        return $this->cardmonthIframeWidth;
    }

    public function getCardmonthCustomStyle()
    {
        return $this->cardmonthCustomStyle;
    }

    public function getCardmonthFieldType()
    {
        return $this->cardmonthFieldType;
    }

    public function getCardyearInputChars()
    {
        return $this->cardyearInputChars;
    }

    public function getCardyearInputCharsMax()
    {
        return $this->cardyearInputCharsMax;
    }

    public function getCardyearInputCss()
    {
        return $this->cardyearInputCss;
    }

    public function getCardyearCustomIframe()
    {
        return $this->cardyearCustomIframe;
    }

    public function getCardyearIframeHeight()
    {
        return $this->cardyearIframeHeight;
    }

    public function getCardyearIframeWidth()
    {
        return $this->cardyearIframeWidth;
    }

    public function getCardyearCustomStyle()
    {
        return $this->cardyearCustomStyle;
    }

    public function getCardyearFieldType()
    {
        return $this->cardyearFieldType;
    }

    public function setShop(\Shopware\Models\Shop\Shop $shop)
    {
        $this->shop = $shop;
    }

    public function setIntegrationType($integrationType)
    {
        $this->integrationType = $integrationType;
    }

    public function setStandardInputCss($standardInputCss)
    {
        $this->standardInputCss = $standardInputCss;
    }

    public function setStandardInputCssSelected($standardInputCssSelected)
    {
        $this->standardInputCssSelected = $standardInputCssSelected;
    }

    public function setStandardIframeHeight($standardIframeHeight)
    {
        $this->standardIframeHeight = $standardIframeHeight;
    }

    public function setStandardIframeWidth($standardIframeWidth)
    {
        $this->standardIframeWidth = $standardIframeWidth;
    }

    public function setCardnoInputChars($cardnoInputChars)
    {
        $this->cardnoInputChars = $cardnoInputChars;
    }

    public function setCardnoInputCharsMax($cardnoInputCharsMax)
    {
        $this->cardnoInputCharsMax = $cardnoInputCharsMax;
    }

    public function setCardnoInputCss($cardnoInputCss)
    {
        $this->cardnoInputCss = $cardnoInputCss;
    }

    public function setCardnoCustomIframe($cardnoCustomIframe)
    {
        $this->cardnoCustomIframe = $cardnoCustomIframe;
    }

    public function setCardnoIframeHeight($cardnoIframeHeight)
    {
        $this->cardnoIframeHeight = $cardnoIframeHeight;
    }

    public function setCardnoIframeWidth($cardnoIframeWidth)
    {
        $this->cardnoIframeWidth = $cardnoIframeWidth;
    }

    public function setCardnoCustomStyle($cardnoCustomStyle)
    {
        $this->cardnoCustomStyle = $cardnoCustomStyle;
    }

    public function setCardnoFieldType($cardnoFieldType)
    {
        $this->cardnoFieldType = $cardnoFieldType;
    }

    public function setCardcvcInputChars($cardcvcInputChars)
    {
        $this->cardcvcInputChars = $cardcvcInputChars;
    }

    public function setCardcvcInputCharsMax($cardcvcInputCharsMax)
    {
        $this->cardcvcInputCharsMax = $cardcvcInputCharsMax;
    }

    public function setCardcvcInputCss($cardcvcInputCss)
    {
        $this->cardcvcInputCss = $cardcvcInputCss;
    }

    public function setCardcvcCustomIframe($cardcvcCustomIframe)
    {
        $this->cardcvcCustomIframe = $cardcvcCustomIframe;
    }

    public function setCardcvcIframeHeight($cardcvcIframeHeight)
    {
        $this->cardcvcIframeHeight = $cardcvcIframeHeight;
    }

    public function setCardcvcIframeWidth($cardcvcIframeWidth)
    {
        $this->cardcvcIframeWidth = $cardcvcIframeWidth;
    }

    public function setCardcvcCustomStyle($cardcvcCustomStyle)
    {
        $this->cardcvcCustomStyle = $cardcvcCustomStyle;
    }

    public function setCardcvcFieldType($cardcvcFieldType)
    {
        $this->cardcvcFieldType = $cardcvcFieldType;
    }

    public function setCardmonthInputChars($cardmonthInputChars)
    {
        $this->cardmonthInputChars = $cardmonthInputChars;
    }

    public function setCardmonthInputCharsMax($cardmonthInputCharsMax)
    {
        $this->cardmonthInputCharsMax = $cardmonthInputCharsMax;
    }

    public function setCardmonthInputCss($cardmonthInputCss)
    {
        $this->cardmonthInputCss = $cardmonthInputCss;
    }

    public function setCardmonthCustomIframe($cardmonthCustomIframe)
    {
        $this->cardmonthCustomIframe = $cardmonthCustomIframe;
    }

    public function setCardmonthIframeHeight($cardmonthIframeHeight)
    {
        $this->cardmonthIframeHeight = $cardmonthIframeHeight;
    }

    public function setCardmonthIframeWidth($cardmonthIframeWidth)
    {
        $this->cardmonthIframeWidth = $cardmonthIframeWidth;
    }

    public function setCardmonthCustomStyle($cardmonthCustomStyle)
    {
        $this->cardmonthCustomStyle = $cardmonthCustomStyle;
    }

    public function setCardmonthFieldType($cardmonthFieldType)
    {
        $this->cardmonthFieldType = $cardmonthFieldType;
    }

    public function setCardyearInputChars($cardyearInputChars)
    {
        $this->cardyearInputChars = $cardyearInputChars;
    }

    public function setCardyearInputCharsMax($cardyearInputCharsMax)
    {
        $this->cardyearInputCharsMax = $cardyearInputCharsMax;
    }

    public function setCardyearInputCss($cardyearInputCss)
    {
        $this->cardyearInputCss = $cardyearInputCss;
    }

    public function setCardyearCustomIframe($cardyearCustomIframe)
    {
        $this->cardyearCustomIframe = $cardyearCustomIframe;
    }

    public function setCardyearIframeHeight($cardyearIframeHeight)
    {
        $this->cardyearIframeHeight = $cardyearIframeHeight;
    }

    public function setCardyearIframeWidth($cardyearIframeWidth)
    {
        $this->cardyearIframeWidth = $cardyearIframeWidth;
    }

    public function setCardyearCustomStyle($cardyearCustomStyle)
    {
        $this->cardyearCustomStyle = $cardyearCustomStyle;
    }

    public function setCardyearFieldType($cardyearFieldType)
    {
        $this->cardyearFieldType = $cardyearFieldType;
    }

    public function getIsDefault()
    {
        return $this->isDefault;
    }

    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
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

    public function getCheckCc()
    {
        return $this->checkCc;
    }

    public function setCheckCc($checkCc)
    {
        $this->checkCc = $checkCc;
    }

    public function getCreditcardMinValid()
    {
        return $this->creditcardMinValid;
    }

    public function setCreditcardMinValid($creditcardMinValid)
    {
        $this->creditcardMinValid = $creditcardMinValid;
    }

    public function getEnableAutoCardtypeDetection()
    {
        return $this->enableAutoCardtypeDetection;
    }

    public function setEnableAutoCardtypeDetection($enableAutoCardtypeDetection)
    {
        $this->enableAutoCardtypeDetection = $enableAutoCardtypeDetection;
    }
    
    public function getDefaultTranslationIframeMonth1()
    {
        return $this->defaultTranslationIframeMonth1;
    }

    public function setDefaultTranslationIframeMonth1($defaultTranslationIframeMonth1)
    {
        $this->defaultTranslationIframeMonth1 = $defaultTranslationIframeMonth1;
    }       
    
    public function getDefaultTranslationIframeMonth2()
    {
        return $this->defaultTranslationIframeMonth2;
    }

    public function setDefaultTranslationIframeMonth2($defaultTranslationIframeMonth2)
    {
        $this->defaultTranslationIframeMonth2 = $defaultTranslationIframeMonth2;
    }
    
    public function getDefaultTranslationIframeMonth3()
    {
        return $this->defaultTranslationIframeMonth3;
    }

    public function setDefaultTranslationIframeMonth3($defaultTranslationIframeMonth3)
    {
        $this->defaultTranslationIframeMonth3 = $defaultTranslationIframeMonth3;
    }   

    public function getDefaultTranslationIframeMonth4()
    {
        return $this->defaultTranslationIframeMonth4;
    }

    public function setDefaultTranslationIframeMonth4($defaultTranslationIframeMonth4)
    {
        $this->defaultTranslationIframeMonth4 = $defaultTranslationIframeMonth4;
    }     
    
    public function getDefaultTranslationIframeMonth5()
    {
        return $this->defaultTranslationIframeMonth5;
    }

    public function setDefaultTranslationIframeMonth5($defaultTranslationIframeMonth5)
    {
        $this->defaultTranslationIframeMonth5 = $defaultTranslationIframeMonth5;
    }  

    public function getDefaultTranslationIframeMonth6()
    {
        return $this->defaultTranslationIframeMonth6;
    }

    public function setDefaultTranslationIframeMonth6($defaultTranslationIframeMonth6)
    {
        $this->defaultTranslationIframeMonth6 = $defaultTranslationIframeMonth6;
    }  

    public function getDefaultTranslationIframeMonth7()
    {
        return $this->defaultTranslationIframeMonth7;
    }

    public function setDefaultTranslationIframeMonth7($defaultTranslationIframeMonth7)
    {
        $this->defaultTranslationIframeMonth7 = $defaultTranslationIframeMonth7;
    }  

    public function getDefaultTranslationIframeMonth8()
    {
        return $this->defaultTranslationIframeMonth8;
    }

    public function setDefaultTranslationIframeMonth8($defaultTranslationIframeMonth8)
    {
        $this->defaultTranslationIframeMonth8 = $defaultTranslationIframeMonth8;
    }  

    public function getDefaultTranslationIframeMonth9()
    {
        return $this->defaultTranslationIframeMonth9;
    }

    public function setDefaultTranslationIframeMonth9($defaultTranslationIframeMonth9)
    {
        $this->defaultTranslationIframeMonth9 = $defaultTranslationIframeMonth9;
    }  

    public function getDefaultTranslationIframeMonth10()
    {
        return $this->defaultTranslationIframeMonth10;
    }

    public function setDefaultTranslationIframeMonth10($defaultTranslationIframeMonth10)
    {
        $this->defaultTranslationIframeMonth10 = $defaultTranslationIframeMonth10;
    }  

    public function getDefaultTranslationIframeMonth11()
    {
        return $this->defaultTranslationIframeMonth11;
    }

    public function setDefaultTranslationIframeMonth11($defaultTranslationIframeMonth11)
    {
        $this->defaultTranslationIframeMonth11 = $defaultTranslationIframeMonth11;
    }      
    
    public function getDefaultTranslationIframeMonth12()
    {
        return $this->defaultTranslationIframeMonth12;
    }

    public function setDefaultTranslationIframeMonth12($defaultTranslationIframeMonth12)
    {
        $this->defaultTranslationIframeMonth12 = $defaultTranslationIframeMonth12;
    }

    public function getDefaultTranslationIframeinvalidCardpan()
    {
        return $this->defaultTranslationIframeinvalidCardpan;
    }

    public function setDefaultTranslationIframeinvalidCardpan($defaultTranslationIframeinvalidCardpan)
    {
        $this->defaultTranslationIframeinvalidCardpan = $defaultTranslationIframeinvalidCardpan;
    }       
    
    public function getDefaultTranslationIframeinvalidCvc()
    {
        return $this->defaultTranslationIframeinvalidCvc;
    }

    public function setDefaultTranslationIframeinvalidCvc($defaultTranslationIframeinvalidCvc)
    {
        $this->defaultTranslationIframeinvalidCvc = $defaultTranslationIframeinvalidCvc;
    }
    
    public function getDefaultTranslationIframeinvalidPanForCardtype()
    {
        return $this->defaultTranslationIframeinvalidPanForCardtype;
    }

    public function setDefaultTranslationIframeinvalidPanForCardtype($defaultTranslationIframeinvalidPanForCardtype)
    {
        $this->defaultTranslationIframeinvalidPanForCardtype = $defaultTranslationIframeinvalidPanForCardtype;
    }
    
    public function getDefaultTranslationIframeinvalidCardtype()
    {
        return $this->defaultTranslationIframeinvalidCardtype;
    }

    public function setDefaultTranslationIframeinvalidCardtype($defaultTranslationIframeinvalidCardtype)
    {
        $this->defaultTranslationIframeinvalidCardtype = $defaultTranslationIframeinvalidCardtype;
    }      

    public function getDefaultTranslationIframeinvalidExpireDate()
    {
        return $this->defaultTranslationIframeinvalidExpireDate;
    }

    public function setDefaultTranslationIframeinvalidExpireDate($defaultTranslationIframeinvalidExpireDate)
    {
        $this->defaultTranslationIframeinvalidExpireDate = $defaultTranslationIframeinvalidExpireDate;
    }     
    
    public function getDefaultTranslationIframeinvalidIssueNumber()
    {
        return $this->defaultTranslationIframeinvalidIssueNumber;
    }

    public function setDefaultTranslationIframeinvalidIssueNumber($defaultTranslationIframeinvalidIssueNumber)
    {
        $this->defaultTranslationIframeinvalidIssueNumber = $defaultTranslationIframeinvalidIssueNumber;
    }      

    public function getDefaultTranslationIframetransactionRejected()
    {
        return $this->defaultTranslationIframeinvalidExpireDate;
    }

    public function setDefaultTranslationIframetransactionRejected($defaultTranslationIframetransactionRejected)
    {
        $this->defaultTranslationIframetransactionRejected = $defaultTranslationIframetransactionRejected;
    }     
    
    public function getDefaultTranslationIframeCardpan()
    {
        return $this->defaultTranslationIframeCardpan;
    }

    public function setDefaultTranslationIframeCardpan($defaultTranslationIframeCardpan)
    {
        $this->defaultTranslationIframeCardpan = $defaultTranslationIframeCardpan;
    }  

    public function getDefaultTranslationIframeCvc()
    {
        return $this->defaultTranslationIframeCvc;
    }

    public function setDefaultTranslationIframeCvc($defaultTranslationIframeCvc)
    {
        $this->defaultTranslationIframeCvc = $defaultTranslationIframeCvc;
    }

    
}
