/**
 * $Id: $
 */

//{block name="backend/mopt_apilog_payone/application"}
Ext.define('Shopware.apps.MoptApilogPayone', {
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
  name: 'Shopware.apps.MoptApilogPayone',
  /**
	* Sets the loading path for the sub-application.
	*
	* Note that you'll need a "loadAction" in your
	* controller (server-side)
	* @string
	*/
  loadPath : '{url controller=MoptApilogPayone action=load}',
  bulkLoad: true,

  /**
    * Required views for controller
    * @array
    */
  views: [ 'main.Window', 'main.Detailwindow', 'log.List', 'detail.Detail' ],
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

  /**
     * Returns the main application window for this is expected
     * by the Enlight.app.SubApplication class.
     * The class sets a new event listener on the "destroy" event of
     * the main application window to perform the destroying of the
     * whole sub application when the user closes the main application window.
     *
     * This method will be called when all dependencies are solved and
     * all member controllers, models, views and stores are initialized.
     *
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
  launch:function () {
    var me = this,
    mainController = me.getController('Main');

    return mainController.mainWindow;
  }
});
//{/block}