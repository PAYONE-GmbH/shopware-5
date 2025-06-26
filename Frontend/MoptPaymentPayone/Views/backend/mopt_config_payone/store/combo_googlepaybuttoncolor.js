//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_googlepaybuttoncolor"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboGooglepaybuttoncolor', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: '{s name="fieldvalue/default"}Default{/s}',
      value: 'default'
    },
    {
      display: '{s name="fieldvalue/white"}Weiss{/s}',
      value: 'white'
    },
    {
      display: '{s name="fieldvalue/black"}Schwarz{/s}',
      value: 'black'
    }
  ]
});
//{/block}