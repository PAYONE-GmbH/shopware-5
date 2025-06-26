Ext.define('Shopware.apps.MoptPayonePaypal.store.Button', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayonePaypal'
        };
    },
    model: 'Shopware.apps.MoptPayonePaypal.model.Button'
});