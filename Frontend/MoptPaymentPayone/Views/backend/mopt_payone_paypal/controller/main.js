Ext.define('Shopware.apps.MoptPayonePaypal.controller.Main', {
    extend: 'Enlight.app.Controller',
 
    init: function() {
        var me = this;
        me.mainWindow = me.getView('list.Window').create({ }).show();
    }
});