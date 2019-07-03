Ext.define('Shopware.apps.MoptPayonePayDirekt.view.list.Button', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-pay-direkt-listing-grid',
    region: 'center',
    snippets: {
        language: '{s name=localeId}localeId{/s}',
        button: '{s name=image}Pay Direkt image{/s}',
    },
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.MoptPayonePayDirekt.view.detail.Window',
            columns: {
                localeId: { header: me.snippets.localeId },
                image: { header: me.snippets.image },
            }
        };
    }
    
});
