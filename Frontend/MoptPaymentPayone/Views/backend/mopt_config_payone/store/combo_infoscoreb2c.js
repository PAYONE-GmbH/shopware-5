//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/store/combo_infoscoreb2c"}
Ext.define('Shopware.apps.MoptConfigPayone.store.ComboInfoscoreb2c', {
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
        },
        {
            display: '{s name=fieldvalue/NONE}Keine Prüfung{/s}',
            value: 'NONE'
        }
    ]
});
//{/block}
