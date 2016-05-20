//{block name="backend/mopt_config_payone/store/signal"}
Ext.define('Shopware.apps.MoptConfigPayone.store.Signal', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
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