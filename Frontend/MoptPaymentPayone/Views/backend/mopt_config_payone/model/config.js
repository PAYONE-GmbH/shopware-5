/**
 * $Id: $
 */

//{block name="backend/mopt_config_payone/model/config"}
Ext.define('Shopware.apps.MoptConfigPayone.model.Config', {
  extend: 'Ext.data.Model',
  fields: [
    { name: 'id', type: 'int' },
    { name: 'merchantId', type: 'int' },
    { name: 'portalId', type: 'int' },
    { name: 'subaccountId', type: 'int' },
    { name: 'apiKey', type: 'string' },
    { name: 'liveMode', type: 'boolean' },
    { name: 'authorisationMethod', type: 'string' },
    { name: 'submitBasket', type: 'boolean' },
    { name: 'adresscheckActive', type: 'boolean' },
    { name: 'adresscheckLiveMode', type: 'boolean'},
    { name: 'adresscheckBillingAdress', type: 'int'},
    { name: 'adresscheckShippingAdress', type: 'int'},
    { name: 'adresscheckAutomaticCorrection', type: 'int'},
    { name: 'adresscheckFailureHandling', type: 'int'},
    { name: 'adresscheckMinBasket', type: 'int'},
    { name: 'adresscheckMaxBasket', type: 'int'},
    { name: 'adresscheckLifetime', type: 'int'},
    { name: 'adresscheckFailureMessage', type: 'string' },
    { name: 'mapPersonCheck', type: 'int' },
    { name: 'mapKnowPreLastname', type: 'int' },
    { name: 'mapKnowLastname', type: 'int' },
    { name: 'mapNotKnowPreLastname', type: 'int' },
    { name: 'mapMultiNameToAdress', type: 'int' },
    { name: 'mapUndeliverable', type: 'int' },
    { name: 'mapPersonDead', type: 'int' },
    { name: 'mapWrongAdress', type: 'int' },
    { name: 'mapAddressCheckNotPossible', type: 'int' },
    { name: 'mapAddressOkayBuildingUnknown', type: 'int' },
    { name: 'mapPersonMovedAddressUnknown', type: 'int' },
    { name: 'mapUnknownReturnValue', type: 'int' },
    { name: 'consumerscoreActive', type: 'boolean' },
    { name: 'consumerscoreLiveMode', type: 'boolean'},
    { name: 'consumerscoreCheckMoment', type: 'int'},
    { name: 'consumerscoreCheckModeB2C', type: 'string'},
    { name: 'consumerscoreCheckModeB2B', type: 'string'},
    { name: 'consumerscoreDefault', type: 'int'},
    { name: 'consumerscoreBoniversumUnknown', type: 'int'},
    { name: 'consumerscoreLifetime', type: 'int'},
    { name: 'consumerscoreMinBasket', type: 'int'},
    { name: 'consumerscoreMaxBasket', type: 'int'},
    { name: 'consumerscoreFailureHandling', type: 'int'},
    { name: 'consumerscoreNoteMessage', type: 'string'},
    { name: 'consumerscoreNoteActive', type: 'boolean'},
    { name: 'consumerscoreAgreementMessage', type: 'string'},
    { name: 'consumerscoreAgreementActive', type: 'boolean'},
    { name: 'consumerscoreAbtestValue', type: 'int' },
    { name: 'consumerscoreAbtestActive', type: 'boolean'},
    { name: 'stateAppointed', type: 'int'},
    { name: 'stateCapture', type: 'int'},
    { name: 'statePaid', type: 'int'},
    { name: 'stateUnderpaid', type: 'int'},
    { name: 'stateCancelation', type: 'int'},
    { name: 'stateRefund', type: 'int'},
    { name: 'stateDebit', type: 'int'},
    { name: 'stateReminder', type: 'int'},
    { name: 'stateReminder2', type: 'int'},
    { name: 'stateReminder3', type: 'int'},
    { name: 'stateReminder4', type: 'int'},
    { name: 'stateReminder5', type: 'int'},
    { name: 'stateReminderA', type: 'int'},
    { name: 'stateReminderS', type: 'int'},
    { name: 'stateReminderM', type: 'int'},
    { name: 'stateReminderI', type: 'int'},
    { name: 'stateVauthorization', type: 'int'},
    { name: 'stateVsettlement', type: 'int'},
    { name: 'stateTransfer', type: 'int'},
    { name: 'stateInvoice', type: 'int'},
    { name: 'stateFailed', type: 'int'},
    { name: 'extra', type: 'string'},
    { name: 'checkCc', type: 'boolean'},
    { name: 'checkAccount', type: 'int'},
    { name: 'transLogging', type: 'boolean'},
    { name: 'transTimeout', type: 'int'},
    { name: 'transTimeoutRaise', type: 'int'},
    { name: 'transMaxTrials', type: 'int'},
    { name: 'transAppointed', type: 'string'},
    { name: 'transCapture', type: 'string'},
    { name: 'transPaid', type: 'string'},
    { name: 'transUnderpaid', type: 'string'},
    { name: 'transCancelation', type: 'string'},
    { name: 'transRefund', type: 'string'},
    { name: 'transDebit', type: 'string'},
    { name: 'transReminder', type: 'string'},
    { name: 'transVauthorization', type: 'string'},
    { name: 'transVsettlement', type: 'string'},
    { name: 'transTransfer', type: 'string'},
    { name: 'transInvoice', type: 'string'},
    { name: 'transFailed', type: 'string'},
    { name: 'showAccountnumber', type: 'boolean'},
    { name: 'showBic', type: 'boolean'},    
    { name: 'showSofortIbanBic', type: 'boolean'},
    { name: 'mandateActive', type: 'boolean'},
    { name: 'mandateDownloadEnabled', type: 'boolean'},
    { name: 'klarnaStoreId', type: 'string'},
    { name: 'saveTerms', type: 'int'},
    { name: 'paypalEcsActive', type: 'boolean'},
    { name: 'creditcardMinValid', type: 'int'},
    { name: 'adresscheckBillingCountries', type: 'string'},
    { name: 'adresscheckShippingCountries', type: 'string'},
    { name: 'payolutionCompanyName', type: 'string'},
    { name: 'payolutionB2bmode', type: 'boolean'},
    { name: 'payolutionDraftUser', type: 'string'},
    { name: 'payolutionDraftPassword', type: 'string'},
    { name: 'paydirektOvercapture', type: 'boolean'},
    { name: 'sendOrdernumberAsReference', type: 'boolean'},
    { name: 'changeOrderOnTXS', type: 'boolean'},
    { name: 'ratepaySnippetId', type: 'string'},
    { name: 'trustlyShowIbanBic', type: 'boolean'},
    { name: 'applepayMerchantId', type: 'string'},
    { name: 'applepayVisa', type: 'boolean'},
    { name: 'applepayMastercard', type: 'boolean'},
    { name: 'applepayGirocard', type: 'boolean'},
    // { name: 'applepayAmex', type: 'boolean'},
    // { name: 'applepayDiscover', type: 'boolean'},
    { name: 'applepayCertificate', type: 'string'},
    { name: 'applepayPrivateKey', type: 'string'},
    { name: 'applepayPrivateKeyPassword', type: 'string'},
    { name: 'applepayDebug', type: 'boolean'},
    { name: 'allowDifferentAddresses', type: 'boolean'},
    { name: 'creditcardDefaultDescription', type: 'string'},
  ],
  /**
   * Validation
   */
  validations: [
    
  ],
  /**
   * Auch Models können über eigene Proxies verfügen. Dadurch
   * ist es möglich, konkrete Datensätze zu persistieren ohne
   * immer direkt auf einem Store arbeiten zu müssen:
   */
  proxy: {
    type: 'ajax',
    /**
     * Konfiguriert die Controller-Actions, die für die jeweiligen
     * Proxy-Operationen ausgeführt werden.
     */
    api: {
      create: '{url action="saveConfig"}',
      update: '{url action="updateConfig"}'
    },
    /**
     * Konfiguriert den Datenaustausch zwischen
     * Controller und Proxy
     */
    reader: {
      type: 'json',
      root: 'data'
    }
  }
});
//{/block}
