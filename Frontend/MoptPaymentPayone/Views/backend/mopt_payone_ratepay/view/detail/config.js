//{namespace name=backend/mopt_payone_ratepay/main}
Ext.define('Shopware.apps.MoptPayoneRatepay.view.detail.Config', {
    extend: 'Shopware.model.Container',
    padding: 20,
    configure: function () {

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
                            fieldLabel: 'Ratenkauf Modus',
                            name: 'ratepayInstallmentMode',
                            allowBlank: false
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
             
