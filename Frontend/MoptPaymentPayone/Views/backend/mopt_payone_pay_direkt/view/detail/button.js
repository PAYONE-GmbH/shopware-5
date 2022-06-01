Ext.define('Shopware.apps.MoptPayonePayDirekt.view.detail.Button', {
    extend: 'Shopware.model.Container',
    padding: 20,
    configure: function () {
        var me = this;
        /** global: Ext */

        me.packStationModeStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'string', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: 'allow', name: '{s name="packStation/allow"}Erlauben{/s}' },
                { value: 'deny', name: '{s name="packStation/deny"}Verbieten{/s}' },
            ]
        });

        return {
            controller: 'MoptPayonePayDirekt',
            fieldSets: [{
                    title: '{s name="details/title"}Payone Pay Direkt Button-Details{/s}',
                    layout: 'fit',
                    fields: {
                        shopId: {
                            fieldLabel: '{s name="shop"}Shop{/s}',
                            name: 'shopId',
                            allowBlank: false
                        },
                        packStationMode: {
                            xtype: 'combobox',
                            fieldLabel: 'Packstation mode',
                            name: 'packStationMode',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            helpText: '– Allow (recommended, default): allow packstation as shipping address for Paydirekt Express.<BR>' +
                                '– Deny: deny packstation as shipping address for Paydirekt Express. <BR>',
                            allowBlank: true,
                            store: me.packStationModeStore
                        },
                        dispatchId: {
                            fieldLabel: '{s name="dispatch"}Versandart{/s}',
                            name: 'dispatchId',
                            allowBlank: false
                        },
                        image: {
                            fieldLabel: '{s name="image"}Paydirekt image{/s}',
                            xtype: 'mediaselectionfield',
                            allowBlank: false
                        }
                    }
                }]
        };
    }
});
             