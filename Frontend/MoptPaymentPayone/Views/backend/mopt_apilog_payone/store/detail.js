/**
 * $Id: $
 */

/**
 * apilogs details store
 */
//{block name="backend/mopt_apilog_payone/store/detail"}
Ext.define('Shopware.apps.MoptApilogPayone.store.Detail', {
  /**
   * Extend for the standard ExtJS 4
   * @string
   */
  extend: 'Ext.data.Store',
  
  model: 'Shopware.apps.MoptApilogPayone.model.Grid2cols',
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
      read: '{url controller="MoptApilogPayone" action="getGridData"}'
    },
    /**
     * Definiert, dass die Kommunikation mit dem Controller
     * in JSON abgewickelt wird. Die Daten werden hier im
     * "data"-Element des JSON-Arrays hinterlegt,
     * die Gesamtzahl der vorhandenen Einträge im "total"-Element.
     */
    reader: {
      type: 'json',
      root: 'data',
      //total values, used for paging
      totalProperty: 'total'
    }
  },
  
  // Default sorting for the store
  sortOnLoad: true
});
//{/block}