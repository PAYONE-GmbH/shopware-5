//{block name="backend/mopt_config_payone/model/combo"}
Ext.define('Shopware.apps.MoptConfigPayone.model.Combo', {
  extend: 'Ext.data.Model',
  fields: [
    {
      name: 'display', 
      type: 'string'
    },
    {
      name: 'value', 
      type: 'boolean'
    }
  ],
  
  isField: true
  
});
//{/block}