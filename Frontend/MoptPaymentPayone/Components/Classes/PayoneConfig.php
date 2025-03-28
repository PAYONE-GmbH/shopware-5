<?php

/**
 * $Id: $
 */
class Mopt_PayoneConfig
{

    public static $MOPT_PAYONE_FORWARD_TRANSACTION_STATUS_DEFAULTS = [
        'curl_timeout' => 200,
        'curl_timeout_raise' => 100,
        'curl_trials_max' => 3,
    ];
  /**
   * standard valid IPs, add load balancer IP here if any problems occur
   *
   * @var array
   */
    protected $validIPs = array(
      '213.178.72.196',
      '213.178.72.197',
      '217.70.200.*',
      '185.60.20.*',
      );

    const PAYMENTS_ALL = [
        'mopt_payone__cc_visa',
        'mopt_payone__cc_mastercard',
        'mopt_payone__cc_american_express',
        'mopt_payone__cc_carte_blue',
        'mopt_payone__cc_diners_club',
        'mopt_payone__cc_jcb',
        'mopt_payone__cc_china_union',
        'mopt_payone__ibt_sofortueberweisung',
        'mopt_payone__ibt_eps',
        'mopt_payone__ibt_post_efinance',
        'mopt_payone__ibt_post_finance_card',
        'mopt_payone__ibt_ideal',
        'mopt_payone__ewallet_paypal',
        'mopt_payone__acc_debitnote',
        'mopt_payone__acc_invoice',
        'mopt_payone__acc_payinadvance',
        'mopt_payone__acc_cashondel',
        'mopt_payone__fin_klarna_old',
        'mopt_payone__fin_kis_klarna_installments',
        'mopt_payone__fin_kiv_klarna_invoice',
        'mopt_payone__fin_kdd_klarna_direct_debit',
        'mopt_payone__ibt_p24',
        'mopt_payone__fin_payolution_invoice',
        'mopt_payone__fin_payolution_debitnote',
        'mopt_payone__fin_payolution_installment',
        'mopt_payone__fin_ratepay_invoice',
        'mopt_payone__fin_ratepay_installment',
        'mopt_payone__fin_ratepay_direct_debit',
        'mopt_payone__acc_payone_safe_invoice',
        'mopt_payone__ibt_bancontact',
        'mopt_payone__ewallet_amazon_pay',
        'mopt_payone__ewallet_alipay',
        'mopt_payone__ewallet_wechatpay',
        'mopt_payone__fin_payone_secured_invoice',
        'mopt_payone__fin_payone_secured_installment',
        'mopt_payone__fin_payone_secured_directdebit',
        'mopt_payone__ewallet_paypalv2',
        'mopt_payone__ewallet_paypal_expressv2',
        'mopt_payone__ewallet_googlepay',
    ];

    const PAYMENTS_ADDRESSCHECK_EXCLUDED = [
        'mopt_payone__ewallet_amazon_pay',
        'mopt_payone__ewallet_paypal',
        'mopt_payone__ewallet_paypal_express',
        'mopt_payone__ewallet_applepay',
        'mopt_payone__ewallet_paypalv2',
        'mopt_payone__ewallet_paypal_expressv2',
    ];

