//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/view/main/window"}
Ext.define('Shopware.apps.MoptConfigPayone.view.main.Window', {
  extend: 'Enlight.app.Window',
  alias: 'widget.config-main-window',
  title: '{s name="window/main/title"}PAYONE Konfiguration{/s}',
  border: false,
  autoShow: true,
  layout: 'fit',
  width: 920,
  height: 650,
  maximizable: true,
  minimizable: true,

  initComponent: function() {
    var me = this;

    me.items = [{
        xtype: 'mopt-config-main-detail',
        data: me.data
      }];


    me.callParent(arguments);
  }

});
//{/block}