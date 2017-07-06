//{namespace name=backend/mopt_export_payone/main}
//{block name="backend/mopt_export_payone/view/main/window"}
Ext.define('Shopware.apps.MoptExportPayone.view.main.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name=window/title}PAYONE Konfigurationsexport{/s}',
    alias: 'widget.mopt-export-config-main-window',
    border: false,
    layout: 'fit',
    autoShow: true,
    height: 200,
    width: 265,

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        
        me.bbar = me.createToolbar();
        
        me.items = [{
            xtype: 'container',
            html: '<div style="padding:10px;">{s name=window/info}Es wird eine XML-Datei mit allen PAYONE-Konfigurationsoptionen erstellt.{/s}</div>'
        }];
        me.callParent(arguments);
    },
    
    /**
     * Creates the toolbar
     *
     * @return Object
     */
    createToolbar: function() {
        var me = this;

        return {
            xtype: 'toolbar',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: [
                '->',
                {
                    cls: 'primary',
                    name: 'mopt-payone-export-download',
                    text: '{s name=button/download}Download Konfigurationsdatei{/s}',
                    handler: function() {
                        me.fireEvent('moptDownloadConfigExport', me);
                    }
                }]
        };
    }
});
//{/block}