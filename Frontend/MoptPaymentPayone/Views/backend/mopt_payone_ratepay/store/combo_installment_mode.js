//{block name="backend/mopt_payone_ratepay/store/combo_installment_mode"}
Ext.define('Shopware.apps.MoptPayoneRatepay.store.ComboInstallmentMode', {
  extend: 'Ext.data.Store',
  model: 'Shopware.apps.MoptPayoneRatepay.model.Combo',
  data: [
    {
      display: 'Lastschrift',
      value: 'true'
    }, {
      display: 'Vorkasse',
      value: 'false'
    }]
});
//{/block}