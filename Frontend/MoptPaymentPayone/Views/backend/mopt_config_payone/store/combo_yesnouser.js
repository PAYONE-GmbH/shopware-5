//{block name="backend/mopt_config_payone/store/combo_yesnouser"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboYesnouser', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'Ja',
      value: '0'
    }, 
    {
      display: 'Nein',
      value: '1'
    },
    {
      display: 'Benutzerentscheidung',
      value: '2'
    }
  ]
});
//{/block}