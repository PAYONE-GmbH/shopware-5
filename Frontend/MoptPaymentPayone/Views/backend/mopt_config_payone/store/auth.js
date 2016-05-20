//{block name="backend/mopt_config_payone/store/auth"}
Ext.define('Shopware.apps.MoptConfigPayone.store.Auth', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: 'Vorautorisierung',
      value: 'Vorautorisierung'
    }, {
      display: 'Autorisierung',
      value: 'Autorisierung'
    }]
});
//{/block}