//{namespace name=backend/mopt_payone_ratepay/main}
Ext.define('Shopware.apps.MoptPayoneRatepay.view.detail.Config', {
    extend: 'Shopware.model.Container',
    padding: 20,

    configure: function () {
        var me = this;

        me.customStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'boolean', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: true, name: 'Lastschrift' },
                { value: false, name: 'Vorkasse' }
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
                            fieldLabel: 'Währung',
                            name: 'currencyId',
                            allowBlank: false
                        },
                        ratepayInstallmentMode: {
                            xtype: 'combobox',
                            fieldLabel: 'Ratenkauf Modus',
                            name: 'ratepayInstallmentMode',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },

                       countryCodeBilling: {
                            label: 'Land',
                            hidden: true
                        }                       
                    }                  
                }]
        };
    }
});
             
