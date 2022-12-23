//{namespace name=backend/mopt_risk_management/main}
//{block name="backend/risk_management/store/risks" append}
Ext.define('Shopware.apps.RiskManagement.store.MoptPayone__Risks', {

    override: 'Shopware.apps.RiskManagement.store.Risks',
    
    constructor: function() 
    {
      var me = this;
      
      if(!me.mopt_payone__isExtended())
      {
        me.data.push({ description: '{s name="risks_store/comboBox/moptTrafficLight"}Ampel IST{/s}', value: 'MOPT_PAYONE__TRAFFIC_LIGHT_IS' });
        me.data.push({ description: '{s name="risks_store/comboBox/moptTrafficLightNot"}Ampel IST NICHT{/s}', value: 'MOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT' });
      }
      
      me.callParent(arguments);
    },
            
    mopt_payone__isExtended: function()
    {
      var me = this;
      
      for (var i = 0; i < me.data.length; i++)
      {
        if (me.data[i].value.indexOf('MOPT_PAYONE__') == 0)
        {
          return true;
        }
      }
      
      return false;
    }
});
//{/block}