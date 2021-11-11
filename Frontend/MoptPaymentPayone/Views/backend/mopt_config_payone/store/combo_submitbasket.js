//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_submitbasket"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboSubmitbasket', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
  data: [
    {
      display: '{s name="fieldvalue/no"}Nein{/s}',
      value: false
    }, {
      display: '{s name="fieldvalue/yes"}Ja{/s}',
      value: true
    }]
});
//{/block}