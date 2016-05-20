//{block name="backend/mopt_config_payone/store/combo_mistake"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboMistake', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: 'Vorgang abbrechen',
      value: '0'
    },
    {
      display: 'Neueingabe der Adresse die zum Fehler geführt hat',
      value: '1'
    },
    {
      display: 'Anschließende Bonitätsprüfung durchführen',
      value: '2'
    },
    {
      display: 'fortfahren',
      value: '3'
    }
  ]
});
//{/block}