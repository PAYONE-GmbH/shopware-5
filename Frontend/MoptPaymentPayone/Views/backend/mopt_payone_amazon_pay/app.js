Ext.define('Shopware.apps.MoptPayoneAmazonPay', {
    extend: 'Enlight.app.SubApplication',
 
    name:'Shopware.apps.MoptPayoneAmazonPay',
 
    loadPath: '{url action=load}',
    bulkLoad: true,
 
    controllers: [ 'Main' ],
 
    views: [
        'list.Window',
        'list.Config',
        'detail.Window',
        'detail.Config'
    ],
 
    models: [ 'Config' ],
    stores: [ 'Config' ],
 
    launch: function() {
        return this.getController('Main').mainWindow;
    }
});