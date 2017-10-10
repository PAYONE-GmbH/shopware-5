//{namespace name=backend/mopt_payone_amazon_pay/main}
Ext.define('Shopware.apps.MoptPayoneAmazonPay.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.mopt-payone-amazon-pay-list-window',
    height: 450,
    width: 600,
    title : '{s name=window/title}Payone Amazon Pay{/s}',
 
    configure: function() {
        return {
            listingGrid: 'Shopware.apps.MoptPayoneAmazonPay.view.list.Config',
            listingStore: 'Shopware.apps.MoptPayoneAmazonPay.store.Config'
        };        
    }     
});
