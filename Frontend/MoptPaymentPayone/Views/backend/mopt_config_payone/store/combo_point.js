//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_point"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboPoint', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name=fieldvalue/beforepaymentchoice}Vor der Zahlartenauswahl{/s}',
      value: 0
    },
    {
      display: '{s name=fieldvalue/afterpaymentchoice}Nach der Zahlartenauswahl{/s}',
      value: 1
    }
  ]
});
//{/block}