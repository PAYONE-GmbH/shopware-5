//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_checkbasicperson"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboCheckbasicperson', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name=fieldvalue/donotcheck}nicht durchführen{/s}',
      value: '0'
    }, 
    {
      display: '{s name=fieldvalue/basic}Basic{/s}',
      value: '1'
    },
    {
      display: '{s name=fieldvalue/person}Person{/s}',
      value: '2'
    }
  ]
});
//{/block}