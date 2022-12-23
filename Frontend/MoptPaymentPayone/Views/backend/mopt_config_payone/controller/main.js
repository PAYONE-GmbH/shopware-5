//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/controller/main"}
Ext.define('Shopware.apps.MoptConfigPayone.controller.Main', {
  extend: 'Ext.app.Controller',
  refs: [
    {
      ref: 'detailForm',
      selector: 'config-main-window config-main-detail'
    }
  ],
  init: function() {
    var me = this;
    me.data = {
      config: Ext.create('Shopware.apps.MoptConfigPayone.store.Config'),
      testlive: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboTestlive'),
      signal: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboSignal'),
      auth: Ext.create('Shopware.apps.MoptConfigPayone.store.Auth'),
      submitbasket: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboSubmitbasket'),
      yesno: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboYesno'),
      yesnouser: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboYesnouser'),
      point: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboPoint'),
      infoscoreb2c: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboInfoscoreb2c'),
      infoscoreb2b: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboInfoscoreb2b'),
      checkbasicperson: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboCheckbasicperson'),
      mistake: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboMistake'),
      consumerscore: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboConsumerscore'),
      payments: Ext.create('Shopware.apps.MoptConfigPayone.store.Payments'),
      states: Ext.create('Shopware.apps.MoptConfigPayone.store.StatePayment'),
      checkcc: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboAccountcheck'),
      terms: Ext.create('Shopware.apps.MoptConfigPayone.store.ComboTerms')
    };

    me.mainWindow = me.createMainWindow();

    me.callParent(arguments);

    return me.mainWindow;
  },
  
  createMainWindow: function() {
    var me = this, window;

    window = me.getView('main.Window').create({
      data: me.data
    });

    return window;
  },
  /**
   * Helper function to register all component events
   */
  addControls: function() {
    var me = this;

    me.control({
      'config-list-window config-detail-panel': {
        nameChanged: me.onFormChange,
        commentChanged: me.onFormChange,
        saveConfig: me.onUpdateConfig
      }
    });
  }
});
//{/block}