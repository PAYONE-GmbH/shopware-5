//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_paypalv2expressbuttonshape"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboPaypalv2expressbuttonshape', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: '{s name="fieldvalue/rect"}Standard{/s}',
      value: 'rect'
    },
    {
      display: '{s name="fieldvalue/pill"}Runde Ecken{/s}',
      value: 'pill'
    },
    {
      display: '{s name="fieldvalue/sharp"}Spitze Ecken{/s}',
      value: 'sharp'
    }
  ]
});
//{/block}