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
                    }
                }]
        };
    }
});
             