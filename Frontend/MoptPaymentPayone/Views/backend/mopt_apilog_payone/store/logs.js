/**
 * $Id: $
 */

/**
 * apilogs store
 */
//{block name="backend/mopt_apilog_payone/store/logs"}
Ext.define('Shopware.apps.MoptApilogPayone.store.Logs', {
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
  storeId: 'logsStore',
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
  model: 'Shopware.apps.MoptApilogPayone.model.Log',
  proxy: {
    type: 'ajax',
    /**
     * Configure the url mapping for the different
     * @object
     */
    api: {
      //read out all articles
      read: '{url controller="MoptApilogPayone" action="getApilogs"}',
      destroy: '{url controller="MoptApilogPayone" action="deleteLogs"}',
      detail: '{url controller="MoptApilogPayone" action="getDetailLog"}',
      search: '{url controller="MoptApilogPayone" action="getSearchResult"}'
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
    sortOnLoad: true,
    sorters: {
      property: 'creationDate',
      direction: 'DESC'
    }
  }
  // Default sorting for the store

});
//{/block}