Ext.define('Shopware.apps.MoptPayoneRatepay.store.Config', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayoneRatepay'
        };
    },
    model: 'Shopware.apps.MoptPayoneRatepay.model.Config'
});