//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_testlive"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboTestlive', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combo',
  data: [
    {
      value: true,
      display: 'Live'
    },
    {
      value: false,
      display: 'Test'
    }
  ]
});
//{/block}
