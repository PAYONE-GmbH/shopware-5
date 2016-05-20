//{block name="backend/mopt_config_payone/store/combo_yesno"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboYesno', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
  data: [
    {
      display: 'Ja',
      value: 'true'
    }, {
      display: 'Nein',
      value: 'false'
    }]
});
//{/block}