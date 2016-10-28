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
        { name: 'currencyId', type: 'int'},
        { name: 'countryCodeBilling', type: 'string', useNull: true, hidden: true}
    ],

    associations: [
            {
                relation: 'ManyToOne',
                field: 'currencyId',

                type: 'hasMany',
                model: 'Shopware.apps.Base.model.Currency',
                associationKey: 'currency'
            }]    
});
 
