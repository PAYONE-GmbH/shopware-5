/**
 * $Id: $
 */

//{block name="backend/mopt_payone_transaction_log/store/logs"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.store.Logs', {
  /**
   * Extend for the standard ExtJS 4
   * @string
   */
  extend: 'Ext.data.Store',
  /**
   * Auto load the store after the component
   * is initialized
   * @boolean
   */
  autoLoad: false,
  /**
   * Amount of data loaded at once
   * @integer
   */
  pageSize: 20,
  remoteFilter: true,
  remoteSort: true,
  /**
   * Define the used model for this store
   * @string
   */
  model: 'Shopware.apps.MoptPayoneTransactionLog.model.Log',
  // Default sorting for the store
  sortOnLoad: true,
  /**
   * Configure the data communication
   * @object
   */
  proxy: {
    type: 'ajax',
    api: {
      read: '{url controller="MoptPayoneTransactionLog" action="getTransactionLogs"}',
      detail: '{url controller="MoptApilogPayone" action="getDetailLog"}',
      search: '{url controller="MoptPayoneTransactionLog" action="getSearchResult"}'
    },
    /**
     * Configure the data reader
     * @object
     */
    reader: {
      type: 'json',
      root: 'data',
      //total values, used for paging
      totalProperty: 'total'
    },
    sorters: {
      property: 'creationDate',
      direction: 'DESC'
    }
  }
});
//{/block}