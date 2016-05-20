//{block name="backend/mopt_config_payone/store/combo_terms"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboTerms', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'Aus',
      value: 0
    },
    {
      display: 'Auf der Confirm Seite',
      value: 1
    },
    {
      display: 'Global',
      value: 2
    }
  ]
});
//{/block}
