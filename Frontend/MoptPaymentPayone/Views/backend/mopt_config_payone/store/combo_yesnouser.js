//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_yesnouser"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboYesnouser', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name="fieldvalue/yes"}Ja{/s}',
      value: '0'
    }, 
    {
      display: '{s name="fieldvalue/no"}Nein{/s}',
      value: '1'
    },
    {
      display: '{s name="fieldvalue/userchoice"}Benutzerentscheidung{/s}',
      value: '2'
    }
  ]
});
//{/block}