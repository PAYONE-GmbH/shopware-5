//{block name="backend/risk_management/app" append}
Ext.define('Shopware.apps.RiskManagement.controller.MoptPayone__Main', {
  override: 'Shopware.apps.RiskManagement.controller.Main',
  init: function() {
    var me = this;
    //register store
    me.subApplication.trafficLightStore = me.subApplication.getStore('TrafficLights').load();
    me.callParent(arguments);
  }
});
//{/block }