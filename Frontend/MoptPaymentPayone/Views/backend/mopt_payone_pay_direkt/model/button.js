Ext.define('Shopware.apps.MoptPayonePayDirekt.model.Button', {
    extend: 'Shopware.data.Model',
    configure: function () {
        return {
            controller: 'MoptPayonePayDirekt',
            detail: 'Shopware.apps.MoptPayonePayDirekt.view.detail.Button'
        };
    },
 
    fields: [
        { name: 'id', type: 'int', useNull: true},
        { name: 'shopId', type: 'int' },
        { name: 'packStationMode', type: 'string' },
        { name: 'dispatchId', type: 'int'},
        { name: 'image', type: 'string'},
    ],

    associations: [
        {
            relation: 'ManyToOne',
            field: 'shopId',

            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Shop',
            name: 'getShop',
            associationKey: 'shop'
        },
        {
            relation: 'ManyToOne',
            field: 'dispatchId',
            type: 'hasMany',
            model: 'Shopware.apps.Order.model.Dispatch',
            name: 'getDispatch',
            associationKey: 'dispatch'
        }
    ]
});
