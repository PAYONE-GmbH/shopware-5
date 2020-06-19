//{namespace name=backend/mopt_payone_order/main}
//{block name="backend/order/controller/detail" append}
Ext.define('Shopware.apps.Order.controller.MoptPayoneDetail', {
  override: 'Shopware.apps.Order.controller.Detail',
  
  init: function() {
    var me = this;
    me.callParent(arguments);
    
    me.control({
        'order-detail-window order-position-panel': {
          moptPayoneCapturePositions: me.onMoptPayoneCapturePositions,
          moptPayoneDebitPositions: me.onMoptPayoneDebitPositions
        }
    });
  },
  
  onMoptPayoneDebitPositions: function(order, grid, options) {
    var me = this;
    var positionIds = me.moptPayoneGetPositionIdsFromGrid(grid);
    
    if(!positionIds){
      return;
    }
    
    var selectionModel = grid.getSelectionModel();
    var positions = selectionModel.getSelection();
    var amount = 0;
    var currency = order.raw.currency;
    
    
    for (var i = 0; i < positions.length; i++)
    {
      amount+=positions[i].get('total');
    }
    
    var details = order.raw.details;
    var showShippingCostsCheckbox = true;
    
    for (var i = 0; i < details.length; i++)
    {
      if (details[i].articleNumber === "SHIPPING")
      {
        showShippingCostsCheckbox = false;
      }
    }
    
    var moptMessageBoxText =  '<p>{s name=detail/debit1}Sie haben{/s} ' + positionIds.length + ' {s name=detail/debit2}Position(en) mit einem Gesamtbetrag von{/s} ' 
            + '<span style="color: red;">' + amount.toFixed(2) + ' ' + currency +' </span>{s name=detail/debit3}markiert{/s}.</p><br>'
            + '<p><label for="mopt_payone__capture_shipment">{s name=detail/debit4}Versandkosten mit gutschreiben{/s}</label>'
            + '<input type="checkbox" id="mopt_payone__debit_shipment" class="x-form-field x-form-checkbox"' 
            + 'style="margin: 0 0 0 4px; height: 15px !important; width: 15px !important;"/></p>' 
            + '<br><p>{s name=detail/debit5}Sind Sie sicher{/s}?</p>';
    
    if(!showShippingCostsCheckbox)
    {
      moptMessageBoxText =  '{s name=detail/debit1}Sie haben{/s} ' + positionIds.length + ' {s name=detail/debit2}Position(en) mit einem Gesamtbetrag von{/s} ' 
            + '<span style="color: red;">' + amount.toFixed(2) + ' ' + currency +' </span>{s name=detail/debit3}markiert{/s}. '
            + '<br> {s name=detail/debit5}Sind Sie sicher{/s}?';
    }
    
    Ext.MessageBox.confirm('{s name=detail/debit}Gutschrift{/s}', 
    moptMessageBoxText, function (response) {
      if ( response !== 'yes' ) {
        return;
      }
      var includeShipment = false;
      
      
      if (showShippingCostsCheckbox && Ext.get('mopt_payone__debit_shipment').dom.checked)
      {
				includeShipment = true;
			} 
      
      Ext.Ajax.request({
        url: '{url controller="MoptPayoneOrder" action="moptPayoneDebit"}',
        method: 'POST',
        params: { id: order.get('id'), positionIds: Ext.JSON.encode(positionIds), includeShipment: includeShipment},
        headers: { 'Accept': 'application/json'},
        success: function(response)
        {
          var jsonData = Ext.JSON.decode(response.responseText);
          if (jsonData.success)
          {
            Ext.Msg.alert('{s name=detail/debit}Gutschrift{/s}', '{s name=detail/debitSuccess}Die Gutschrift wurde erfolgreich durchgeführt.{/s}');

            //reload form
            options.callback(order);
          }
          else
          {
            Ext.Msg.alert('{s name=detail/debit}Gutschrift{/s}', jsonData.error_message);
          }
        }
      });
    });
  },
          
  onMoptPayoneCapturePositions: function(order, grid, options) {
    var me = this;
    var positionIds = me.moptPayoneGetPositionIdsFromGrid(grid);
    
    if(!positionIds){
      return;
    }
    
    var selectionModel = grid.getSelectionModel();
    var positions = selectionModel.getSelection();
    var amount = 0;
    var currency = order.raw.currency;
    
    
    for (var i = 0; i < positions.length; i++)
    {
      amount+=positions[i].get('total');
    }
    
    var details = order.raw.details;
    var showShippingCostsCheckbox = true;
    var paymentName = order.raw.payment.name;
    
    for (var i = 0; i < details.length; i++)
    {
      if (details[i].articleNumber === "SHIPPING")
      {
        showShippingCostsCheckbox = false;
      }
    }

    var moptMessageBoxText =  '<p>{s name=detail/debit1}Sie haben{/s} ' + positionIds.length 
            + ' {s name=detail/debit2}Position(en) mit einem Gesamtbetrag von{/s} <span style="color: red;">' 
              + amount.toFixed(2) + ' ' + currency +' </span> {s name=detail/debit3}markiert{/s}.</p><br>'
              + '<p><label for="mopt_payone__capture_shipment">{s name=detail/debit6}Versandkosten auch Einziehen{/s}</label>' 
              + '<input type="checkbox" id="mopt_payone__capture_shipment" class="x-form-field x-form-checkbox"' 
              + 'style="margin: 0 0 0 4px; height: 15px !important; width: 15px !important;" checked/>'
              + '</p><br>' 
              + '<p>{s name=detail/debit7}Welche Art des Zahlungseinzugs möchten Sie vornehmen{/s}?</p>';
    
    if(!showShippingCostsCheckbox)
    {
      moptMessageBoxText =  '{s name=detail/debit1}Sie haben{/s} ' + positionIds.length 
              + ' {s name=detail/debit2}Position(en) mit einem Gesamtbetrag von{/s} <span style="color: red;">' 
              + amount.toFixed(2) + ' ' + currency +' </span> {s name=detail/debit3}markiert{/s}. <br>'
              + ' {s name=detail/debit7}Welche Art des Zahlungseinzugs möchten Sie vornehmen{/s}?';
    }

    if (paymentName == 'mopt_payone__fin_paypal_installment'){
      var moptButtonText = { no: '{s name=detail/captureFinal}Finaler Geldeinzug{/s}', cancel: '{s name=detail/cancel}Abbrechen{/s}' }
    } else {
      var moptButtonText = { yes: '{s name=detail/capturePartly}(Teil-)Geldeinzug{/s}', no: '{s name=detail/captureFinal}Finaler Geldeinzug{/s}', cancel: '{s name=detail/cancel}Abbrechen{/s}' }
    }
    //bit wierd message-box... plausible way doesn't seem to work 
    //(see: http://stackoverflow.com/questions/12263291/extjs-4-or-4-1-messagebox-custom-buttons)
    Ext.MessageBox.show({
      title: '{s name=detail/captureConfirm}Zahlung einziehen{/s}',
      msg: moptMessageBoxText,
      buttonText: moptButtonText,
      fn: function(btn){
        
        var includeShipment = false;
        
         if (showShippingCostsCheckbox && Ext.get('mopt_payone__capture_shipment').dom.checked)
         {
          includeShipment = true;
         } 
        
        if(btn === 'yes') {
          me.moptPayoneCallCapture(order, positionIds, false, options, includeShipment);
        } else if (btn === 'no') {
          me.moptPayoneCallCapture(order, positionIds, true, options, includeShipment);
        } else {
          Ext.MessageBox.hide();
        }
      }
    });
  },
  
  moptPayoneCallCapture: function(order, positionIds, finalize, options, includeShipment) {
    Ext.Ajax.request({
      url: '{url controller="MoptPayoneOrder" action="moptPayoneCaptureOrder"}',
      method: 'POST',
      params: { id: order.get('id'), 
                positionIds: Ext.JSON.encode(positionIds), 
                finalize: finalize, 
                includeShipment: includeShipment},
      headers: { 'Accept': 'application/json'},
      success: function(response)
      {
        var jsonData = Ext.JSON.decode(response.responseText);
        if (jsonData.success)
        {
          Ext.Msg.alert('{s name=detail/captureMoney}Geldeinzug{/s}', '{s name=detail/captureSuccess}Der Geldeinzug wurde erfolgreich durchgeführt.{/s}');
          
          //reload form
          options.callback(order);
        }
        else
        {
          Ext.Msg.alert('{s name=detail/captureMoney}Geldeinzug{/s}', jsonData.error_message);
        }
      }
    });
  },
  
  moptPayoneGetPositionIdsFromGrid: function(grid) {
    
    var selectionModel = grid.getSelectionModel();
    var positions = selectionModel.getSelection();
    var positionIds = [];
    
    if (positions.length === 0) {
      return;
    }
    
    for (var i = 0; i < positions.length; i++)
    {
      positionIds.push(positions[i].get('id'));
    }
    
    return positionIds;
  }
  
});
//{/block}
