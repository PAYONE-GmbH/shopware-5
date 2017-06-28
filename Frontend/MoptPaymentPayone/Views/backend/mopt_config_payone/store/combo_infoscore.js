//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_infoscore"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboInfoscore', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
  data: [
    {
      display: '{s name=fieldvalue/IH}Infoscore (harte Kriterien){/s}',
      value: 'IH'
    },
    {
      display: '{s name=fieldvalue/IA}Infoscore (alle Merkmale){/s}',
      value: 'IA'
    },
    {
      display: '{s name=fieldvalue/IB}Infoscore (alle Merkmale + Boniscore){/s}',
      value: 'IB'
    },
    {
      display: '{s name=fieldvalue/CE}Boniversum VERITA Score{/s}',
      value: 'CE'
    }
  ]
});
//{/block}
