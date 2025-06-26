//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_consumerscore"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboConsumerscore', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name="fieldvalue/cancel"}abbrechen{/s}',
      value: '0'
    },
    {
      display: '{s name="fieldvalue/continue"}fortfahren{/s}',
      value: '1'
    }
  ]
});
//{/block}