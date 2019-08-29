Ext.define('Shopware.apps.MoptPayonePayDirekt.view.list.Button', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-pay-direkt-listing-grid',
    region: 'center',
    snippets: {
        language: 'Sprache',
        dispatch: 'Versandart',
        button: '{s name=button}PayDirekt Button{/s}'
    },
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.MoptPayonePayDirekt.view.detail.Window',
            columns: {
                localeId: { header: me.snippets.language },
                dispatchId: { header: me.snippets.dispatch},
                image: { header: me.snippets.button },
            }
        };
    }
    
});
