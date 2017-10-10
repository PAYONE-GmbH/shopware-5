/**
 * $Id: $
 */

//{block name="backend/mopt_payone_transaction_log/controller/main"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.controller.Main', {
  /**
    * Extend from the standard ExtJS 4
    * @string
    */
  extend: 'Ext.app.Controller',

  requires: [ 'Shopware.apps.MoptPayoneTransactionLog.controller.Log' ],

  init: function() {
    var me = this;
    me.subApplication.logStore = me.subApplication.getStore('Logs');
    me.subApplication.logStore.load();
    
    //details
    me.subApplication.dataStore = me.subApplication.getStore('Detail');
    me.subApplication.dataStore.load();
    
    me.mainWindow = me.getView('main.Window').create({
      logStore: me.subApplication.logStore
    });

    this.callParent(arguments);
  }
});
//{/block}