/**
* $Id: $
 */

//{block name="backend/mopt_support_payone/controller/main"}
Ext.define('Shopware.apps.MoptSupportPayone.controller.Main', {

  /**
     * Extend from the standard ExtJS 4 controller
     * @string
     */
  extend: 'Ext.app.Controller',

  /**
	 * Creates the necessary event listener for this
	 * specific controller and opens a new Ext.window.Window
	 * to display the subapplication
     *
     * @return void
	 */
  init: function() {
    var me = this;
    me.mainWindow = me.getView('main.Window').create();
  }
});
//{/block}