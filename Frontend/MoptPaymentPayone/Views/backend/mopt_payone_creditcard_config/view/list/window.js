//{namespace name=backend/mopt_payone_creditccard_config/main}
Ext.define('Shopware.apps.MoptPayoneCreditcardConfig.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.mopt-payone-creditcard-config-list-window',
    height: 450,
    width: 600,
    title : '{s name="window/title"}Payone Kreditkartenkonfiguration{/s}',
 
    configure: function() {
        return {
            listingGrid: 'Shopware.apps.MoptPayoneCreditcardConfig.view.list.Creditcardconfig',
            listingStore: 'Shopware.apps.MoptPayoneCreditcardConfig.store.Creditcardconfig'
        };
    }
});