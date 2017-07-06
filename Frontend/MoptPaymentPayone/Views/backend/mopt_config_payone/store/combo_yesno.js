//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_yesno"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboYesno', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
  data: [
    {
      display: '{s name=fieldvalue/yes}Ja{/s}',
      value: 'true'
    }, {
      display: '{s name=fieldvalue/no}Nein{/s}',
      value: 'false'
    }]
});
//{/block}