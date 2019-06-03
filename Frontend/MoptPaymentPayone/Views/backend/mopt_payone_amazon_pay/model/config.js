Ext.define('Shopware.apps.MoptPayoneAmazonPay.model.Config', {
    extend: 'Shopware.data.Model',
    configure: function () {
        return {
            controller: 'MoptPayoneAmazonPay',
            detail: 'Shopware.apps.MoptPayoneAmazonPay.view.detail.Config'
        };
    },

    fields: [
        { name: 'clientId', type: 'string' },
        { name: 'sellerId', type: 'string' },
        { name: 'buttonType', type: 'string' },
        { name: 'buttonColor', type: 'string' },
        { name: 'buttonLanguage', type: 'string' },
        { name: 'amazonMode', type: 'string' },
        { name: 'packStationMode', type: 'string' }
    ]
});
 
