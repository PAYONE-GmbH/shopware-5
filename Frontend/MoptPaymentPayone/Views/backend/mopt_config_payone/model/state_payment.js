//{block name="backend/mopt_config_payone/model/state_payment"}
Ext.define('Shopware.apps.MoptConfigPayone.model.StatePayment', {
  extend: 'Ext.data.Model',
  fields: [
    {
      name: 'id', 
      type: 'integer'
    },
    {
      name: 'description',
      type: 'string'
    }
  ]
 
  
});
//{/block}