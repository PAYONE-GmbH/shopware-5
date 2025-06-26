/**
 * $Id: $
 */

//{block name="backend/mopt_payone_transaction_log/store/detail"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.store.Detail', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptPayoneTransactionLog.model.Grid2cols',
  autoLoad: false,
  /**
   * Define the used model for this store
   * @string
   */
  proxy: {
    type: 'ajax',
    api: {
      read: '{url controller="MoptPayoneTransactionLog" action="getDetailData"}'
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
    }
  }
});
//{/block}