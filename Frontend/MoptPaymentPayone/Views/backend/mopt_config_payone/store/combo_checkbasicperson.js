//{block name="backend/mopt_config_payone/store/combo_checkbasicperson"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboCheckbasicperson', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'nicht prüfen',
      value: '0'
    }, 
    {
      display: 'Basic',
      value: '1'
    },
    {
      display: 'Person',
      value: '2'
    }
  ]
});
//{/block}