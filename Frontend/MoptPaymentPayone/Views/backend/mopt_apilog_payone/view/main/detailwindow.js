/**
 * $Id: $
 */

//{namespace name=backend/mopt_apilog_payone/main}
//{block name="backend/mopt_apilog_payone/view/main/detailwindow"}
Ext.define('Shopware.apps.MoptApilogPayone.view.main.Detailwindow', {
	extend: 'Enlight.app.Window',
    title: '{s name="window_detail_title"}API-Log Details{/s}',
    cls: Ext.baseCSSPrefix + 'detail-window',
    alias: 'widget.moptPayoneApilogMainDetailWindow',
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
        me.title = '{s name="api_log_details_for"}API-Log Details zu ID {/s}' + me.itemSelected;
        me.items = [{
            xtype: 'moptPayoneApilogMainDetail',
            itemSelected: me.itemSelected
        }];

        me.callParent(arguments);
    }
});
//{/block}