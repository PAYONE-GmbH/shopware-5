/**
 * $Id: $
 */

//{namespace name=backend/mopt_payone_transaction_log/main}

//{block name="backend/mopt_payone_transaction_log/view/main/window"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.view.main.Window', {
	extend: 'Enlight.app.Window',
    title: '{s name=window_title}Transaktion-Log{/s}',
    cls: Ext.baseCSSPrefix + 'log-window',
    alias: 'widget.log-main-window-api',
    border: false,
    autoShow: true,
    layout: 'border',
    height: 514,
    width: 1200,

    stateful: true,
    stateId:'shopware-log-window',

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.items = [{
            xtype: 'moptPayoneTransactionLogMainList',
            logStore: me.logStore
        }];

        me.callParent(arguments);
    }
});
//{/block}