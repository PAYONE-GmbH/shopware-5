/**
 * $Id: $
 */

//{block name="backend/mopt_payone_transaction_log/application"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog', {
  /**
	* Extends from our special controller, which handles the
	* sub-application behavior and the event bus
	* @string
	*/
  extend : 'Enlight.app.SubApplication',
  /**
	* The name of the module. Used for internal purpose
	* @string
	*/
  name: 'Shopware.apps.MoptPayoneTransactionLog',
  /**
	* Sets the loading path for the sub-application.
	*
	* Note that you'll need a "loadAction" in your
	* controller (server-side)
	* @string
	*/
  loadPath : '{url controller=MoptPayoneTransactionLog action=load}',
  bulkLoad: true,

  /**
    * Required views for controller
    * @array
    */
  views: [ 'main.Window', 'log.List', 'main.Detailwindow', 'detail.Detail'],
  /**
    * Required stores for controller
    * @array
    */
  stores: [ 'Logs', 'Detail' ],
  /**
    * Required models for controller
    * @array
    */
  models: [ 'Log', 'Grid2cols' ],

  /**
	* Requires controllers for sub-application
	* @array
	*/
  controllers : [ 'Main' ],

  launch:function () {
    var me = this,
    mainController = me.getController('Main');

    return mainController.mainWindow;
  }
});
//{/block}