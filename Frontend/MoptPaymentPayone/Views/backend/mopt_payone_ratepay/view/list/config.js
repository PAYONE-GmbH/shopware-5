//{namespace name=backend/mopt_payone_ratepay/main}
Ext.define('Shopware.apps.MoptPayoneRatepay.view.list.Config', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-ratepay-listing-grid',
    region: 'center',
    snippets: {
        currency: '{s name=currency}Währung{/s}',
        ratepaybutton: '{s name=ratepaybutton}Ratepay Konfiguration abrufen{/s}',
        ratepaymode: '{s name=ratepaymode}Ratenkauf Modus{/s}',
        country: '{s name=country}Land{/s}',
        vorkasse: '{s name=vorkasse}Vorkasse{/s}',
        lastschrift: '{s name=lastschrift}Lastschrift{/s}'
    },
    configure: function () {
        var me = this;
        return {
            detailWindow: 'Shopware.apps.MoptPayoneRatepay.view.detail.Window',
            columns: {
                shopid: { header: 'Shop ID' },  
                currencyId: { header: me.snippets.currency },
                ratepayInstallmentMode: {
                    header: me.snippets.ratepaymode,
                    renderer: function (value) {
                        if (value == false) {
                            return me.snippets.vorkasse;
                        } else {
                            return me.snippets.lastschrift;
                        }
                    }
                },
                countryCodeBilling: { header: me.snippets.country}
                
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
            text: me.snippets.ratepaybutton,
            handler: function() {
                me.fireEvent('download-config', me);
            }
        });
    },  
});
