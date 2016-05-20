//{namespace name=backend/mopt_payone_paypal/main}
Ext.define('Shopware.apps.MoptPayonePaypal.view.list.Button', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-paypal-listing-grid',
    region: 'center',
    snippets: {
        language: '{s name=language}Sprache{/s}',
        button: '{s name=button}PayPal Button{/s}',
        default: '{s name=default}Default{/s}'
    },
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.MoptPayonePaypal.view.detail.Window',
            columns: {
                localeId: { header: me.snippets.language },
                image: { header: me.snippets.button },
                isDefault: { header: me.snippets.default, width: 90, flex: 0 }
            }
        };
    }
    
});
