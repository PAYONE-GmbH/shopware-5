//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_googlepaybuttontype"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboGooglepaybuttontype', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: '{s name="fieldvalue/buy"}Buy{/s}',
      value: 'buy'
    },
    {
      display: '{s name="fieldvalue/checkout"}Checkout{/s}',
      value: 'checkout'
    },
    {
      display: '{s name="fieldvalue/order"}Order{/s}',
      value: 'order'
    },
    {
      display: '{s name="fieldvalue/pay"}Pay{/s}',
      value: 'pay'
    },
    {
      display: '{s name="fieldvalue/plain"}Plain{/s}',
      value: 'plain'
    }
  ]
});
//{/block}