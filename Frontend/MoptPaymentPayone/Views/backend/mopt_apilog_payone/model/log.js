/**
 * $Id: $
 */

/**
 * model for api log entries
 */
//{block name="backend/mopt_apilog_payone/model/log"}
Ext.define('Shopware.apps.MoptApilogPayone.model.Log', {
  /**
    * Extends the standard ExtJS 4
    * @string
    */
  extend: 'Ext.data.Model',
  /**
    * The fields used for this model
    * @array
    */
  fields: [
  //{block name="backend/mopt_apilog_payone/model/log/fields"}{/block}
  'id',
  'request',
  'response',
  'liveMode',
  'merchantId',
  'portalId',
  {
    name: 'creationDate',
    type: 'date',
    dateFormat:'Y-m-d'
  },
  'requestDetails',
  'responseDetails',
  'responseArray',
  'requestArray'
  ]
  ,
  /**
    * Configure the data communication
    * @object
    */
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
      detail: '{url controller="MoptApilogPayone" action="getDetailLog"}'
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
    }
  }
});
//{/block}