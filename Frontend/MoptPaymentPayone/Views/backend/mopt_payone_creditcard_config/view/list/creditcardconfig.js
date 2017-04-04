//{namespace name=backend/mopt_payone_creditcard_config/main}
Ext.define('Shopware.apps.MoptPayoneCreditcardConfig.view.list.Creditcardconfig', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.mopt-payone-creditcard-config-listing-grid',
    region: 'center',
    snippets: {
        shop: '{s name=shop}Shop{/s}',
        integrationType: '{s name=integrationType}Anfragetyp{/s}',
        isDefault: '{s name=isDefault}Default{/s}'
    },
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.MoptPayoneCreditcardConfig.view.detail.Window',
            columns: {
                shopId: { header: me.snippets.shop },
                integrationType: { 
                    header: me.snippets.integrationType,
                    renderer: function (value) {
                    if(value === 0) {
                        return '{s name=integrationTypeHostedIframe}hosted-iFrame{/s}';
                    } else {
                        return '{s name=integrationTypeAjaxCall}AJAX{/s}';
                    } 
                }
                },
                isDefault: { header: me.snippets.isDefault }
            }
        };
    }
    
});
