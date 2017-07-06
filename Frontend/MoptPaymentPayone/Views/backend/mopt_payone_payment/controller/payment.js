//{namespace name=backend/mopt_payone_payment/main}
//{block name="backend/payment/controller/payment" append}
Ext.define('Shopware.apps.Payment.controller.MoptPayonePayment', {
  override: 'Shopware.apps.Payment.controller.Payment',
  
  init: function() {
    var me = this;
    
    me.control({
      'payment-main-window': {
        moptPayoneDuplicatePayment: me.onMoptPayoneDuplicatePayment
      }
    });
    
    me.callParent(arguments);
  },
  
  onMoptPayoneDuplicatePayment: function(generalForm) {
    var me = this,
    record = generalForm.getRecord(),
    paymentId = record.get('id'),
    paymentName = record.get('description'),
    paymentStore = me.subApplication.paymentStore;
      
    Ext.Ajax.request({
      url: '{url controller="MoptPayonePayment" action="moptPayoneDuplicatePayment"}',
      method: 'POST',
      params: {
        id: paymentId
      },
      headers: {
        'Accept': 'application/json'
      },
      success: function(response)
      {
        var jsonData = Ext.JSON.decode(response.responseText);
        if (jsonData.success)
        {
          Ext.Msg.alert('{s name=duplicatePayment/title}Zahlart Duplizieren{/s}', 
          '{s name=duplicatePayment/thePayment}Die Zahlart{/s} \"' 
                  + paymentName + '\" {s name=duplicatePayment/successful}wurde erfolgreich dupliziert{/s}');

          //reload form
          options.callback(record);
        }
        else
        {
          Ext.Msg.alert('{s name=duplicatePayment/title}Zahlart Duplizieren{/s}', jsonData.error_message);
        }
      }
    });
    paymentStore.load();
  }
  
  
});
//{/block}