//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/auth"}
Ext.define('Shopware.apps.MoptConfigPayone.store.Auth', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: '{s name="fieldvalue/preauthorisation"}Vorautorisierung{/s}',
      value: 'Vorautorisierung'
    }, {
      display: '{s name="fieldvalue/authorisation"}Autorisierung{/s}',
      value: 'Autorisierung'
    }]
});
//{/block}