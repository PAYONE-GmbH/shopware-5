//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_mistake"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboMistake', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptConfigPayone.model.Comboint',
  data: [
    {
      display: '{s name=fieldvalue/cancel}Vorgang abbrechen{/s}',
      value: '0'
    },
    {
      display: '{s name=fieldvalue/reenter}Neueingabe der Adresse die zum Fehler geführt hat{/s}',
      value: '1'
    },
    {
      display: '{s name=fieldvalue/doconsumerscore}Anschließende Bonitätsprüfung durchführen{/s}',
      value: '2'
    },
    {
      display: '{s name=fieldvalue/continue}fortfahren{/s}',
      value: '3'
    }
  ]
});
//{/block}