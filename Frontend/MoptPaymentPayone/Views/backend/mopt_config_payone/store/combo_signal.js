//{block name="backend/mopt_config_payone/store/combo_signal"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboSignal', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'Rot',
      value: 0
    },
    {
      display: 'Gelb',
      value: 1
    },
    {
      display: 'Grün',
      value: 2
    }
  ]
});
//{/block}