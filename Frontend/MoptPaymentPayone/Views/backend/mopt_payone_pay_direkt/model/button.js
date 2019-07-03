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
        { name: 'localeId', type: 'int' },
        { name: 'image', type: 'string'},
    ],
    
    associations: [
        {
            relation: 'ManyToOne',
            field: 'localeId',
            
            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Locale',
            name: 'getLocale',
            associationKey: 'locale'
        }]
});
 
