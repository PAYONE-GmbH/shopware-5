/**
 * $Id: $
 */

//{namespace name=backend/mopt_payone_transaction_log/main}
//{block name="backend/mopt_payone_transaction_log/view/log/list"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.view.log.List',
        {
          extend: 'Ext.grid.Panel',
          border: 0,
          ui: 'shopware-ui',
          /**
           * Alias name for the view. Could be used to get an instance
           * of the view through Ext.widget('moptPayoneTransactionLogMainList')
           * @string
           */
          alias: 'widget.moptPayoneTransactionLogMainList',
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
            me.selModel = me.createSelectionModel();
            me.store = me.logStore;
            me.columns = me.getColumns();

            //paging
            me.toolbar = me.getToolbar(me);
            me.dockedItems = [];
            me.dockedItems.push(me.toolbar);
            me.dockedItems.push({
              dock: 'bottom',
              xtype: 'pagingtoolbar',
              displayInfo: true,
              store: me.store,
              width: '50%'
            });

            me.callParent(arguments);
          },
          createSelectionModel: function()
          {
            var me = this;

            return Ext.create('Ext.selection.RowModel', {
              listeners: {
                selectionchange: function(view, selected) {
                if(selected[0])
                {
                    me.detail = Ext.create('Shopware.apps.MoptPayoneTransactionLog.view.main.Detailwindow', {
                      itemSelected: selected[0].data.id
                    }).show();
                  }
                }
              }
            });
          },
          /**
           *  Creates the columns
           *
           *  @return array columns Contains all columns
           */
          getColumns: function() {
            var me = this;

            var columns = [{
                header: '{s name="mopt_payone_transaction_log/grid/column_id"}ID{/s}',
                dataIndex: 'id',
                flex: 1
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_transactionId"}Transaktionsid{/s}',
                dataIndex: 'transactionId',
                flex: 1
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_orderNr"}Bestellnummer{/s}',
                dataIndex: 'orderNr',
                flex: 1
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_mode"}Betriebsmodus{/s}',
                dataIndex: 'liveMode',
                flex: 1,
                renderer: me.renderLivemode
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_portal_id"}Portal ID{/s}',
                dataIndex: 'portalId',
                flex: 1
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_status"}Status{/s}',
                dataIndex: 'status',
                flex: 1
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_creation_date"}erstellt{/s}',
                dataIndex: 'creationDate',
                flex: 1,
                xtype: 'datecolumn',
                renderer: me.renderDate
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_update_date"}geändert{/s}',
                dataIndex: 'updateDate',
                flex: 1,
                xtype: 'datecolumn',
                renderer: me.renderDate
              }, {
                header: '{s name="mopt_payone_transaction_log/grid/column_transaction_date"}Transaktionsdatum{/s}',
                dataIndex: 'transactionDate',
                flex: 1,
                xtype: 'datecolumn',
                renderer: me.renderDate
              }
            ];

            return columns;
          },
          renderDate: function(value)
          {
            return Ext.util.Format.date(value) + ' ' + Ext.util.Format.date(value, 'H:i:s');
          },
          renderLivemode: function(value)
          {
            return value === true ? 'live' : 'test';
          },
          getToolbar: function()
          {
            var me = this;

            var items = [
              '-',
              {
                xtype: 'textfield',
                name: 'searchTrans',
                id: 'searchTransField',
                fieldLabel: '{s name="toolbar/searchField"}Freitext{/s}'
              },
              {
                xtype: 'button',
                name: 'searchbtntrans',
                text: '{s name="toolbar/searchButton"}Suchen{/s}',
                id: 'searchBtnTrans',
                width: '50px',
                dock: 'top',
                handler: function() {
                  data = me.store.load({
                    action: 'search',
                    filters: [{
                        property: 'search',
                        value: Ext.getCmp('searchTransField').getValue()
                      }]

                  });
                }

              }, '-',
              {
                xtype: 'button',
                name: 'resetTransBtn',
                text: '{s name="toolbar/searchReset"}Suche zurücksetzen{/s}',
                id: 'resetTransBtn',
                dock: 'top',
                handler: function(btn, event) {
                  var stori = me.store;
                  Ext.getCmp('searchTransField').setValue('');
                  data = me.store.load({
                    action: 'search',
                    pageSize: 20,
                  });
                }
              }


            ];
            return Ext.create('Ext.toolbar.Toolbar', {
              dock: 'top',
              ui: 'shopware-ui',
              items: items
            });
          }
        });
//{/block}