    const PAYMENTS_ADDRESSCHECK_INCLUDED = [
        'mopt_payone__cc_visa',
        'mopt_payone__cc_mastercard',
        'mopt_payone__cc_american_express',
        'mopt_payone__cc_carte_blue',
        'mopt_payone__cc_diners_club',
        'mopt_payone__cc_jcb',
        'mopt_payone__cc_china_union',
        'mopt_payone__ibt_sofortueberweisung',
        'mopt_payone__ibt_eps',
        'mopt_payone__ibt_post_efinance',
        'mopt_payone__ibt_post_finance_card',
        'mopt_payone__ibt_ideal',
        'mopt_payone__acc_debitnote',
        'mopt_payone__acc_invoice',
        'mopt_payone__acc_payinadvance',
        'mopt_payone__acc_cashondel',
        'mopt_payone__fin_klarna_old',
        'mopt_payone__fin_kis_klarna_installments',
        'mopt_payone__fin_kiv_klarna_invoice',
        'mopt_payone__fin_kdd_klarna_direct_debit',
        'mopt_payone__ibt_p24',
        'mopt_payone__fin_payolution_invoice',
        'mopt_payone__fin_payolution_debitnote',
        'mopt_payone__fin_payolution_installment',
        'mopt_payone__fin_ratepay_invoice',
        'mopt_payone__fin_ratepay_installment',
        'mopt_payone__fin_ratepay_direct_debit',
        'mopt_payone__acc_payone_safe_invoice',
        'mopt_payone__ibt_bancontact',
        'mopt_payone__ewallet_alipay',
        'mopt_payone__ewallet_wechatpay',
        'mopt_payone__ewallet_googlepay',
    ];

    const PAYMENTS_EXCLUDED_FROM_ACCOUNTPAGE = [
        'mopt_payone__ewallet_amazon_pay',
        'mopt_payone__fin_payolution_installment',
        'mopt_payone__fin_ratepay_installment',
        'mopt_payone__ewallet_applepay',
        'mopt_payone__fin_klarna_old',
        'mopt_payone__fin_kis_klarna_installments',
        'mopt_payone__fin_kiv_klarna_invoice',
        'mopt_payone__fin_kdd_klarna_direct_debit',
        'mopt_payone_klarna',
        'mopt_payone__ewallet_paypal_express',
        'mopt_payone__ewallet_paypal_expressv2',
        'mopt_payone__ewallet_applepay',
        'mopt_payone__ewallet_googlepay',
    ];

    const PAYMENTS_EXCLUDED_FROM_SHIPPINGPAYMENTPAGE = [
        'mopt_payone__ewallet_amazon_pay',
        'mopt_payone__ewallet_paypal_express',
        'mopt_payone__ewallet_paypal_expressv2',
        // 'mopt_payone__ewallet_applepay', applepay is handled seperately
    ];

    const B2BPAYMENTS_EXCLUDED_FROM_SHIPPINGPAYMENTPAGE = [
        'mopt_payone__fin_payolution_debitnote',
        'mopt_payone__fin_payolution_installment',
        'mopt_payone__fin_payone_secured_installment',
        'mopt_payone__fin_payone_secured_directdebit',
        'mopt_payone__fin_kis_klarna_installments',
        'mopt_payone__fin_kdd_klarna_direct_debit',
    ];

    const PAYMENTS_NO_SHIPPINGADDRESS_ALLOWED = [
        'mopt_payone__fin_payone_secured_invoice',
        'mopt_payone__fin_payone_secured_installment',
        'mopt_payone__fin_payone_secured_directdebit',
    ];

    // for these payments basket AND address changes are not allowed
    const PAYMENTS_EXPRESS = [
        'mopt_payone__ewallet_paypal_express',
        'mopt_payone__ewallet_paypal_expressv2',
    ];

    // for these payments basket changes are not allowed after accepting installment conditions
    const PAYMENTS_INSTALLMENTS = [
        'mopt_payone__ewallet_amazon_pay',
        'mopt_payone__fin_kis_klarna_installments',
        'mopt_payone__fin_paypal_installment',
        'mopt_payone__fin_payolution_installment',
        'mopt_payone__fin_ratepay_installment',
        'mopt_payone__fin_payone_secured_installment',
    ];

    const PAYMENTS_DONOTSENDCAPTUREMODE = [
        'mopt_payone__acc_payone_safe_invoice',
        'mopt_payone__ewallet_alipay',
        'mopt_payone__ibt_trustly',
        'mopt_payone__ewallet_wechatpay',
        'mopt_payone__acc_cashondel',
    ];

  /**
   * return array with configured valid IPs to accept transaction feedback from
   *
   * @return array
   */
    public function getValidIPs()
    {
        return $this->validIPs;
    }

}
