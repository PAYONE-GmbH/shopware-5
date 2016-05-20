Ext.define('Shopware.apps.MoptPayonePaypal', {
    extend: 'Enlight.app.SubApplication',
 
    name:'Shopware.apps.MoptPayonePaypal',
 
    loadPath: '{url action=load}',
    bulkLoad: true,
 
    controllers: [ 'Main' ],
 
    views: [
        'list.Window',
        'list.Button',
        'detail.Window',
        'detail.Button'
    ],
 
    models: [ 'Button' ],
    stores: [ 'Button' ],
 
    launch: function() {
        return this.getController('Main').mainWindow;
    }
});