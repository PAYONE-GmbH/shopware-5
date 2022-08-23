//{namespace name=backend/mopt_payone_paypal/main}
Ext.define('Shopware.apps.MoptPayonePaypal.view.list.Button', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-paypal-listing-grid',
    region: 'center',
    snippets: {
        shop: '{s name="shop"}Shop{/s}',
        button: '{s name="button"}PayPal Button{/s}',
    },
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.MoptPayonePaypal.view.detail.Window',
            columns: {
                shopId: { header: me.snippets.shop },
                image: { header: me.snippets.button }
            }
        };
    }
    
});
