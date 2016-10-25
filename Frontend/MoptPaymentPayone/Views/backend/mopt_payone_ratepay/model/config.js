Ext.define('Shopware.apps.MoptPayoneRatepay.model.Config', {
    extend: 'Shopware.data.Model',
    configure: function () {
        return {
            controller: 'MoptPayoneRatepay',
            detail: 'Shopware.apps.MoptPayoneRatepay.view.detail.Config'
        };
    },
 
    fields: [
        { name: 'shopid', type: 'int', useNull: true},
        { name: 'currency', type: 'string', useNull: true},
    ]
});
 
