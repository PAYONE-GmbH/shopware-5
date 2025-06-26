//{namespace name=backend/mopt_payone_amazon_pay/main}
Ext.define('Shopware.apps.MoptPayoneAmazonPay.view.list.Config', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-amazon-pay-listing-grid',
    region: 'center',
    snippets: {
        clientId: '{s name="amazon_clientid"}Client Id{/s}',
        sellerId: '{s name="amazon_sellerid"}Seller Id{/s}',
        buttonType: '{s name="amazon_buttontype"}Button Typ{/s}',
        buttonColor: '{s name="amazon_buttoncolor"}Button Farbe{/s}',
        /*
        buttonLanguage: '{s name="amazon_buttonlanguage"}Button Sprache{/s}',
        */
        amazonMode: '{s name="amazon_mode"}Amazon Modus{/s}',
        amazonDownloadButton: '{s name="amazon_download_button"}Konfigurations Download{/s}',
        shop: '{s name="shop"}Shop{/s}',
        button: '{s name="button"}Amazonpay Button{/s}'
    },

    configure: function () {
        var me = this;
        return {
            detailWindow: 'Shopware.apps.MoptPayoneAmazonPay.view.detail.Window',
            columns: {
                clientId: { header: 'Client Id' },
                sellerId: { header: 'Seller Id' },
                buttonType:  { header: 'Button Typ' },
                buttonColor: { header: 'Button Farbe' },
                /*
                buttonLanguage: { header: 'Button Sprache' },
                */
                amazonMode: { header: 'Amazon Modus' },
                shopId: { header: me.snippets.shop },
            }
        };
    },

    createToolbarItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items = Ext.Array.insert(
            items,
            2,
            [me.createToolbarButton()]
        );

        return items;
    },

    createToolbarButton: function () {
        var me = this;
        return Ext.create('Ext.button.Button', {
            text: me.snippets.amazonDownloadButton,
            handler: function () {
                me.fireEvent('download-config', me);
            }
        });
    }
});
