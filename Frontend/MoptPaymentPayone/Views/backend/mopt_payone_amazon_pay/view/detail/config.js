//{namespace name=backend/mopt_payone_amazon_pay/main}
Ext.define('Shopware.apps.MoptPayoneAmazonPay.view.detail.Config', {
    extend: 'Shopware.model.Container',
    padding: 20,

    configure: function () {
        var me = this;
        /** global: Ext */
        me.buttonTypeStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'string', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: 'PwA', name: '{s name=amazon_buttontype_amazonpay}Amazon Pay (Default){/s}' },
                { value: 'Pay', name: '{s name=amazon_buttontype_pay}Pay{/s}' },
                { value: 'A', name: '{s name=amazon_buttontype_a}A{/s}' }
            ]
        });
        me.buttonColorStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'string', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: 'Gold', name: '{s name=amazon_buttoncolor_gold}Gold (default){/s}' },
                { value: 'LightGray', name: '{s name=amazon_buttoncolor_lightgray}Light gray{/s}' },
                { value: 'DarkGray', name: '{s name=amazon_buttoncolor_darkgray}Dark gray{/s}' }
            ]
        });
        /*
        me.buttonLanguageStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'string', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: 'none', name: '{s name=amazon_buttonlanguage_autodetect}Autodetect (default){/s}' },
                { value: 'en-GB', name: '{s name=amazon_buttonlanguage_en-GB}English (UK){/s}' },
                { value: 'de-DE', name: '{s name=amazon_buttonlanguage_de-DE}German (Germany){/s}' },
                { value: 'fr-FR', name: '{s name=amazon_buttonlanguage_fr-FR}French (France){/s}' },
                { value: 'it-IT', name: '{s name=amazon_buttonlanguage_it-IT}Italian (Italy){/s}' },
                { value: 'es-ES', name: '{s name=amazon_buttonlanguage_es-ES}Spanish (Spain){/s}' }
            ]
        });
        */
        me.amazonModeStore =  Ext.create('Ext.data.Store', {
            fields: [
                { type: 'string', name: 'value' },
                { type: 'string', name: 'name' }
            ],
            data: [
                { value: 'sync', name: '{s name=amazon_mode_always_sync}Always Synchronous{/s}' },
                { value: 'firstsync', name: '{s name=amazon_mode_firstsync}Asynchronous On Failure (default):{/s}'}
            ]
        });


        return {
            controller: 'MoptPayoneAmazonPay',
            fieldSets: [{
                title: 'Payone Amazon Pay Details',
                layout: 'fit',
                fields: {
                    clientId: { disabled: true },
                    sellerId: { disabled: true },
                    buttonType: {
                        xtype: 'combobox',
                        fieldLabel: '{s name=amazon_buttontype}Button Type{/s}',
                        name: 'buttonType',
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'value',
                        helpText: 'Amazon Pay (Default): Typical "Amazon Pay" button<BR>Pay: A slightly smaller "Pay" button<BR>A: A small button with only the Amazon Pay Logo',
                        editable: false,
                        allowBlank: false,
                        store: me.buttonTypeStore
                    },
                    buttonColor: {
                        xtype: 'combobox',
                        fieldLabel: '{s name=amazon_buttoncolor}Button Color{/s}',
                        name: 'buttonColor',
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'value',
                        editable: false,
                        allowBlank: false,
                        store: me.buttonColorStore
                    },
                    /*
                    buttonLanguage: {
                        xtype: 'combobox',
                        fieldLabel: '{s name=amazon_buttonlanguage}Button Language{/s}',
                        name: 'buttonLanguage',
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'value',
                        editable: false,
                        allowBlank: false,
                        store: me.buttonLanguageStore
                    },
                   */
                    amazonMode: {
                        xtype: 'combobox',
                        fieldLabel: '{s name=amazon_mode}Amazon Mode{/s}',
                        name: 'amazonMode',
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'value',
                        helpText: '– Asynchronous On Failure (recommended, default): If a synchronous transaction fails, the transaction will be retried with an asynchronous request.<BR>' +
                        '– Always Synchronous: Always try synchronous transactions. These might have lower acceptance rates with Amazon. However, you will receive an instant confirmation of the transaction and you will be able to continue right away.<BR>',
                        editable: false,
                        allowBlank: false,
                        store: me.amazonModeStore
                    }
                }
            }]
        };
    }
});
             
