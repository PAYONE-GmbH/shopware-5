/**
 * $Id: $
 */

//{namespace name=backend/mopt_apilog_payone/main}
//{block name="backend/mopt_apilog_payone/view/main/window"}
Ext.define('Shopware.apps.MoptApilogPayone.view.main.Window', {
  extend: 'Enlight.app.Window',
  title: '{s name=window_title}API-Log{/s}',
  cls: Ext.baseCSSPrefix + 'log-window',
  alias: 'widget.log-main-window-api',
  border: false,
  autoShow: true,
  height: 550,
  layout: 'border',
  width: 1000,
  stateful: true,
  stateId: 'shopware-log-window',
  /**
   * Initializes the component and builds up the main interface
   *
   * @return void
   */
  initComponent: function() {
    var me = this;
    me.items = [
      {
        xtype: 'moptPayoneApilogMainList',
        logStore: me.logStore
      }
    ];

    me.callParent(arguments);
  }
});
//{/block}