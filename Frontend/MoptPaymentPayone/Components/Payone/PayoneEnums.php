<?php

namespace Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone;

class PayoneEnums
{
    const APPROVED = 'APPROVED';
    const REDIRECT = 'REDIRECT';
    const VALID = 'VALID';
    const INVALID = 'INVALID';
    const BLOCKED = 'BLOCKED';
    const ENROLLED = 'ENROLLED';
    const ERROR = 'ERROR';
    const OK = 'OK';

    const PYV = 'PYV'; // Payolution-Invoicing
    const PYM = 'PYM'; // Payolution-Monthly
    const PYS = 'PYS'; // Payolution-Installment
    const PYD = 'PYD'; // Payolution-Debit

    const PYV_FULL = 'Payolution-Invoicing';
    const PYM_FULL = 'Payolution-Monthly';
    const PYS_FULL = 'Payolution-Installment';
    const PYD_FULL = 'Payolution-Debit';

    const PAYOLUTION_PRE_CHECK = 'pre_check';

    const PAYOLUTION_CALCULATION = 'calculation';

    const RATEPAY_PROFILE = 'profile';

    const RATEPAY_REQUEST_TYPE_CALCULATION = "calculation";

    // AddresscheckTypes
    const NONE = 'NO';
    const BASIC = 'BA';
    const PERSON = 'PE';
    const BONIVERSUM_BASIC = 'BB';
    const BONIVERSUM_PERSON = 'PB';

    // ConsumerScore
    const INFOSCORE_HARD = 'IH';
    const INFOSCORE_ALL = 'IA';
    const INFOSCORE_ALL_BONI = 'IB';
    const BONIVERSUM_VERITA = 'CE';

    const KLARNA_INSTALLMENTS = 'KIS';

    const KLARNA_DIRECT_DEBIT = 'KDD';

    const KLARNA_INVOICE = 'KIV';

    const MODE_LIVE = 'live';
    const MODE_TEST = 'test';

    const ADDRESS_CORRECT = '10';
    const ADDRESS_CORRECTABLE = '20';


    const AddressCheckPersonstatus_NONE = 'NONE';
    const AddressCheckPersonstatus_PPB = 'PPB';
    const AddressCheckPersonstatus_PHB = 'PHB';
    const AddressCheckPersonstatus_PAB = 'PAB';
    const AddressCheckPersonstatus_PKI = 'PKI';
    const AddressCheckPersonstatus_PNZ = 'PNZ';
    const AddressCheckPersonstatus_PPV = 'PPV';
    const AddressCheckPersonstatus_PPF = 'PPF';
    const AddressCheckPersonstatus_PNP = 'PNP';
    const AddressCheckPersonstatus_PUG = 'PUB';
    const AddressCheckPersonstatus_PUZ = 'PUZ';
    const AddressCheckPersonstatus_UKN = 'UKN';

    const InvoicingItemType_GOODS = 'goods';

    const InvoicingItemType_VOUCHER = 'voucher';

    const InvoicingItemType_HANDLING = 'handling';

    const InvoicingItemType_SHIPMENT = 'shipment';

    const CaptureMode_COMPLETED = 'completed';
    const CaptureMode_NOTCOMPLETED = 'notcompleted';

    const GenericpaymentAction_KLARNA_START_SESSION = 'start_session';
    const GenericpaymentAction_genericpayment = 'genericpayment';
    const CreditcardcheckAction = 'creditcardcheck';

    const FinancingType_KLV = 'KLV';
    const FinancingType_KIS = 'KIS';
    const FinancingType_KIV = 'KIV';
    const FinancingType_KDD = 'KDD';

    const B2B = 'b2b';
    const B2C = 'b2c';
    const INSTANT_MONEY_TRANSFER = 'PNT';
    const BANCONTACT = 'BCT';
    const EPS_ONLINE_BANK_TRANSFER = 'EPS';
    const POSTFINANCE_EFINANCE = 'PFF';
    const POSTFINANCE_CARD = 'PFC';
    const IDEAL = 'IDL';
    const P24 = 'P24';
    const DEBITPAYMENT = 'elv';
    const CREDITCARD = 'cc';
    const ADVANCEPAYMENT = 'vor';
    const INVOICE = 'rec';
    const ONLINEBANKTRANSFER = 'sb';
    const CASHONDELIVERY = 'cod';
    const WALLET = 'wlt';
    const FINANCING = 'fnc';
    const CASH = 'csh';
    const RATEPAY = 'fnc';
    const SAFEINVOICE = 'POV';
    const DHL = 'DHL';
    const BARTOLINI = 'BRT';
    const PIV = 'PIV';  // PAYONE secured Invoice
    const PIN = 'PIN'; // PAYONE secured installment
    const PDD = 'PDD'; // PAYONE secured direct debit
    const PAYONE_SECURED_INSTALLMENT_CALCULATE = "installment_options";
    const PAYPAL_EXPRESS = 'PPE';
    const PAYPAL_EXPRESSV2 = 'PAL';
    const PAYPAL_ECS_SET_EXPRESSCHECKOUT = 'setexpresscheckout';
    const PAYPAL_ECS_GET_EXPRESSCHECKOUTDETAILS = 'getexpresscheckoutdetails';
    const AMAZON_GETCONFIGURATION = "getconfiguration";
    const AMAZON_GETORDERREFERENCEDETAILS = "getorderreferencedetails";
    const AMAZON_SETORDERREFERENCEDETAILS = "setorderreferencedetails";
    const PAYPAL_INSTALLMENT_RESERVERVATION = "installment_reservation";
    const PAYPAL_INSTALLMENT_SALE = "installment_sale";
    const PAYPAL_INSTALLMENT_GET_PAYMENT = "get_Payment";
    const KLARNA_START_SESSION = "start_session";
    const YES = 'yes';
    const NO = 'no';
    const AUTO = 'auto';
    const RPV = 'RPV'; // Ratepay-Invoicing
    const RPV_FULL = 'Ratepay-Invoicing'; // Ratepay-Invoicing
    const RPS = 'RPS'; // Ratepay-Installment
    const RPD = 'RPD'; // Ratepay-Direct-Debit
    const AMAZONPAY = 'AMZ';
    const INTERNET = 'internet';
}