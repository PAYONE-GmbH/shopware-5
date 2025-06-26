/**
 * $Id: $
 */

//{block name="backend/mopt_config_payone/model/config"}
Ext.define('Shopware.apps.MoptConfigPayone.model.Payment', {
  extend: 'Ext.data.Model',
  fields: [
    {
      name: 'id', 
      type: 'int'
    },
    {
      name: 'description', 
      type: 'string'
    },
    {
      name: 'name', 
      type: 'string'
    },
    {
      name: 'configSet', 
      type: 'int'
    }
  ]
});
//{/block}