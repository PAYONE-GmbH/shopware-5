Ext.define('Shopware.apps.MoptPayoneAmazonPay.controller.Main', {
    extend: 'Enlight.app.Controller',
 
    init: function() {
        var me = this;

        me.control({
            'mopt-payone-amazon-pay-listing-grid': {
                'download-config': me.displayProcessWindow
            }
        });
        Shopware.app.Application.on('download-config-process', me.onDownloadAmazonPayConfig);
        me.mainWindow = me.getView('list.Window').create({ }).show();
    },
    
    displayProcessWindow: function(grid) {
        var selection = grid.getSelectionModel().getSelection();
 
        if (selection.length <= 0) return;
 
        Ext.create('Shopware.window.Progress', {
            title: 'Stapelverarbeitung',
            configure: function() {
                return {
                    tasks: [{
                        event: 'download-config-process',
                        data: selection,
                        text: 'Konfiguration [0] von [1]'
                    }],
 
                    infoText: '<h2>Amazon Pay Konfigurationen werden abgerufen</h2>' +
                        'Um den Prozess abzubrechen, können Sie den <b><i>`Cancel process`</i></b> Button verwenden. ' +
                        'Abhänging von der Datenmenge kann dieser Prozess einige Minuten in Anspruch nehmen.'
                }
            }
        }).show();
    },
    onDownloadAmazonPayConfig: function (task, record, callback) {
        Ext.Ajax.request({
            url: '{url controller=MoptPayoneAmazonPay action=downloadConfig}',
            method: 'POST',
            params: {
                configId: record.get('id')
            },
            success: function(response, operation) {
                callback(response, operation);
            },
            error: function(response, operation) {
                callback(response, operation);
            }
        }); 
    }
});
