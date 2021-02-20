/*
 * JS extension for Payone Payment Methods
 */

function moptPaymentReady() {

    $.plugin('moptPayoneIbanBicValidator', {
        defaults: {
            ibanbicReg: /^[A-Z0-9 ]+$/,
            errorMessageClass: 'register--error-msg',
            moptIbanErrorMessage: 'Dieses Feld darf nur Großbuchstaben und Ziffern enthalten'
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();

            me.$el.bind('keyup change', function (e) {
                $('#moptiban--message').remove();
                if (me.$el.val() && !me.opts.ibanbicReg.test(me.$el.val())) {
                    me.$el.addClass('has--error');
                    $('<div>', {
                        'html': '<p>' + me.opts.moptIbanErrorMessage + '</p>',
                        'id': 'moptiban--message',
                        'class': me.opts.errorMessageClass
                    }).insertAfter(me.$el);

                } else {
                    me.$el.removeClass('has--error');
                    $('#moptiban--message').remove();
                }
                ;
            });
        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptPayoneNumberValidator', {
        defaults: {
            numberReg: /^[0-9 ]+$/,
            errorMessageClass: 'register--error-msg',
            moptNumberErrorMessage: 'Dieses Feld darf nur Zahlen enthalten'
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();

            me.$el.bind('keyup change', function (e) {
                $('#moptnumber--message').remove();
                if (me.$el.val() && !me.opts.numberReg.test(me.$el.val())) {
                    me.$el.addClass('has--error');
                    $('<div>', {
                        'html': '<p>' + me.opts.moptNumberErrorMessage + '</p>',
                        'id': 'moptnumber--message',
                        'class': me.opts.errorMessageClass
                    }).insertAfter(me.$el);

                } else {
                    me.$el.removeClass('has--error');
                    $('#moptnumber--message').remove();
                }
                ;
            });
        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptPayoneBankcodeValidator', {
        defaults: {
            bankCodeReg: /^(?:\s*[0-9]\s*){8}$/,
            errorMessageClass: 'register--error-msg',
            moptBankCodeErrorMessage: 'Die Bankleitzahl muss aus 8 Ziffern bestehen'
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();

            me.$el.bind('keyup change', function (e) {
                $('#moptbankcode--message').remove();
                if (me.$el.val() && !me.opts.bankCodeReg.test(me.$el.val())) {
                    me.$el.addClass('has--error');
                    $('<div>', {
                        'html': '<p>' + me.opts.moptBankCodeErrorMessage + '</p>',
                        'id': 'moptbankcode--message',
                        'class': me.opts.errorMessageClass
                    }).insertAfter(me.$el);

                } else {
                    me.$el.removeClass('has--error');
                    $('#moptbankcode--message').remove();
                }
                ;
            });

        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptPayoneCardholderValidator', {
        defaults: {
            cardholderReg: /^[A-Za-z \-äöüÄÖÜß]{1,50}$/,
            errorMessageClass: 'register--error-msg',
            moptCardholderErrorMessage: 'Karteninhaber darf nur a-z, A-Z und Leerzeichen enthalten'
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();

            me.$el.bind('keyup change', function (e) {
                $('#moptcardholder--message').remove();
                if (me.$el.val() && !me.opts.cardholderReg.test(me.$el.val())) {
                    me.$el.addClass('has--error');
                    $('<div>', {
                        'html': '<p>' + me.opts.moptCardholderErrorMessage + '</p>',
                        'id': 'moptcardholder--message',
                        'class': me.opts.errorMessageClass
                    }).insertAfter(me.$el);

                } else {
                    me.$el.removeClass('has--error');
                    $('#moptcardholder--message').remove();
                }
                ;
            });

        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptPayoneSubmitPaymentForm', {
        init: function () {
            var me = this;

            if ($('#mopt_payone_creditcard_form').length > 0) {
                $('#mopt_payone_creditcard_form').moptPayoneCreditcardPrepare();
                // prepare and show Iframe or Display already checked and validated CreditcardData
                if (typeof $('#mopt_payone__cc_truncatedcardpan_hidden').val() !== 'undefined' && $('#mopt_payone__cc_truncatedcardpan_hidden').val().indexOf("XXXX") > 0) {
                    showhiddenCCFields();
                }

                var creditcardCheckType = $('#mopt_payone_creditcard_form').attr('data-moptCreditcardIntegration');
                if (typeof $('#mopt_payone_creditcard_form') !== "undefined") {
                    me.$el.bind('submit', function (e) {
                        if ($('#mopt_payone__cc_cardholder').hasClass('has--error')) {
                            e.preventDefault();
                        }

                        if ($('#payment_meanmopt_payone_creditcard').is(":checked")
                            && $('#mopt_payone__cc_truncatedcardpan').val().indexOf("XXXX") <= 0
                            && creditcardCheckType === '1') {
                            e.preventDefault();
                            if (typeof $('#mopt_payone_creditcard_form').data('plugin_moptPayoneCreditcardCheck') !== 'undefined') {
                                $('#mopt_payone_creditcard_form').data('plugin_moptPayoneCreditcardCheck').destroy();
                            }
                            $('#mopt_payone_creditcard_form').moptPayoneCreditcardCheck();
                        }
                        else if ($('#payment_meanmopt_payone_creditcard').is(":checked")
                            && creditcardCheckType === '0'
                            && $('#mopt_payone__cc_hostediframesubmit').val() === '1'
                            && $('#mopt_payone__cc_truncatedcardpan_hidden').val().indexOf("XXXX") <= 0
                        ) {
                            e.preventDefault();
                            if (typeof $('#mopt_payone_creditcard_form').data('plugin_moptPayoneIframeCreditcardCheck') !== 'undefined') {
                                $('#mopt_payone_creditcard_form').data('plugin_moptPayoneIframeCreditcardCheck').destroy();
                            }
                            $('#mopt_payone_creditcard_form').moptPayoneIframeCreditcardCheck();
                            return 'undefined';
                        } else if ($('#payment_meanmopt_payone_creditcard').is(":checked")) {
                            var data = {};
                            if (creditcardCheckType === '0'){
                                data.mopt_payone__cc_cardexpiredate = $('#mopt_payone__cc_cardexpireyear_hidden').val().substr(2,4) + $('#mopt_payone__cc_cardexpiremonth_hidden').val();
                            } else {
                                data.mopt_payone__cc_cardexpiredate = $('#mopt_payone__cc_Year').val().substr(2,4) + $('#mopt_payone__cc_month').val();
                            }
                            var success = expiryCheck(data);
                            if (success == true) {
                                return true;
                            } else {
                                e.preventDefault();
                                return false;
                            }
                        };
                    });
                }
            }

        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptPayoneCreditcardPrepare', {
        defaults: {
            mopt_payone__cc_paymentid: false,
            mopt_payone_credit_cards_id: '',
            mopt_payone__cc_Year: false,
            messageCreditCardCvcProcessed: 'Kartenprüfziffer wurde verarbeitet',
            moptPayoneParamsMode: '',
            moptPayoneParamsMid: '',
            moptPayoneParamsAid: '',
            moptPayoneParamsPortalid: '',
            moptPayoneParamsHash: '',
            moptPayoneParamsLanguage: '',
            moptCreditcardIntegration: '1',
            moptCreditcardConfig: '',
            mopt_payone__cc_paymentshort: '',
            mopt_payone_credit_cards_short: '',
            mopt_payone_available_cardtypes: '',
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();

            if (typeof window.Payone !== "undefined") {
                if (me.opts.moptCreditcardIntegration === 1) {
                    me.prepareAjaxCreditcardCheck();
                } else {
                    me.prepareIframeCreditcardCheck();
                }
            } else {
                if (me.opts.moptCreditcardIntegration === 1) {
                    $.getScript("https://secure.pay1.de/client-api/js/ajax.js")
                        .done(function (script, textStatus) {
                            me.prepareAjaxCreditcardCheck();
                        })
                        .fail(function (jqxhr, settings, exception) {
                            me.$el.closest(".payment--method").remove();
                            return;
                        });
                } else {
                    $.getScript("https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js")
                        .done(function (script, textStatus) {
                            me.prepareIframeCreditcardCheck();
                        })
                        .fail(function (jqxhr, settings, exception) {
                            me.$el.closest(".payment--method").remove();
                            return;
                        });
                }
            }
        },
        prepareAjaxCreditcardCheck: function () {
            var me = this;

            if (me.opts.mopt_payone__cc_paymentid) {
                $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_paymentid').val());
            } else {
                $('#payment_meanmopt_payone_creditcard').val(me.opts.mopt_payone_credit_cards_id);
            }

            if (me.opts.mopt_payone__cc_Year) {
                $('#mopt_payone__cc_Year').val(me.opts.mopt_payone__cc_Year);
            }

            if ($('#mopt_payone__cc_truncatedcardpan').val().indexOf("XXXX") >= 0) {
                $('#mopt_payone__cc_show_saved_hint').show();
                $('#mopt_payone__cc_cvc').val(me.opts.messageCreditCardCvcProcessed);
            }

            $('#mopt_payone__cc_cardtype').change(function () {
                $('#mopt_payone__cc_truncatedcardpan').val('');
                $('#mopt_payone__cc_cvc').val('');
                $('#mopt_payone__cc_show_saved_hint').hide();
            });


            $('#mopt_payone__cc_truncatedcardpan').click(function() {
                if ($('#mopt_payone__cc_truncatedcardpan').val().indexOf("XXXX") >= 0) {
                    $('#mopt_payone__cc_truncatedcardpan').val('');
                    $('#mopt_payone__cc_cvc').val('');
                    $('#mopt_payone__cc_show_saved_hint').hide();
                }
            });
        },
        prepareIframeCreditcardCheck: function () {
            var me = this;
            var request, config;

            if (me.opts.mopt_payone__cc_paymentid) {
                $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_paymentid').val());
            } else {
                $('#payment_meanmopt_payone_creditcard').val(me.opts.mopt_payone_credit_cards_id);
            }

            config = {
                fields: {
                    cardpan: {
                        selector: "mopt_payone__cc_truncatedcardpan",
                        type: me.opts.moptCreditcardConfig.cardno_field_type,
                        size: me.opts.moptCreditcardConfig.cardno_input_chars,
                        maxlength: me.opts.moptCreditcardConfig.cardno_input_chars_max,
                        iframe: {}
                    },
                    cardcvc2: {
                        selector: "mopt_payone__cc_cvc",
                        type: me.opts.moptCreditcardConfig.cardcvc_field_type,
                        size: me.opts.moptCreditcardConfig.cardcvc_input_chars,
                        maxlength: me.opts.moptCreditcardConfig.cardcvc_input_chars_max,
                        iframe: {}
                    },
                    cardexpiremonth: {
                        selector: "mopt_payone__cc_month",
                        type: me.opts.moptCreditcardConfig.cardmonth_field_type,
                        size: me.opts.moptCreditcardConfig.cardmonth_input_chars,
                        maxlength: me.opts.moptCreditcardConfig.cardmonth_input_chars_max,
                        iframe: {}
                    },
                    cardexpireyear: {
                        selector: "mopt_payone__cc_Year",
                        type: me.opts.moptCreditcardConfig.cardyear_field_type,
                        size: me.opts.moptCreditcardConfig.cardyear_input_chars,
                        maxlength: me.opts.moptCreditcardConfig.cardyear_input_chars_max,
                        iframe: {}
                    }
                },
                defaultStyle: {
                    input: me.opts.moptCreditcardConfig.standard_input_css,
                    select: me.opts.moptCreditcardConfig.standard_input_css_selected,
                    iframe: {
                        height: me.opts.moptCreditcardConfig.standard_iframe_height,
                        width: me.opts.moptCreditcardConfig.standard_iframe_width
                    }
                }
            };

            if (me.opts.moptCreditcardConfig.show_errors === '1') {
                config.error = "errorOutput";
            }
            config.language = eval('Payone.ClientApi.Language.' + me.opts.moptPayoneParamsLanguage);


            if (me.opts.moptCreditcardConfig.cardno_custom_style === '0') {
                config.fields.cardpan.style = me.opts.moptCreditcardConfig.cardno_input_css;
            }
            if (me.opts.moptCreditcardConfig.cardno_custom_iframe === '0') {
                config.fields.cardpan.iframe.width = me.opts.moptCreditcardConfig.cardno_iframe_width;
                config.fields.cardpan.iframe.height = me.opts.moptCreditcardConfig.cardno_iframe_height;
            }

            if (me.opts.moptCreditcardConfig.check_cc === '1') {
                config.fields.cardcvc2.length = {"A": 4, "V": 3, "M": 3};
            }

            if (me.opts.moptCreditcardConfig.cardcvc_custom_style === '0' && me.opts.moptCreditcardConfig.check_cc === '1') {
                config.fields.cardcvc2.style = me.opts.moptCreditcardConfig.cardcvc_input_css;
            }
            if (me.opts.moptCreditcardConfig.cardcvc_custom_iframe === '0' && me.opts.moptCreditcardConfig.check_cc === '1') {
                config.fields.cardcvc2.iframe.width = me.opts.moptCreditcardConfig.cardcvc_iframe_width;
                config.fields.cardcvc2.iframe.height = me.opts.moptCreditcardConfig.cardcvc_iframe_height;
            }

            if (me.opts.moptCreditcardConfig.cardmonth_custom_style === '0') {
                config.fields.cardexpiremonth.style = me.opts.moptCreditcardConfig.cardmonth_input_css;
            }
            if (me.opts.moptCreditcardConfig.cardmonth_custom_iframe === '0') {
                config.fields.cardexpiremonth.iframe.width = me.opts.moptCreditcardConfig.cardmonth_iframe_width;
                config.fields.cardexpiremonth.iframe.height = me.opts.moptCreditcardConfig.cardmonth_iframe_height;
            }

            if (me.opts.moptCreditcardConfig.cardyear_custom_style === '0') {
                config.fields.cardexpireyear.style = me.opts.moptCreditcardConfig.cardyear_input_css;
            }
            if (me.opts.moptCreditcardConfig.cardyear_custom_iframe === '0') {
                config.fields.cardexpireyear.iframe.width = me.opts.moptCreditcardConfig.cardyear_iframe_width;
                config.fields.cardexpireyear.iframe.height = me.opts.moptCreditcardConfig.cardyear_iframe_height;
            }

            var fcpolang = me.opts.moptPayoneParamsLanguage;

            if (Payone.ClientApi.Language[fcpolang] === undefined){
                console.log("language is not (yet) supported, falling back to english)");
                fcpolang = 'en';
            }

            if (me.opts.moptCreditcardConfig.default_translation_iframe_month1) {
                Payone.ClientApi.Language[fcpolang].months.month1 = me.opts.moptCreditcardConfig.default_translation_iframe_month1;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month2) {
                Payone.ClientApi.Language[fcpolang].months.month2 = me.opts.moptCreditcardConfig.default_translation_iframe_month2;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month3) {
                Payone.ClientApi.Language[fcpolang].months.month3 = me.opts.moptCreditcardConfig.default_translation_iframe_month3;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month4) {
                Payone.ClientApi.Language[fcpolang].months.month4 = me.opts.moptCreditcardConfig.default_translation_iframe_month4;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month5) {
                Payone.ClientApi.Language[fcpolang].months.month5 = me.opts.moptCreditcardConfig.default_translation_iframe_month5;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month6) {
                Payone.ClientApi.Language[fcpolang].months.month6 = me.opts.moptCreditcardConfig.default_translation_iframe_month6;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month7) {
                Payone.ClientApi.Language[fcpolang].months.month7 = me.opts.moptCreditcardConfig.default_translation_iframe_month7;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month8) {
                Payone.ClientApi.Language[fcpolang].months.month8 = me.opts.moptCreditcardConfig.default_translation_iframe_month8;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month9) {
                Payone.ClientApi.Language[fcpolang].months.month9 = me.opts.moptCreditcardConfig.default_translation_iframe_month9;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month10) {
                Payone.ClientApi.Language[fcpolang].months.month10 = me.opts.moptCreditcardConfig.default_translation_iframe_month10;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month11) {
                Payone.ClientApi.Language[fcpolang].months.month11 = me.opts.moptCreditcardConfig.default_translation_iframe_month11;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_month12) {
                Payone.ClientApi.Language[fcpolang].months.month12 = me.opts.moptCreditcardConfig.default_translation_iframe_month12;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframeinvalid_cardpan && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].invalidCardpan = me.opts.moptCreditcardConfig.default_translation_iframeinvalid_cardpan;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframeinvalid_cvc && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].invalidCvc = me.opts.moptCreditcardConfig.default_translation_iframeinvalid_cvc;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframeinvalid_pan_for_cardtype && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].invalidPanForCardtype = me.opts.moptCreditcardConfig.default_translation_iframeinvalid_pan_for_cardtype;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframeinvalid_cardtype && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].invalidCardtype = me.opts.moptCreditcardConfig.default_translation_iframeinvalid_cardtype;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframeinvalidExpireDate && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].invalidExpireDate = me.opts.moptCreditcardConfig.default_translation_iframeinvalidExpireDate;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframeinvalidIssueNumber && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].invalidIssueNumber = me.opts.moptCreditcardConfig.default_translation_iframeinvalidIssueNumber;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframetransactionRejected && me.opts.moptCreditcardConfig.show_errors === '1') {
                Payone.ClientApi.Language[fcpolang].transactionRejected = me.opts.moptCreditcardConfig.default_translation_iframetransactionRejected;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_cardpan) {
                Payone.ClientApi.Language[fcpolang].placeholders.cardpan = me.opts.moptCreditcardConfig.default_translation_iframe_cardpan;
            }
            if (me.opts.moptCreditcardConfig.default_translation_iframe_cvc && me.opts.moptCreditcardConfig.check_cc === '1') {
                Payone.ClientApi.Language[fcpolang].placeholders.cvc = me.opts.moptCreditcardConfig.default_translation_iframe_cvc;
            }

            // -- Auto CC Detection Start --

            window.PayoneAutoCcDetection = {

                /**
                 * Indicates whether the card type was set manually.
                 */
                _cardTypeWasManuallySet: false,

                /**
                 * Stores the last detection result.
                 */
                _lastDetectionResult: null,

                /**
                 * Returns true if the auto CC detection is enabled.
                 *
                 * @returns {boolean} True if auto CC detection is enabled.
                 */
                isEnabled: function () {
                    return me.opts.moptCreditcardConfig.auto_cardtype_detection === '1';
                },

                /**
                 * Returns the CC icon elements.
                 *
                 * @returns {jQuery}
                 */
                $ccIcons: function () {
                    return $('.payone-cc-icon');
                },

                /**
                 * Returns the card type select field element.
                 *
                 * @returns {jQuery}
                 */
                $ccTypeSelect: function () {
                    return $('#mopt_payone__cc_cardtype');
                },

                /**
                 * Returns the wrapper element for detection messages.
                 *
                 * @returns {jQuery}
                 */
                $detectionMessagesWrapper: function () {
                    return $('#payone-cc-auto-detection-messages');
                },

                /**
                 * Unselect all CC icons.
                 */
                unselectIcons: function () {
                    // Remove any --selected modifier class from CC icons.
                    PayoneAutoCcDetection.$ccIcons().removeClass('payone-cc-icon--selected');
                },

                /**
                 * Changes the selected icon based on the provided card type.
                 *
                 * @param {string} type The card type.
                 */
                changeIcon: function (type) {
                    PayoneAutoCcDetection.unselectIcons();
                    $('#payone-cc-icon-' + type.toLowerCase()).addClass('payone-cc-icon--selected');
                },

                /**
                 * Changes the payment additional description based on the provided card type.
                 *
                 * @param {string} type The card type.
                 */
                changeAdditionalDescription: function (type) {
                    $('.payone_additionalDescriptions').hide();
                    $('#' + type.toLowerCase() + '_additionalDescription').show();
                },

                /**
                 * Changes the value of the card type select field and triggers a change event.
                 *
                 * @param {string} type The new select field value.
                 */
                changeCardType: function (type) {
                    // Change value of card type select field and trigger change event manually.
                    PayoneAutoCcDetection.$ccTypeSelect()
                        .val(type.toUpperCase())
                        .trigger('change');
                },

                /**
                 * Marks the CC icons as clickable and registers the click handler.
                 */
                enableClickableIcons: function () {
                    PayoneAutoCcDetection.$ccIcons()
                        .addClass('payone-cc-icon--clickable')
                        .on('click.payone', PayoneAutoCcDetection.handleIconClick);
                },

                /**
                 * Un-marks the CC icons as clickable and unregisters the click handler.
                 */
                disableClickableIcons: function () {
                    PayoneAutoCcDetection.$ccIcons()
                        .removeClass('payone-cc-icon--clickable')
                        .off('click.payone');
                },

                /**
                 * Handles an icon click event.
                 */
                handleIconClick: function () {
                    // Hide error message.
                    PayoneAutoCcDetection.hideMessage();

                    // Get card type from the data-cc-type attribute value of the clicked icon.
                    var type = $(this).data('ccType').toUpperCase();

                    // Change card type select field to type.
                    PayoneAutoCcDetection.changeCardType(type);

                    // We need to set the card type manually here.
                    // Keep in mind that we cannot go back to valid auto detection
                    // state after setting the card type manually.
                    PayoneAutoCcDetection._cardTypeWasManuallySet = true;
                    iframes.setCardType(type);
                },

                /**
                 * Handles the unknown card type detection result.
                 */
                handleUnknownCardType: function () {
                    PayoneAutoCcDetection.unselectIcons();
                    PayoneAutoCcDetection.enableClickableIcons();
                    PayoneAutoCcDetection.prepareUnknownCardTypeMessage();
                    PayoneAutoCcDetection.showMessage();
                },

                /**
                 * Handles the unsupported card type detection result.
                 */
                handleUnsupportedCardType: function () {
                    PayoneAutoCcDetection.unselectIcons();
                    PayoneAutoCcDetection.disableClickableIcons();
                    PayoneAutoCcDetection.prepareUnsupportedCardTypeMessage();
                    PayoneAutoCcDetection.showMessage();
                },

                /**
                 * Handles a detected card type.
                 *
                 * @param {string} type
                 */
                handleDetectedCardType: function (type) {
                    PayoneAutoCcDetection.disableClickableIcons();

                    // Hide error message.
                    PayoneAutoCcDetection.hideMessage();

                    // Change the select field as well as the icon to the detected card type.
                    PayoneAutoCcDetection.changeCardType(type);
                    PayoneAutoCcDetection.changeIcon(type);
                },

                /**
                 * Shows the user message.
                 */
                showMessage: function () {
                    PayoneAutoCcDetection.$detectionMessagesWrapper().fadeIn(300);
                },

                /**
                 * Hides the user message.
                 */
                hideMessage: function () {
                    PayoneAutoCcDetection.$detectionMessagesWrapper().fadeOut(200);
                },

                /**
                 * Shows a user message that the current PAN
                 * results in an unknown card type.
                 */
                prepareUnknownCardTypeMessage: function () {
                    PayoneAutoCcDetection.$detectionMessagesWrapper().find('.payone-auto-cc-detection-message').hide();
                    PayoneAutoCcDetection.$detectionMessagesWrapper().find('.payone-auto-cc-detection-message[data-msg-type="unknown"]').show();
                },

                /**
                 * Shows a user message that the current PAN
                 * results in an unsupported card type.
                 */
                prepareUnsupportedCardTypeMessage: function () {
                    PayoneAutoCcDetection.$detectionMessagesWrapper().find('.payone-auto-cc-detection-message').hide();
                    PayoneAutoCcDetection.$detectionMessagesWrapper().find('.payone-auto-cc-detection-message[data-msg-type="unsupported"]').show();
                },

                /**
                 * Initializes the automatic card type detection.
                 */
                init: function () {
                    // Update hosted iFrame config to enable auto CC detection.
                    config.autoCardtypeDetection = {
                        deactivate: false,
                        supportedCardtypes: me.opts.mopt_payone_available_cardtypes.split(','),
                        callback: function (type) {
                            // Prevent auto detection if the card type was set manually.
                            if (PayoneAutoCcDetection._cardTypeWasManuallySet) {
                                // Ignore CC auto detection result because the card type was set manually.
                                // The Hosted Iframe JS reports valid card types only after the card type was set manually.
                                // Therefore we keep updating the card type icon.
                                // Change the select field as well as the icon to the detected card type.
                                PayoneAutoCcDetection.changeCardType(type);
                                PayoneAutoCcDetection.changeIcon(type);
                                return;
                            }

                            type = type.toUpperCase();

                            if (type === '?') {
                                PayoneAutoCcDetection.handleUnknownCardType();
                            }
                            else if (type === '-') {
                                PayoneAutoCcDetection.handleUnsupportedCardType();
                            }
                            else {
                                PayoneAutoCcDetection.handleDetectedCardType(type);
                            }

                            // Store current detection result as last result for next call.
                            PayoneAutoCcDetection._lastDetectionResult = type;
                        },
                    };

                    // Listen on card type field change for CC icon changes.
                    PayoneAutoCcDetection.$ccTypeSelect().on('change', function () {
                        PayoneAutoCcDetection.changeIcon(this.value);
                        PayoneAutoCcDetection.changeAdditionalDescription(this.value);
                    });
                },

            };

            if (PayoneAutoCcDetection.isEnabled()) {
                PayoneAutoCcDetection.init();
            }

            // -- Auto CC Detection End --

            request = {
                request: 'creditcardcheck',
                responsetype: 'JSON',
                mode: me.opts.moptPayoneParamsMode,
                mid: me.opts.moptPayoneParamsMid,
                aid: me.opts.moptPayoneParamsAid,
                portalid: me.opts.moptPayoneParamsPortalid,
                encoding: 'UTF-8',
                storecarddata: 'yes',
                hash: me.opts.moptPayoneParamsHash
            };

            iframes = new Payone.ClientApi.HostedIFrames(config, request);

            // Prevent setting card type manually due to strange behaviour of hosted iFrame API.
            if (!PayoneAutoCcDetection.isEnabled()) {
                if (me.opts.mopt_payone__cc_paymentshort) {
                    iframes.setCardType(me.opts.mopt_payone__cc_paymentshort);
                } else {
                    iframes.setCardType(me.opts.mopt_payone_credit_cards_short);
                }

                $('#mopt_payone__cc_cardtype').change(function () {
                    iframes.setCardType(this.value);
                });
            }
        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptPayoneCreditcardCheck', {
        defaults: {
            mopt_payone__cc_paymentid: false,
            mopt_payone_credit_cards_id: '',
            mopt_payone__cc_Year: false,
            messageCreditCardCvcProcessed: 'Kartenprüfziffer wurde verarbeitet',
            moptPayoneParamsMode: '',
            moptPayoneParamsMid: '',
            moptPayoneParamsAid: '',
            moptPayoneParamsPortalid: '',
            moptPayoneParamsHash: '',
            moptPayoneParamsLanguage: '',
            moptCreditcardMinValid: 0
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();
            me.checkCreditCard();
        },
        checkCreditCard: function () {
            var me = this;
            var today = new Date();
            var minValidDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + me.opts.moptCreditcardMinValid);
            var selectedDate = new Date($('#mopt_payone__cc_Year').val(), $('#mopt_payone__cc_month').val(), 0);
            var diff = selectedDate.getTime() - minValidDate.getTime();
            var cvcLengthCheck = false;
            var cvc = $('#mopt_payone__cc_cvc').val();

            if (cvc !== 'undefined') {
                switch($('#mopt_payone__cc_cardtype').val()) {
                    case 'A':
                        cvcLengthCheck = (cvc.length == 4);
                        break;
                    case 'V':
                        cvcLengthCheck = (cvc.length == 3);
                        break;
                    case 'M':
                        cvcLengthCheck = (cvc.length == 3);
                        break;
                    default:
                        cvcLengthCheck = true;
                }
            }

            if ( diff < 0 ) {
                $('#mopt_payone__cc_cvc').val('');
                processPayoneResponse(false);
            } else if (! cvcLengthCheck) {
                function Response(){
                    this.get = function (egal){
                        return 1079;
                    }
                }
                response = new Response();
                processPayoneResponse(response);
            }
            else {
                var data = {
                    request: 'creditcardcheck',
                    mode: me.opts.moptPayoneParamsMode,
                    mid: me.opts.moptPayoneParamsMid,
                    aid: me.opts.moptPayoneParamsAid,
                    portalid: me.opts.moptPayoneParamsPortalid,
                    encoding: 'UTF-8',
                    storecarddata: 'yes',
                    hash: me.opts.moptPayoneParamsHash,
                    cardholder: $('#mopt_payone__cc_accountholder').val(),
                    cardpan: $('#mopt_payone__cc_truncatedcardpan').val(),
                    cardtype: $('#mopt_payone__cc_cardtype').val(),
                    cardexpiremonth: $('#mopt_payone__cc_month').val(),
                    cardexpireyear: $('#mopt_payone__cc_Year').val(),
                    cardcvc2: $('#mopt_payone__cc_cvc').val(),
                    language: me.opts.moptPayoneParamsLanguage,
                    responsetype: 'JSON'
                };
                var options = {
                    return_type: 'object',
                    callback_function_name: 'processPayoneResponse'
                };
                var request = new PayoneRequest(data, options);
                $('#mopt_payone__cc_cvc').val('');
                request.checkAndStore();
            }
        },
        destroy: function () {
            var me = this;
            me._destroy();

        }
    });

    $.plugin('moptPayoneIframeCreditcardCheck', {
        init: function () {
            if (iframes.isComplete()) {
                iframes.creditCardCheck('processPayoneIframeResponse');
            } else if (
                iframes.isCardTypeComplete() &&
                iframes.isCardpanComplete() &&
                iframes.isExpireMonthComplete() &&
                iframes.isExpireYearComplete()) {
                iframes.creditCardCheck('processPayoneIframeResponse');
            } else {
                moptShowGeneralIFrameError();
            }
        },
        destroy: function () {
            var me = this;
            me._destroy();

        }
    });

    $.plugin('moptPayoneIframeCreditcardCheckWithoutSubmit', {
        init: function () {
            if (iframes.isComplete()) {
                iframes.creditCardCheck('processPayoneIframeResponseWithoutSubmit');
            } else if (
                iframes.isCardTypeComplete() &&
                iframes.isCardpanComplete() &&
                iframes.isExpireMonthComplete() &&
                iframes.isExpireYearComplete()) {
                iframes.creditCardCheck('processPayoneIframeResponseWithoutSubmit');
            } else {
                moptShowGeneralIFrameError();
            }
        },
        destroy: function () {
            var me = this;
            me._destroy();

        }
    });


    function poBindDispatchChange() {
        $("input[name='sDispatch']").on('change', function (e) {
            if (typeof $('#mopt_payone_creditcard_form').attr('data-moptCreditcardIntegration') !== "undefined") {
                var creditcardCheckType = $('#mopt_payone_creditcard_form').attr('data-moptCreditcardIntegration');
                $('#mopt_payone_creditcard_form').moptPayoneCreditcardPrepare();
                // prepare and show Iframe or Display already checked and validated CreditcardData
                if ($('#mopt_payone__cc_truncatedcardpan_hidden').val().indexOf("XXXX") > 0) {
                    showhiddenCCFields();
                }
                if ($('#payment_meanmopt_payone_creditcard').is(":checked")
                    && creditcardCheckType === '0'
                    && $('#mopt_payone__cc_hostediframesubmit').val() === '1'
                    && $('#mopt_payone__cc_truncatedcardpan_hidden').val().indexOf("XXXX") <= 0
                ) {
                    e.preventDefault();
                    if (typeof $('#mopt_payone_creditcard_form').data('plugin_moptPayoneIframeCreditcardCheck') !== 'undefined') {
                        $('#mopt_payone_creditcard_form').data('plugin_moptPayoneIframeCreditcardCheck').destroy();
                    }
                    $('#mopt_payone_creditcard_form').moptPayoneIframeCreditcardCheckWithoutSubmit();
                    return 'undefined';

                } else {
                    return true;
                }
                ;
            }
        });
    }

    function poBindCCSelectChange() {
        // show already pre selected description
        $('.payone_additionalDescriptions').hide();
        if ($('#mopt_payone__cc_cardtype').val()) {
            var selected = $('#mopt_payone__cc_cardtype').val().toLowerCase();
            $('#' + selected + '_additionalDescription').show();
        }
        $('#mopt_payone__cc_cardtype').on('change', function () {
            $('.payone_additionalDescriptions').hide();
            $('#' + this.value.toLowerCase() + '_additionalDescription').show();
        });
    }

    function moptShowGeneralIFrameError() {
        var $msg = $('#payone-general-iframe-error');

        if (!$msg.data('active')) {
            $msg.data('active', true);
            $msg.fadeIn(300);
            var errorCheck = function () {
                if (iframes.isComplete()) {
                    $msg.fadeOut(200, function () {
                        $msg.data('active', false);
                    });
                }
                else {
                    setTimeout(errorCheck, 200);
                }
            };
            setTimeout(errorCheck, 200);
        }
        document.getElementById('payone-general-iframe-error').scrollIntoView({behavior: "smooth", block: "end", inline: "center"});;
    }

    $.subscribe("plugin/swShippingPayment/onInputChanged", function () {
        poBindDispatchChange();
        poBindCCSelectChange();
    });


//define global iframe var
    var iframes;

//call the plugins
    poBindDispatchChange();
    poBindCCSelectChange();

    $('.moptPayoneIbanBic').moptPayoneIbanBicValidator();
    $('.moptPayoneNumber').moptPayoneNumberValidator();
    $('.moptPayoneBankcode').moptPayoneBankcodeValidator();
    $('.moptPayoneCardholder').moptPayoneCardholderValidator();
    $('#shippingPaymentForm').moptPayoneSubmitPaymentForm();
    $('form[name="frmRegister"]').moptPayoneSubmitPaymentForm();

}

var jsloadMethod = document.querySelector('#jsLoadMethod').value;
var isAsyncJsLoading = (jsloadMethod === 'async' || jsloadMethod === 'default');

if (isAsyncJsLoading) {
    $(document).ready(function(){
        if (typeof document.asyncReady == "undefined")
        {
            moptPaymentReady();
        }
    });

    if (typeof document.asyncReady !== "undefined") {
        document.asyncReady(function () {
            moptPaymentReady();
        });
    }
} else {
    moptPaymentReady();
}
