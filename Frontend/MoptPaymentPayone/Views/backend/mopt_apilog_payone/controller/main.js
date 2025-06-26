/**
 * $Id: $
 */

//{block name="backend/mopt_apilog_payone/controller/log"}
Ext.define('Shopware.apps.MoptApilogPayone.controller.Main', {
  /**
    * Extend from the standard ExtJS 4
    * @string
    */
  extend: 'Ext.app.Controller',

  requires: [ 'Shopware.apps.MoptApilogPayone.controller.Log' ],
 

  /**
   * Init-function to create the main-window and assign the paymentStore
   */
  init: function() {
    var me = this;
    me.subApplication.logStore = me.subApplication.getStore('Logs');
    me.subApplication.logStore.load();
    me.subApplication.dataStore = me.subApplication.getStore('Detail');
    me.subApplication.dataStore.load();
    me.mainWindow = me.getView('main.Window').create({
      logStore: me.subApplication.logStore
    });
    
    this.callParent(arguments);
  }
});
//{/block}