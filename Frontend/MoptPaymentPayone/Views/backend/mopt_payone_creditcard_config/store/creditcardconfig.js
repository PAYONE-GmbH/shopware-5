Ext.define('Shopware.apps.MoptPayoneCreditcardConfig.store.Creditcardconfig', {
    extend:'Shopware.store.Listing',
 
    configure: function() {
        return {
            controller: 'MoptPayoneCreditcardConfig'
        };
    },
    model: 'Shopware.apps.MoptPayoneCreditcardConfig.model.Creditcardconfig'
});