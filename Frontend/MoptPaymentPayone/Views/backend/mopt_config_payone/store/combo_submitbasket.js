//{block name="backend/mopt_config_payone/store/combo_submitbasket"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboSubmitbasket', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
  data: [
    {
      display: 'Nein',
      value: false
    }, {
      display: 'Ja',
      value: true
    }]
});
//{/block}