/**
 * $Id: $
 */

//{namespace name=backend/mopt_payone_transaction_log/main}
//{block name="backend/mopt_payone_transaction_log/view/main/detailwindow"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.view.main.Detailwindow', {
	extend: 'Enlight.app.Window',
    title: '{s name="window_title_detail_trans"}Transaktionsstatus-Detail{/s}',
    cls: Ext.baseCSSPrefix + 'detail-window',
    alias: 'widget.moptPayoneTransactionLogMainDetailWindow',
    border: false,
    autoShow: true,
    layout: 'border',
    height: '90%',
    width: 800,

    stateful: true,
    stateId:'shopware-detail-window',

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.title = '{s name="transaction_log_details_for"}Transaktionsstatus-Detail zu ID {/s}' + me.itemSelected;
        me.items = [{
            xtype: 'moptPayoneTransactionLogMainDetail',
            itemSelected: me.itemSelected
        }];

        me.callParent(arguments);
    }
});
//{/block}