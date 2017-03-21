//{namespace name=backend/mopt_payone_ratepay/main}
Ext.define('Shopware.apps.MoptPayoneRatepay.view.list.Config', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-ratepay-listing-grid',
    region: 'center',
    snippets: {
    },
    configure: function () {
        return {
            detailWindow: 'Shopware.apps.MoptPayoneRatepay.view.detail.Window',
            columns: {
                shopid: { header: 'Shop ID' },  
                currencyId: { header: 'Währung' },
                ratepayInstallmentMode: {
                    header: 'Ratenkauf Modus',
                    renderer: function (value) {
                        if (value == false) {
                            return 'Vorkasse';
                        } else {
                            return 'Lastschrift'
                        }
                    }
                },
                countryCodeBilling: { header: 'Land'}
                
            }
        };
    },

    createToolbarItems: function() {
        var me = this,
            items = me.callParent(arguments);

        items = Ext.Array.insert(
            items,
            2,
            [ me.createToolbarButton() ]
        );

        return items;
    },

    createToolbarButton: function() {
        var me = this;
        return Ext.create('Ext.button.Button', {
            text: 'Ratepay Konfiguration abrufen',
            handler: function() {
                me.fireEvent('download-config', me);
            }
        });
    },  
});
