/**
 * $Id: $
 */

//{block name="backend/mopt_config_payone/model/signal"}
Ext.define('Shopware.apps.MoptConfigPayone.model.Signal', {
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