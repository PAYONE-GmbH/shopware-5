//{block name="backend/risk_management/app" append}
Ext.define('Shopware.apps.RiskManagement.controller.MoptPayone__RiskManagement', {
  override: 'Shopware.apps.RiskManagement.controller.RiskManagement',
  
  //replace text-input with combobox
  onChangeRisk: function(comboBox, newValue, indexOfNextItem)
  {
    var me = this;
    
    if(newValue == 'MOPT_PAYONE__TRAFFIC_LIGHT_IS' || newValue=='MOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT')
    {
      var nextItem = comboBox.up('container').items.items[indexOfNextItem];
      var newComboBox = Ext.create('Ext.form.field.ComboBox', {
        store: me.subApplication.trafficLightStore,
        displayField: 'description',
        valueField: 'value',
        editable: false,
        columnWidth: 0.1,
        style: {
          marginLeft: '10px'
        }
      });
      comboBox.up('container').remove(nextItem);
      comboBox.up('container').insert(indexOfNextItem, newComboBox);
      nextItem.show();
    }
    else
    {
      me.callParent(arguments);
    }
  }
});
//{/block }