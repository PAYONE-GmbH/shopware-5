/**
 * $Id: $
 */

/**
 * this model is used for the detail view of log entries
 */
//{block name="backend/mopt_apilog_payone/model/grid2cols"}
Ext.define('Shopware.apps.MoptApilogPayone.model.Grid2cols', {
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
  //{block name="backend/mopt_apilog_payone/model/grid2cols/fields"}{/block}
  'name',
  'value'
  ]
  
});
//{/block}