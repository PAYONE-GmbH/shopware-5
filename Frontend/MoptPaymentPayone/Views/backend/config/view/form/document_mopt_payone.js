//
// {namespace name="backend/config/view/document"}
// {block name="backend/config/view/form/document"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.Config.view.form.DocumentMoptPayone', {
    override: 'Shopware.apps.Config.view.form.Document',
    alias: 'widget.config-form-document-mopt-payone',

    initComponent: function() {
        var me = this;
        me.callParent(arguments);
    },

    /**
     * Overrides the getFormItems method and appends the Payone form items
     * @return { Array }
     */
    getFormItems: function() {
        var formItems = this.callParent(arguments);

        var elementFieldSetIndex = -1;
        formItems.forEach(function(item, index) {
            if (item && item.name === 'elementFieldSet') {
                elementFieldSetIndex = index;

                return false;
            }
        });

        if (elementFieldSetIndex === -1) {
            return formItems;
        }

        formItems[elementFieldSetIndex].items.push({
            xtype: 'tinymce',
            fieldLabel: 'PAYONE Footer Content',
            labelWidth: 100,
            name: 'PAYONE_Footer_Value',
            hidden: true,
            translatable: false
        }, {
            xtype: 'textarea',
            fieldLabel: 'PAYONE Footer Style',
            labelWidth: 100,
            name: 'PAYONE_Footer_Style',
            hidden: true,
            translatable: false
        }, {
            xtype: 'tinymce',
            fieldLabel: 'PAYONE Content Info',
            labelWidth: 100,
            name: 'PAYONE_Content_Info_Value',
            hidden: true,
            translatable: false
        }, {
            xtype: 'textarea',
            fieldLabel: 'Payone Content Info Style',
            labelWidth: 100,
            name: 'PAYONE_Content_Info_Style',
            hidden: true,
            translatable: false
        });

        return formItems;
    }
});
// {/block}
