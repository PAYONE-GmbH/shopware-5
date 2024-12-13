//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_paypalv2expressbuttoncolor"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboPaypalv2expressbuttoncolor', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: '{s name="fieldvalue/gold"}Gold{/s}',
      value: 'gold'
    },
    {
      display: '{s name="fieldvalue/blue"}Blau{/s}',
      value: 'blue'
    },
    {
      display: '{s name="fieldvalue/silver"}Silber{/s}',
      value: 'silver'
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