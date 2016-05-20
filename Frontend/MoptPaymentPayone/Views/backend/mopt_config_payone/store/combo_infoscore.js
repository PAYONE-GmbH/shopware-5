//{block name="backend/mopt_config_payone/store/combo_infoscore"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboInfoscore', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: 'Infoscore (harte Kriterien)',
      value: 'IH'
    },
    {
      display: 'Infoscore (alle Merkmale)',
      value: 'IA'
    },
    {
      display: 'Infoscore (alle Merkmale + Boniscore)',
      value: 'IB'
    }
  ]
});
//{/block}