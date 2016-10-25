//{namespace name=backend/mopt_payone_ratepay/main}
Ext.define('Shopware.apps.MoptPayoneRatepay.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.mopt-payone-ratepay-list-window',
    height: 450,
    width: 600,
    title : '{s name=window/title}Payone Ratepay{/s}',
 
    configure: function() {
        return {
            listingGrid: 'Shopware.apps.MoptPayoneRatepay.view.list.Config',
            listingStore: 'Shopware.apps.MoptPayoneRatepay.store.Config'
        };        
    }     
});
