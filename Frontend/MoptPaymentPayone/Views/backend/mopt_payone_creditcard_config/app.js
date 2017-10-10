Ext.define('Shopware.apps.MoptPayoneCreditcardConfig', {
    extend: 'Enlight.app.SubApplication',
 
    name:'Shopware.apps.MoptPayoneCreditcardConfig',
 
    loadPath: '{url action=load}',
    bulkLoad: true,
 
    controllers: [ 'Main' ],
 
    views: [
        'list.Window',
        'list.Creditcardconfig',
        'detail.Window',
        'detail.Creditcardconfig'
    ],
 
    models: [ 'Creditcardconfig' ],
    stores: [ 'Creditcardconfig' ],
 
    launch: function() {
        return this.getController('Main').mainWindow;
    }
});