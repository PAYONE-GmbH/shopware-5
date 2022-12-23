Ext.define('Shopware.apps.MoptPayoneCreditcardConfig.model.Creditcardconfig', {
    extend: 'Shopware.data.Model',
    configure: function () {
        return {
            controller: 'MoptPayoneCreditcardConfig',
            detail: 'Shopware.apps.MoptPayoneCreditcardConfig.view.detail.Creditcardconfig'
        };
    },
 
    fields: [
        { name: 'id', type: 'int', useNull: true},
        { name: 'errorLocaleId', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'showErrors', type: 'boolean'},
        { name: 'isDefault', type: 'boolean'},
        { name: 'integrationType', type: 'int' },
        { name: 'standardInputCss', type: 'string'},
        { name: 'standardInputCssSelected', type: 'string'},
        { name: 'standardIframeHeight', type: 'string'},
        { name: 'standardIframeWidth', type: 'string'},
        
        { name: 'cardnoInputChars', type: 'int'},
        { name: 'cardnoInputCharsMax', type: 'int'},
        { name: 'cardnoInputCss', type: 'string'},
        { name: 'cardnoCustomIframe', type: 'boolean'},
        { name: 'cardnoIframeHeight', type: 'string'},
        { name: 'cardnoIframeWidth', type: 'string'},
        { name: 'cardnoCustomStyle', type: 'boolean'},
        { name: 'cardnoFieldType', type: 'string'},
        
        { name: 'cardcvcInputChars', type: 'int'},
        { name: 'cardcvcInputCharsMax', type: 'int'},
        { name: 'cardcvcInputCss', type: 'string'},
        { name: 'cardcvcCustomIframe', type: 'boolean'},
        { name: 'cardcvcIframeHeight', type: 'string'},
        { name: 'cardcvcIframeWidth', type: 'string'},
        { name: 'cardcvcCustomStyle', type: 'boolean'},
        { name: 'cardcvcFieldType', type: 'string'},
        
        { name: 'cardmonthInputChars', type: 'int'},
        { name: 'cardmonthInputCharsMax', type: 'int'},
        { name: 'cardmonthInputCss', type: 'string'},
        { name: 'cardmonthCustomIframe', type: 'boolean'},
        { name: 'cardmonthIframeHeight', type: 'string'},
        { name: 'cardmonthIframeWidth', type: 'string'},
        { name: 'cardmonthCustomStyle', type: 'boolean'},
        { name: 'cardmonthFieldType', type: 'string'},
        
        { name: 'cardyearInputChars', type: 'int'},
        { name: 'cardyearInputCharsMax', type: 'int'},
        { name: 'cardyearInputCss', type: 'string'},
        { name: 'cardyearCustomIframe', type: 'boolean'},
        { name: 'cardyearIframeHeight', type: 'string'},
        { name: 'cardyearIframeWidth', type: 'string'},
        { name: 'cardyearCustomStyle', type: 'boolean'},
        { name: 'cardyearFieldType', type: 'string'},
        
        { name: 'defaultTranslationIframeMonth1', type: 'string'},
        { name: 'defaultTranslationIframeMonth2', type: 'string'},
        { name: 'defaultTranslationIframeMonth3', type: 'string'},
        { name: 'defaultTranslationIframeMonth4', type: 'string'},
        { name: 'defaultTranslationIframeMonth5', type: 'string'},
        { name: 'defaultTranslationIframeMonth6', type: 'string'},
        { name: 'defaultTranslationIframeMonth7', type: 'string'},
        { name: 'defaultTranslationIframeMonth8', type: 'string'},
        { name: 'defaultTranslationIframeMonth9', type: 'string'},
        { name: 'defaultTranslationIframeMonth10', type: 'string'},
        { name: 'defaultTranslationIframeMonth11', type: 'string'},
        { name: 'defaultTranslationIframeMonth12', type: 'string'},
        
        { name: 'defaultTranslationIframeinvalidCardpan', type: 'string'},
        { name: 'defaultTranslationIframeinvalidCvc', type: 'string'},
        { name: 'defaultTranslationIframeinvalidPanForCardtype', type: 'string'},
        { name: 'defaultTranslationIframeinvalidCardtype', type: 'string'},
        { name: 'defaultTranslationIframeinvalidExpireDate', type: 'string'},
        { name: 'defaultTranslationIframeinvalidIssueNumber', type: 'string'},
        { name: 'defaultTranslationIframetransactionRejected', type: 'string'},
        
        { name: 'defaultTranslationIframeCardpan', type: 'string'},
        { name: 'defaultTranslationIframeCvc', type: 'string'},

        { name: 'merchantId', type: 'int' },
        { name: 'portalId', type: 'int' },
        { name: 'subaccountId', type: 'int' },
        { name: 'apiKey', type: 'string' },
        { name: 'liveMode', type: 'boolean' },
        { name: 'checkCc', type: 'boolean'},
        { name: 'creditcardMinValid', type: 'int'},
        { name: 'enableAutoCardtypeDetection', type: 'boolean'},
    ],
    
    associations: [
        {
            relation: 'ManyToOne',
            field: 'errorLocaleId',
            
            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Locale',
            name: 'getLocale',
            associationKey: 'locale'
        },
        {
            relation: 'ManyToOne',
            field: 'shopId',
            
            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Shop',
            name: 'getShop',
            associationKey: 'shop'
        }
    ]
});
 
