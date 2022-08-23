//{namespace name=backend/mopt_payone_paypal/main}
Ext.define('Shopware.apps.MoptPayonePaypal.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.mopt-payone-paypal-list-window',
    height: 450,
    width: 600,
    title : '{s name="window/title"}Payone PayPal Express{/s}',
 
    configure: function() {
        return {
            listingGrid: 'Shopware.apps.MoptPayonePaypal.view.list.Button',
            listingStore: 'Shopware.apps.MoptPayonePaypal.store.Button'
        };
    }
});
