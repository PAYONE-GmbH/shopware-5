Ext.define('Shopware.apps.MoptPayoneAmazonPay.store.Config', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayoneAmazonPay'
        };
    },
    model: 'Shopware.apps.MoptPayoneAmazonPay.model.Config'
});