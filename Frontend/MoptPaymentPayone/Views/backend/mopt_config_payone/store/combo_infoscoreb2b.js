//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_infoscoreb2b"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboInfoscoreb2b', {
    extend: 'Ext.data.Store',
    model: 'Shopware.apps.MoptConfigPayone.model.Combostring',
    data: [
        {
            display: '{s name=fieldvalue/SFS}Schufa{/s}',
            value: 'SFS'
        },
        {
            display: '{s name=fieldvalue/NONE}Keine Prüfung{/s}',
            value: 'NO'
        }
    ]
});
//{/block}
