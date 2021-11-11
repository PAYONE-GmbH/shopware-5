Ext.define('Shopware.apps.MoptPayonePayDirekt.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.mopt-payone-pay-direkt-list-window',
    height: 450,
    width: 600,
    title : '{s name="window/title"}Payone Pay Direkt{/s}',
 
    configure: function() {
        return {
            listingGrid: 'Shopware.apps.MoptPayonePayDirekt.view.list.Button',
            listingStore: 'Shopware.apps.MoptPayonePayDirekt.store.Button'
        };
    }
});