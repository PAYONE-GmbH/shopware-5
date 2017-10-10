/**
* $Id: $
 */
//{block name="backend/mopt_support_payone/application"}
Ext.define('Shopware.apps.MoptSupportPayone', {

  /**
     * Extends from our special controller, which handles the
     * sub-application behavior and the event bus
     * @string
     */
  extend: 'Enlight.app.SubApplication',

  /**
     * Sets the loading path for the sub-application.
     *
     * Note that you'll need a "loadAction" in your
     * controller (server-side)
     * @string
     */
  loadPath:'{url action=load}',

  /**
     * Enables our bulk loading technique.
     * @booelan
     */
  bulkLoad: true,

  /**
     * The name of the module. Used for internal purpose
     * @string
     */
  name: 'Shopware.apps.MoptSupportPayone',

  /**
     * Required controllers for module (subapplication)
     * @array
     */
  controllers: [ 'Main' ],

  /**
     * Required views for the module (subapplication)
     */
  views: [ 'main.Window' ],

  launch: function() {
    var me = this,
    mainController = me.getController('Main');
    return mainController.mainWindow;
  }
});
//{/block}