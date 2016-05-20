//{block name="backend/mopt_config_payone/store/combo_point"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboPoint', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'Vor der Zahlartenauswahl',
      value: 0
    },
    {
      display: 'Nach der Zahlartenauswahl',
      value: 1
    }
  ]
});
//{/block}