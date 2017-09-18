//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_terms"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboTerms', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name=fieldvalue/off}Aus{/s}',
      value: 0
    },
    {
      display: '{s name=fieldvalue/onconfirm}Auf der Confirm Seite{/s}',
      value: 1
    },
    {
      display: '{s name=fieldvalue/global}Global{/s}',
      value: 2
    }
  ]
});
//{/block}
