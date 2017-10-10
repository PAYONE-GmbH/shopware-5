/**
 * $Id: $
 */
 
//{block name="backend/mopt_config_payone/application"}
Ext.define('Shopware.apps.MoptConfigPayone', {
  name:'Shopware.apps.MoptConfigPayone',
  extend:'Enlight.app.SubApplication',
  bulkLoad: true,
  loadPath:'{url action=load}',

  controllers: [ 'Main' ],
  stores: [ 'Config', 'Signal', 'Payments' ],
  models: [ 'Config', 'Signal', 'Payment' ],
  views: [ 'main.Window', 'main.Detail' ],

  launch: function() {
    var me = this,
    mainController = me.getController('Main');

    return mainController.mainWindow;
  }
});
//{/block}