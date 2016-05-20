//{block name="backend/mopt_config_payone/store/combo_accountcheck"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboAccountcheck', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'nicht durchführen',
      value: '0'
    }, 
    {
      display: 'Basic',
      value: '1'
    },
    {
      display: 'Prüfung gegen die POS-Sperrliste',
      value: '2'
    }
  ]
});
//{/block}