/**
 * $Id: $
 */

//{block name="backend/mopt_config_payone/store/config"}
Ext.define('Shopware.apps.MoptConfigPayone.store.Config', {
  /**
   * Define that this component is an extension of the Ext.data.Store
   */
  extend: 'Ext.data.Store',
  /**
   * Auto load the store after the component
   * is initialized
   * @boolean
   */
  autoLoad: false,
  /**
   * Define the used model for this store
   * @string
   */
  model: 'Shopware.apps.MoptConfigPayone.model.Config',
  /**
   * Configure the data communication
   * @object
   */
  proxy: {
    /**
     * Führt einen Ajax-Request auf die Controller-Actions aus
     */
    type: 'ajax',
    /**
     * Ordnet die Store-Operationen (read, create, update, destroy)
     * den gewünschten Controller-Actions zu
     */
    api: {
      create: '{url controller="MoptConfigPayone" action="saveConfig"}',
      read: '{url controller="MoptConfigPayone" action="getConfig"}',
      update: '{url controller="MoptConfigPayone" action="saveConfig"}',
      payment: '{url controller="MoptConfigPayone" action="getPaymentConfig"}',
      transactions: '{url controller="MoptPayoneTransactionForward" action="getTransactionForward"}'
    },
    /**
     * Definiert, dass die Kommunikation mit dem Controller
     * in JSON abgewickelt wird. Die Daten werden hier im
     * "data"-Element des JSON-Arrays hinterlegt,
     * die Gesamtzahl der vorhandenen Einträge im "total"-Element.
     */
    reader: {
      type: 'json',
      root: 'data'
    },
    
    writer: {
      type: 'json',
      root: 'data',
      writeAllFields: true
    }
  }
});
//{/block}