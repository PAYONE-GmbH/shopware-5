//{block name="backend/risk_management/view/risk_management/container" append}
Ext.define('Shopware.apps.RiskManagement.view.risk_management.MoptPayone__Container', {
  override: 'Shopware.apps.RiskManagement.view.risk_management.Container',
  
  createItems: function()
  {
    var me = this;
    
    //create store
    me.trafficLightStore = Ext.create('Shopware.apps.RiskManagement.store.TrafficLights');
    
    var items = me.callParent(arguments);
    
    //check if rule == mopt
    if(items[0].value == 'MOPT_PAYONE__TRAFFIC_LIGHT_IS' || items[0].value == 'MOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT')
    {
      var field1 = Ext.create('Ext.form.field.ComboBox', {
        store: me.trafficLightStore,
        displayField: 'description',
        valueField: 'value',
        editable: false,
        value: (me.values && me.values.value1) ? me.values.value1 : '',
        columnWidth: 0.1,
        style: {
          marginLeft: '10px'
        }
      });
      items[1] = field1;
    }
    
    if(items[3].value == 'MOPT_PAYONE__TRAFFIC_LIGHT_IS' || items[3].value == 'MOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT')
    {
      var field2 = Ext.create('Ext.form.field.ComboBox', {
        store: me.trafficLightStore,
        displayField: 'description',
        valueField: 'value',
        editable: false,
        value: (me.values && me.values.value2) ? me.values.value2 : '',
        columnWidth: 0.1,
        style: {
          marginLeft: '10px'
        }
      });
      items[4] = field2;
    }
    return items;
  }
});
//{/block}