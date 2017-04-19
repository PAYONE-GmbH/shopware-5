//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_signal"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboSignal', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name=fieldvalue/red}rot{/s}',
      value: 0
    },
    {
      display: '{s name=fieldvalue/yellow}Gelb{/s}',
      value: 1
    },
    {
      display: '{s name=fieldvalue/green}Grün{/s}',
      value: 2
    }
  ]
});
//{/block}