Ext.define('Shopware.apps.MoptPayonePayDirekt.store.Button', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayonePayDirekt'
        };
    },
    model: 'Shopware.apps.MoptPayonePayDirekt.model.Button'
});