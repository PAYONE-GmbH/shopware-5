//{namespace name=backend/mopt_payone_paypal/main}
Ext.define('Shopware.apps.MoptPayonePaypal.view.detail.Button', {
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
                { value: 'allow', name: '{s name=packStation/allow}Erlauben{/s}' },
                { value: 'deny', name: '{s name=packStation/deny}Verbieten{/s}' },
            ]
        });


        return {
            controller: 'MoptPayonePaypal',
            fieldSets: [{
                title: '{s name=details/title}Payone PayPal Button-Details{/s}',
                layout: 'fit',
                fields: {
                    localeId: {
                        fieldLabel: '{s name=language}Sprache{/s}',
                        name: 'localeId',
                        allowBlank: false
                    },
                    packStationMode: {
                        xtype: 'combobox',
                        fieldLabel: 'Packstation mode',
                        name: 'packStationMode',
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'value',
                        helpText: '– Allow (recommended, default): allow packstation as shipping address for PayPal Express.<BR>' +
                            '– Deny: deny packstation as shipping address for PayPal Express. <BR>',
                        allowBlank: true,
                        store: me.packStationModeStore
                    },
                    image: {
                        fieldLabel: '{s name=button}PayPal Button{/s}',
                        xtype: 'mediaselectionfield',
                        allowBlank: false
                    },
                    isDefault: '{s name=default}Default{/s}'
                }
            }]
        };
    }
});
             