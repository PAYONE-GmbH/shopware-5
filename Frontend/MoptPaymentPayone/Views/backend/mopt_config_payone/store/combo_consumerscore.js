//{block name="backend/mopt_config_payone/store/combo_consumerscore"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboConsumerscore', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'Vorgang abbrechen',
      value: '0'
    },
    {
      display: 'fortfahren',
      value: '1'
    }
  ]
});
//{/block}