//{namespace name=backend/mopt_payone_ratepay/main}
/** global: Ext */
Ext.define('Shopware.apps.MoptPayoneRatepay.view.detail.Config', {
    extend: 'Shopware.model.Container',
    padding: 20,

    configure: function () {
        var me = this;
        /** global: Ext */
        me.customStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'boolean', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: true, name: '{s name=lastschrift}Lastschrift{/s}' },
                { value: false, name: '{s name=vorkasse}Vorkasse{/s}' }
            ]
        });

        return {
            controller: 'MoptPayoneRatepay',
            fieldSets: [{
                    title: 'Payone Ratepay Details',
                    layout: 'fit',
                    fields: {
                       shopid: {},
                       currencyId: {
                            fieldLabel: '{s name=currency}Währung{/s}',
                            name: 'currencyId',
                            allowBlank: false
                        },
                        ratepayInstallmentMode: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=ratepaymode}Ratenkauf Modus{/s}',
                            name: 'ratepayInstallmentMode',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },

                       countryCodeBilling: {
                            label: '{s name=country}Land{/s}',
                            hidden: true
                        }                       
                    }                  
                }]
        };
    }
});
             
