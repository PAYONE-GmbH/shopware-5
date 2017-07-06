/**
 * $Id: $
 */

//{namespace name=backend/mopt_apilog_payone/main}


//{block name="backend/mopt_apilog_payone/controller/log"}
Ext.define('Shopware.apps.MoptApilogPayone.controller.Log', {
    /**
    * Extend from the standard ExtJS 4
    * @string
    */
    extend: 'Ext.app.Controller',

	/**
	* Creates the necessary event listener for this
	* specific controller and opens a new Ext.window.Window
	* @return void
	*/
	init: function() {
		var me = this;

		me.control({
			'moptPayoneApilogMainList toolbar combobox': {
				change: me.onSelectFilter
			}
		});
	},

	/**
	 * This function is called, when the user selects a filter by using the combobox in the toolbar.
	 * It handles the filtering of the store.
	 *
	 * @param combobox Contains the combobox itself
	 * @param newValue Contains the new selected and active value
	 */
	onSelectFilter: function(combobox, newValue){
		var win = combobox.up('window'),
			grid   = win.down('grid'),
			store  = grid.getStore();

		//When you delete the filter of the combobox this function is called twice
		//1st time it's an empty string, 2nd time it is null
		if(newValue === null) {
			return;
		}
		//If the value is an empty string
		if(newValue.length == 0) {
			store.clearFilter();
		}else{
			//contains the displayText of the selected value in the combobox
			var selectedDisplayText = combobox.store.data.findBy(function(item){
				if(item.internalId == newValue) {
					return true;
				}
			}).data.name;
			//This won't reload the store
			store.filters.clear();
			//Loads the store with a special filter
			store.filter('searchValue',selectedDisplayText);
		}
	}
});
//{/block}