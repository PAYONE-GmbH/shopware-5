//{namespace name=backend/mopt_payone_order/main}
//{block name="backend/order/view/detail/position" append}
Ext.define('Shopware.apps.Order.view.detail.MoptPayonePosition', {
  override: 'Shopware.apps.Order.view.detail.Position',
  
  createGridToolbar: function() {
    var me = this;
    var toolbar = me.callParent(arguments);
    
    me.moptPayoneCapturePositionsButton = Ext.create('Ext.button.Button', {
      iconCls: 'sprite-money-coin',
      text: '{s name=position/capture}Positionen einziehen{/s}',
      action: 'moptPayoneCapturePositions',
      handler: function() {
        me.fireEvent('moptPayoneCapturePositions', me.record, me.orderPositionGrid, {
            callback: function(order) {
                me.fireEvent('updateForms', order, me.up('window'));
            }
        });
      }
    });
    
    me.moptPayoneDebitPositionsButton = Ext.create('Ext.button.Button', {
      iconCls: 'sprite-money-coin',
      text: '{s name=position/debit}Positionen gutschreiben{/s}',
      action: 'moptPayoneDebitPositions',
      handler: function() {
        me.fireEvent('moptPayoneDebitPositions', me.record, me.orderPositionGrid, {
            callback: function(order) {
                me.fireEvent('updateForms', order, me.up('window'));
            }
        });
      }
    });
    
    toolbar.items.add(me.moptPayoneCapturePositionsButton);
    toolbar.items.add(me.moptPayoneDebitPositionsButton);
    
    return toolbar;
  },
  
  registerEvents: function() {
    var me = this;
    me.callParent(arguments);
    
    this.addEvents('moptPayoneCapturePositions', 'moptPayoneDebitPositions');
  },
          
  getColumns:function (grid) {
    var me = this;
    columns = me.callParent(arguments);
    
    columns.push({
        header: '{s name=position/captured}Eingezogen{/s}',
        dataIndex: 'moptPayoneCaptured',
        flex:1
      },
      {
        header: '{s name=position/debited}Gutgeschrieben{/s}',
        dataIndex: 'moptPayoneDebit',
        flex:1
      }
    );
    return columns;
  }
          
  
});
//{/block}