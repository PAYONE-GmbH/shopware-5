//{block name="backend/mopt_payone_ratepay/model/combo"}
Ext.define('Shopware.apps.MoptPayoneRatepay.model.Combo', {
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