//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/signal"}
Ext.define('Shopware.apps.MoptConfigPayone.store.Signal', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
  data: [
    {
      display: '{s name="fieldvalue/red"}rot{/s}',
      value: 0
    },
    {
      display: '{s name="fieldvalue/yellow"}Gelb{/s}',
      value: 1
    },
    {
      display: '{s name="fieldvalue/green"}Grün{/s}',
      value: 2
    }
  ]
});
//{/block}