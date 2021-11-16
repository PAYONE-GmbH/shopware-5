/**
 * $Id: $
 */

//{namespace name=backend/mopt_apilog_payone/main}

/**
 * Payone apilog detail view
 */
//{block name="backend/mopt_apilog_payone/view/detail/detail"}
Ext.define('Shopware.apps.MoptApilogPayone.view.detail.Detail', {
  /**
   * Extend from the standard ExtJS 4
   * @string
   */
  extend: 'Ext.panel.Panel',
  layout: {
    type: 'hbox',
    align: 'stretch'
  },
  
  width: 800,
  
  border: 0,
  ui: 'shopware-ui',
  /**
   * Alias name for the view. Could be used to get an instance
   * of the view through Ext.widget('moptPayoneApilogMainDetail')
   * @string
   */
  alias: 'widget.moptPayoneApilogMainDetail',
  /**
   * The window uses a border layout, so we need to set
   * a region for the grid panel
   * @string
   */
  region: 'center',
  /**
   * The view needs to be scrollable
   * @string
   */
  autoScroll: true,
  /**
   * Sets up the ui component
   * @return void
   */
  initComponent: function() {
    var me = this;

    me.items = [
      {
        xtype: 'grid',
        title: '{s name="details/request/title"}Request{/s}',
        width: '50%',
        columns: [
          {
            header: '{s name="details/property"}Eigenschaft{/s}',
            dataIndex: 'name',
            width: '30%',
            sortable: false,
            menuDisabled: true
          },
          {
            header: '{s name="details/value"}Wert{/s}',
            dataIndex: 'value',
            width: '70%',
            sortable: false,
            menuDisabled: true
          }
        ],
        store: Ext.create('Shopware.apps.MoptApilogPayone.store.Detail').load({
          action: 'read',
          params: {
            id: me.itemSelected,
            type: 'request'
          }
        }),
        flex: 1
      },
      {
        xtype: 'grid',
        title: '{s name="details/response/title"}Response{/s}',
        width: '50%',
        columns: [
          {
            header: '{s name="details/property"}Eigenschaft{/s}',
            dataIndex: 'name',
            width: '30%',
            menuDisabled: true
          },
          {
            header: '{s name="details/value"}Wert{/s}',
            dataIndex: 'value',
            width: '70%',
            menuDisabled: true
          }
        ],
        store: Ext.create('Shopware.apps.MoptApilogPayone.store.Detail').load({
          action: 'read',
          params: {
            id: me.itemSelected,
            type: 'response'
          }
        }),
        flex: 1
      }
    ];

    me.callParent(arguments);
  }
});
//{/block}