//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/view/main/detail"}
Ext.define('Shopware.apps.MoptConfigPayone.view.main.Detail', {
    extend: 'Ext.form.Panel',
    alias: 'widget.mopt-config-main-detail',
    autoScroll: true,
    cls: 'shopware-form',
    layout: 'anchor',
    border: false,
    url: '{url action=saveConfig}',
    bodyPadding: 10,
    initComponent: function () {
        var me = this;
        me.items = me.createTabpanel(me.getFieldSets(), me.data.payments);
        me.callParent(arguments);
        me.data.signal = Ext.create('Shopware.apps.MoptConfigPayone.store.ComboSignal');
        me.items.getAt(0).addListener('select', function (data) {
            var id = data.getValue();
            data = me.data.config.load({
                filters: [{
                        property: 'payment_id',
                        value: id
                    }],
                limit: 1,
                action: 'payment',
                callback: function (records) {
                    me.activateField(me, records[0].data.extra);
                    me.loadRecord(records[0]);
                }
            });
        });
        me.data.config.load({
            callback: function (records) {
                me.activateField(me, records[0].data.extra);
                me.loadRecord(records[0]);
            }
        });
    },
    createTabpanel: function (fieldsets, payments) {
        var me = this;
        return [
            {
                xtype: 'combobox',
                fieldLabel: '{s name="paymentMethod/label"}Gilt für Zahlart:{/s}',
                store: payments,
                displayField: 'description',
                tpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<tpl if="this.doHighlight(configSet)">',
                        '<div class="x-boundlist-item" style="background-color:#f08080;">{literal}{description}{/literal}</div>',
                        '<tpl else>',
                        '<div class="x-boundlist-item">{literal}{description}{/literal}</div>',
                        '</tpl>',
                        '</tpl>',
                        {
                            doHighlight: function (configSet) {
                                if (configSet === 1)
                                {
                                    return true;
                                }
                                else
                                {
                                    return false;
                                }
                            }
                        }
                ),
                valueField: 'id',
                name: 'paymentId',
                width: 600,
                allowBlank: true,
                value: 0
            },
            {
                xtype: 'button',
                text: '{s name="global-form/resetbutton"}Zurücksetzen{/s}',
                iconCls: 'sprite-tick-circle',
                name: 'reset',
                handler: function (a, b) {
                    me.submit({
                        params: {
                            type: 'reset'
                        },
                        success: function (form, action) {
                            Ext.Msg.alert('{s name="reset/payment"}Zahlartkonfiguration zurücksetzen{/s}', action.result.data);
                            payments.reload();
                        },
                        failure: function () {
                            Ext.Msg.alert('{s name="reset/payment"}Zahlartkonfiguration zurücksetzen{/s}', '{s name="reset/error"}Die Daten wurden nicht zurückgesetzt.{/s}');
                        },
                        waitTitle: '{s name="message/wait"}Bitte warten...{/s}',
                        waitMsg: '{s name="message/processing"}Daten werden verarbeitet{/s}'
                    });
                }
            },
            {
                xtype: 'button',
                text: '{s name="global-form/button"}Speichern{/s}',
                iconCls: 'sprite-tick-circle',
                name: 'save',
                handler: function (a, b) {
                    me.submit({
                        params: {
                            type: 'save'
                        },
                        success: function (form, action) {
                            Ext.Msg.alert('{s name="save/title"}Zahlartkonfiguration speichern{/s}', action.result.data);
                            payments.reload();
                        },
                        failure: function (form, action) {
                            var errorMessage = '{s name="save/error"}Die Daten konnten nicht gespeichert werden. Bitte überprüfen Sie ihre Eingaben.{/s}';
                            if(action.result.data) {
                                errorMessage = action.result.data;
                            }
                            Ext.Msg.alert('{s name="save/title"}Zahlartkonfiguration speichern{/s}', errorMessage);
                        },
                        waitTitle: '{s name="message/wait"}Bitte warten...{/s}',
                        waitMsg: '{s name="message/processing"}Daten werden verarbeitet{/s}'
                    });
                }
            },
            {
                xtype: 'tabpanel',
                items: fieldsets,
                renderTo: document.body,
                width: 880,
                height: 1700
            }];
    },
    activateField: function (me, field) {
        tabs = me.items.getAt(3);
        fieldset = tabs.items.getAt(0);
        if (!field) {
            fieldset.items.getAt(8).enable();
            fieldset.items.getAt(9).enable();
            fieldset.items.getAt(10).enable();
            fieldset.items.getAt(11).enable();
        } else if (field === 'debit') {
            fieldset.items.getAt(8).disable();
            fieldset.items.getAt(9).enable();
            fieldset.items.getAt(10).enable();
            fieldset.items.getAt(11).enable();
        } else {
            fieldset.items.getAt(8).disable();
            fieldset.items.getAt(9).disable();
            fieldset.items.getAt(10).disable();
            fieldset.items.getAt(11).disable();
        }
        ;
        if (field === 'klarna') {
            fieldset.items.getAt(15).enable();
        } else {
            fieldset.items.getAt(15).disable();
        }
        ;
        if (field === 'paypal' || !field) {
            fieldset.items.getAt(17).enable();
        } else {
            fieldset.items.getAt(17).disable();
        }
        ;
        if ( field === 'payolution_debitnote' || field === 'payolution_invoice' || field === 'payolution_installment') {
            fieldset.items.getAt(19).enable();
            fieldset.items.getAt(20).enable();
            fieldset.items.getAt(21).enable();
            fieldset.items.getAt(22).enable();
     
        } else {
            fieldset.items.getAt(19).disable();
            fieldset.items.getAt(20).disable();  
            fieldset.items.getAt(21).disable();
            fieldset.items.getAt(22).disable();
        }         
        ;
        if (field === 'sofort' || !field ) {
            fieldset.items.getAt(12).enable();
        } else {
            fieldset.items.getAt(12).disable();
        }
        ;
        if (field === 'payonesecured') {
            fieldset.items.getAt(36).enable();
        } else {
            fieldset.items.getAt(36).disable();
        }
        ;
        if (typeof field === 'undefined' || field.trim().length === 0) {
            fieldset.items.getAt(24).enable();
        } else {
            fieldset.items.getAt(24).disable();
        }
        ;
        if (!field) {
            fieldset.items.getAt(26).enable();
        } else {
            fieldset.items.getAt(26).disable();
        }
        ;
        if (field === 'p1') {
            fieldset.items.getAt(27).enable();
        } else {
            fieldset.items.getAt(27).disable();
        }
        ;
    },
    /**
     * creates form child elements
     * grouped in Ext.form.FieldSet
     */
    getFieldSets: function () {
        var me = this;
        return [
            {
                xtype: 'fieldset',
                defaults: {
                    anchor: '100%'
                },
                title: '{s name="global-form/fieldset1"}Allgemein{/s}',
                items: me.getGlobalSetItems(),
                flex: 1
            },
            {
                xtype: 'fieldset',
                defaults: {
                    anchor: '100%'
                },
                title: '{s name="global-form/fieldset2"}Adressüberprüfung{/s}',
                items: me.getRiskSetItems(),
                flex: 1
            },
            {
                xtype: 'fieldset',
                defaults: {
                    anchor: '100%'
                },
                title: '{s name="global-form/fieldset3"}Bonitätsprüfung{/s}',
                items: me.getConsumerSetItems(),
                flex: 1
            },
            {
                xtype: 'fieldset',
                defaults: {
                    anchor: '100%'
                },
                title: '{s name="global-form/fieldset4"}Paymentstatus{/s}',
                items: me.getPaymentStatus(),
                flex: 1
            },
            {
                xtype: 'fieldset',
                defaults: {
                    anchor: '100%'
                },
                title: '{s name="global-form/fieldset5"}Transaktionsstatusweiterleitung{/s}',
                items: me.getTransactionMapping(),
                flex: 1
            },
        ];
    },
    getTransactionMapping: function () {

        var me = this;
        return [
            {
                xtype: 'label',
                text: '{s name="forwarding/label"}Mehrere URLs können duch ; getrennt angegeben werden.{/s}',
                margin: '0 0 10 0',
                style: {
                    display: 'block !important'
                }
            },
            {
                name: 'transLogging',
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/logging"}Protokollierung{/s}',
                helpText: '{s name="forwarding/logging/help"}(de)aktivieren des Protokolls zur Weiterleitung (var/log/MoptPaymentPayone_txredirect_*.log){/s}',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                name: 'transTimeout',
                xtype: 'numberfield',
                fieldLabel: '{s name="forwarding/status/timeout"}Timeout{/s}',
                helpText: '{s name="forwarding/status/timeout/help"}Antwort Wartezeit in Millisekunden bis zu einem Abbruch{/s}',
                allowBlank: true,
                labelWidth: 200
            },
            {
                name: 'transTimeoutRaise',
                xtype: 'numberfield',
                fieldLabel: '{s name="forwarding/status/timeoutraise"}Timeout Erhöhung{/s}',
                helpText: '{s name="forwarding/status/timeoutraise/help"}Antwort Wartezeit in Millisekunden bis zu einem Abbruch{/s}',
                allowBlank: true,
                labelWidth: 200
            },
            {
                name: 'transMaxTrials',
                xtype: 'numberfield',
                fieldLabel: '{s name="forwarding/status/maxtries"}max. Anzahl Versuche{/s}',
                helpText: '{s name="forwarding/status/maxtries/help"}max. Anzahl Versuche bis zu einem Abbruch{/s}',
                allowBlank: true,
                labelWidth: 200,
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/appointed"}Appointed{/s}',
                name: 'transAppointed',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/capture"}Capture{/s}',
                name: 'transCapture',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/paid"}Paid{/s}',
                name: 'transPaid',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/underpaid"}Underpaid{/s}',
                name: 'transUnderpaid',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/cancelation"}Cancelation{/s}',
                name: 'transCancelation',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/refund"}Refund{/s}',
                name: 'transRefund',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/debit"}Debit{/s}',
                name: 'transDebit',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/reminder"}Reminder{/s}',
                name: 'transReminder',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/VAutorisierung"}VAutorisierung{/s}',
                name: 'transVauthorization',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/VSettlement"}VSettlement{/s}',
                name: 'transVsettlement',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/transfer"}Transfer{/s}',
                name: 'transTransfer',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/invoice"}Invoice{/s}',
                name: 'transInvoice',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="forwarding/status/failed"}Failed{/s}',
                name: 'transFailed',
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: true,
                labelWidth: 200
            }
        ];
    },
    getRiskSetItems: function () {
        var me = this;
        return [
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/active"}Aktiv{/s}',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'adresscheckActive',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mode"}Betriebsmodus{/s}',
                store: me.data.testlive,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'adresscheckLiveMode',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/billingAddress"}Rechnungsadresse{/s}',
                store: me.data.checkbasicperson,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'adresscheckBillingAdress',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/adresscheckBillingCountries"}Länder Rechnungsadresse{/s}',
                name: 'adresscheckBillingCountries',
                disabled: false,
                helpText: '{s name="fieldlabelhelp/adresscheckCountries"}Komme-getrennte ISO-Codes der Länder für die der Check ausgeführt werden soll. Z.b. DE,CH,AT{/s}',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/shippingAddress"}Lieferadresse{/s}',
                store: me.data.checkbasicperson,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'adresscheckShippingAdress',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/adresscheckShippingCountries"}Länder Lieferadresse{/s}',
                name: 'adresscheckShippingCountries',
                disabled: false,
                helpText: '{s name="fieldlabelhelp/adresscheckCountries"}Komma-getrennte ISO-Codes der Länder für die der Check ausgeführt werden soll. Z.B. DE,CH,AT{/s}',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/automaticCorrection"}Automatische Korrektur{/s}',
                store: me.data.yesnouser,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'adresscheckAutomaticCorrection',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/failureHandling"}Fehlverhalten{/s}',
                store: me.data.mistake,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'adresscheckFailureHandling',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                fieldLabel: '{s name="fieldlabel/minBasket"}Minimaler Warenwert{/s}',
                name: 'adresscheckMinBasket',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                fieldLabel: '{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}',
                name: 'adresscheckMaxBasket',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                fieldLabel: '{s name="fieldlabel/lifetime"}Gültigkeit{/s}',
                name: 'adresscheckLifetime',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/adresscheckFailureMessage"}Fehlermeldung{/s}',
                name: 'adresscheckFailureMessage',
                disabled: true,
                helpText: '{s name="fieldlabelhelp/adresscheckFailureMessage"}Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach addresscheckErrorMessage suchen){/s}',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapPersonCheck"}Keine Personenüberprüfung durchgeführt{/s}',
                name: 'mapPersonCheck',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapKnowPreLastname"}Vor- und Nachname bekannt{/s}',
                name: 'mapKnowPreLastname',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapKnowLastname"}Nachname bekannt{/s}',
                name: 'mapKnowLastname',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapNotKnowPreLastname"}Vor- und Nachname nicht bekannt{/s}',
                name: 'mapNotKnowPreLastname',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapMultiNameToAdress"}Mehrdeutigkeit bei Name zu Anschrift{/s}',
                name: 'mapMultiNameToAdress',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapUndeliverable"}nicht (mehr) zustellbar{/s}',
                name: 'mapUndeliverable',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapPersonDead"}Person verstorben{/s}',
                name: 'mapPersonDead',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapWrongAdress"}Adresse postalisch falsch{/s}',
                name: 'mapWrongAdress',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapAddressCheckNotPossible"}Überprüfung nicht möglich (z.B. Fakename){/s}',
                name: 'mapAddressCheckNotPossible',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapAddressOkayBuildingUnknown"}Adresse korrekt, aber Gebäude unbekannt{/s}',
                name: 'mapAddressOkayBuildingUnknown',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapPersonMovedAddressUnknown"}Person umgezogen, Adresse nicht korrigiert{/s}',
                name: 'mapPersonMovedAddressUnknown',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mapUnknownReturnValue"}Rückgabewert der Überprüfung unbekannt{/s}',
                name: 'mapUnknownReturnValue',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            }
        ];
    },
    getConsumerSetItems: function () {

        var me = this;
        return [
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/active"}Aktiv{/s}',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreActive',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mode"}Betriebsmodus{/s}',
                store: me.data.testlive,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreLiveMode',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/consumerscoreCheckMoment"}Zeitpunkt der Prüfung{/s}',
                store: me.data.point,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreCheckMoment',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/consumerscoreCheckModeB2C"}Prüfungsart B2C{/s}',
                store: me.data.infoscoreb2c,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreCheckModeB2C',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/consumerscoreCheckModeB2B"}Prüfungsart B2B{/s}',
                store: me.data.infoscoreb2b,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreCheckModeB2B',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/consumerscoreDefault"}Standardwert für Neukunden{/s}',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreDefault',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/consumerscoreBoniversumUnknown"}Boniversum unbekannt{/s}',
                store: me.data.signal,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreBoniversumUnknown',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                name: 'consumerscoreLifetime',
                fieldLabel: '{s name="fieldlabel/lifetime"}Gültigkeit{/s}',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                fieldLabel: '{s name="fieldlabel/minBasket"}Minimaler Warenwert{/s}',
                name: 'consumerscoreMinBasket',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                fieldLabel: '{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}',
                name: 'consumerscoreMaxBasket',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/failureHandling"}Fehlverhalten{/s}',
                store: me.data.consumerscore,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'consumerscoreFailureHandling',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: '{s name="fieldlabel/consumerscoreNote"}Hinweistext (nur bei Prüfung nach der Zahlartenauswahl){/s}',
                labelWidth: 200,
                layout: 'vbox',
                items: [
                    {
                        xtype: 'checkboxfield',
                        name: 'consumerscoreNoteActive',
                        checked: '1',
                        boxLabel: '{s name="fieldlabel/active"}Aktiv{/s}'
                    }, {
                        xtype: 'textfield',
                        name: 'consumerscoreNoteMessage',
                        disabled: true,
                        helpText: '{s name="fieldlabelhelp/consumerscoreNote"}Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach consumerscoreNoteMessage suchen){/s}',
                        width: '100%'
                    }
                ]
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: '{s name="fieldlabel/consumerscoreAgreement"}Zustimmungsfrage (nur bei Prüfung nach der Zahlartenauswahl){/s}',
                labelWidth: 200,
                layout: 'vbox',
                items: [
                    {
                        xtype: 'checkboxfield',
                        name: 'consumerscoreAgreementActive',
                        checked: 1,
                        boxLabel: '{s name="fieldlabel/active"}Aktiv{/s}'
                    }, {
                        xtype: 'textfield',
                        name: 'consumerscoreAgreementMessage',
                        disabled: true,
                        helpText: '{s name="fieldlabelhelp/consumerscoreAgreementMessage"}Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach consumerscoreAgreementMessage suchen){/s}',
                        width: '100%'
                    }
                ]
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: '{s name="fieldlabel/abtest"}A/B Test{/s}',
                labelWidth: 200,
                layout: 'vbox',
                items: [
                    {
                        xtype: 'checkboxfield',
                        name: 'consumerscoreAbtestActive',
                        checked: '1',
                        boxLabel: '{s name="fieldlabel/active"}Aktiv{/s}'
                    }, {
                        xtype: 'numberfield',
                        name: 'consumerscoreAbtestValue',
                        width: '100%'
                    }
                ]
            }
        ];
    },
    /**
     * helper function to create form elements of field set
     */
    getGlobalSetItems: function () {
        var me = this;
        return [
            {
                xtype: 'hidden',
                fieldLabel: 'id',
                name: 'id'
            },
            {
                //creates Ext.form.field.Text input field
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/merchantId"}Merchant-ID{/s}',
                helpText: '{s name="fieldlabelhelp/merchantId"}ID des zu verwendenden Accounts{/s}',
                name: 'merchantId',
                allowBlank: false,
                labelWidth: 200
            },
            {
                //creates Ext.form.field.Text input field
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/portalId"}Portal-ID{/s}',
                helpText: '{s name="fieldlabelhelp/portalId"}ID des zu verwendenden Zahlungsportal{/s}',
                name: 'portalId',
                allowBlank: false,
                labelWidth: 200
            },
            {
                //creates Ext.form.field.Text input field
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/subaccountId"}Subaccount-ID{/s}',
                helpText: '{s name="fieldlabelhelp/subaccountId"}ID des zu verwendenden SubAccounts{/s}',
                name: 'subaccountId',
                allowBlank: false,
                labelWidth: 200
            },
            {
                //creates Ext.form.field.Text input field
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/apiKey"}Schlüssel{/s}',
                helpText: '{s name="fieldlabelhelp/apiKey"}Schlüssel des zu verwendenden Zahlungsportal{/s}',
                name: 'apiKey',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/liveMode"}Betriebsmodus{/s}',
                helpText: '{s name="fieldlabelhelp/liveMode"}Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert{/s}',
                store: me.data.testlive,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'liveMode',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/authorisationMethod"}Autorisierung{/s}',
                helpText: '{s name="fieldlabelhelp/authorisationMethod"}Die Vorautorisation ist die Eröffnung eines Zahlvorgangs auf der PAYONE-Plattform. Wenn die Zahlart es zulässt wird eine Reservierung des Betrages durchgeführt. Bei Zahlarten wie Sofortueberweisung.de wird der Betrag sofort eingezogen weil dort keine Reservierung durchgeführt werden kann. Bei Zahlarten wie z.B. Vorkasse oder Rechnung wird der Zahlvorgang nur auf der PAYONE – Plattform angelegt. Wenn die Autorisation durchgeführt wird, dann wird wenn möglich der Betrag sofort eingezogen{/s}',
                name: 'authorisationMethod',
                store: me.data.auth,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/submitBasket"}Warenkorbübergabe{/s}',
                helpText: '{s name="fieldlabelhelp/submitBasket"}Soll der Warenkorbinhalt an PAYONE übermittelt werden?{/s}',
                name: 'submitBasket',
                store: me.data.submitbasket,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/checkCc"}Abfrage Kreditkartenprüfziffer<br>(nur global konfigurierbar){/s}',
                name: 'checkCc',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200,
                value: 'true'
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/checkAccount"}Bankdaten überprüfen{/s}',
                name: 'checkAccount',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/showAccountnumber"}Zusätzlich Kontonummer/Bankleitzahl anzeigen?{/s}',
                helpText: '{s name="fieldlabelhelp/showAccountnumber"}Nur bei deutschen Konten können zusätzlich Kontonummer/Bankleitzahl angezeigt werden.{/s}',
                name: 'showAccountnumber',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },            
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/showBIC"}Zusätzlich zur IBAN auch BIC abfragen?{/s}',
                helpText: '{s name="fieldlabelhelp/showBIC"}Zusätzlich zur IBAN auch BIC abfragen?{/s}',
                name: 'showBic',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/showSofortIbanBic"}Bei SOFORT Überweisung IBAN und BIC abfragen?{/s}',
                helpText: '{s name="fieldlabelhelp/showSofortIbanBic"}Bei SOFORT Überweisung IBAN und BIC abfragen?{/s}',
                name: 'showSofortIbanBic',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },            
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mandateActive"}Mandatserteilung aktivieren?{/s}',
                helpText: '{s name="fieldlabelhelp/mandateActive"}Die Mandatserteilung erfolgt mit dem kostenpflichtigen Request -managemandate-. Dieser Request beinhaltet einen bankaccountcheck. Allerdings ist hier keine Abfrage der POS-Sperrliste möglich.{/s}',
                name: 'mandateActive',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/mandateDownloadEnabled"}Download Mandat als PDF?{/s}',
                helpText: '{s name="fieldlabelhelp/mandateDownloadEnabled"}Diese Option kann nur ausgewählt werden, wenn bei PAYONE das Produkt SEPA-Mandate als PDF gebucht wurde.{/s}',
                name: 'mandateDownloadEnabled',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/klarnaStoreId"}Klarna Store-ID{/s}',
                helpText: '{s name="fieldlabelhelp/klarnaStoreId"}Klarna Store-ID{/s}',
                name: 'klarnaStoreId',
                allowBlank: true,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/saveTerms"}Speichern der AGB Bestätigung{/s}',
                helpText: '{s name="fieldlabelhelp/saveTerms"}Sobald die AGB einmal bestätigt wurden, wird dies gespeichert und die Checkbox dementsprechend vorausgewählt{/s}',
                store: me.data.terms,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'saveTerms',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/paypalEcsActive"}PayPal ECS Button auf Warenkorbseite anzeigen?{/s}',
                helpText: '{s name="fieldlabelhelp/paypalEcsActive"}Mit PayPal Express Checkout Shortcut können Kunden einfach mit PayPal in Ihrem Shop bestellen ohne sich registrieren zu müssen.{/s}',
                name: 'paypalEcsActive',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'numberfield',
                fieldLabel: '{s name="fieldlabel/creditcardMinValid"}Gültigkeit der Kreditkarte{/s}',
                helpText: '{s name="fieldlabelhelp/creditcardMinValid"}Gültigkeit der Kreditkarte in Tagen zudem eine Kreditkarte im Checkout akzeptiert wird.{/s}',
                name: 'creditcardMinValid',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/payolutionCompanyName"}Payolution Firmenname{/s}',
                helpText: '{s name="fieldlabelhelp/payolutionCompanyName"}Payolution Firmenname{/s}',
                name: 'payolutionCompanyName',
                allowBlank: true,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/payolutionB2bMode"}Payolution B2B Modus{/s}',
                helpText: '{s name="fieldlabelhelp/payolutionB2bMode"}Payolution B2B Modus{/s}',
                name: 'payolutionB2bmode',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/payolutionDraftUser"}Payolution-Benutzername{/s}',
                helpText: '{s name="fieldlabelhelp/payolutionDraftUser"}Payolution HTTP-Benutzername{/s}',
                name: 'payolutionDraftUser',
                allowBlank: true,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/payolutionDraftPassword"}Payolution-Passwort{/s}',
                helpText: '{s name="fieldlabelhelp/payolutionDraftPassword"}Payolution HTTP-Passwort{/s}',
                name: 'payolutionDraftPassword',
                allowBlank: true,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/sendOrdernumberAsReference"}Benutze Shopware-Bestellnummer{/s}',
                helpText: '{s name="fieldlabelhelp/sendOrdernumberAsReference"}Sendet die Shopware Bestellnummer anstatt einen Zufallswert an Payone{/s}',
                name: 'sendOrdernumberAsReference',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/changeOrderOnTXS"}Bestellung geändert bei TX Status{/s}',
                helpText: '{s name="fieldlabelhelp/changeOrderOnTXS"}Setze das changed Datum einer Bestellung, wenn ein Transaktions-Status erfolgreich war. Greift erst ab Shopware Version 5.5.0{/s}',
                name: 'changeOrderOnTXS',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/ratepaySnippetId"}Ratepay SnippetId{/s}',
                helpText: '{s name="fieldlabelhelp/ratepaySnippetId"}Ratepay SnippetId{/s}',
                name: 'ratepaySnippetId',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/applepayMerchantID"}Apple Pay MerchantId{/s}',
                helpText: '{s name="fieldlabelhelp/applepayMerchantID"}Ihre Apple Pay MerchantId{/s}',
                name: 'applepayMerchantId',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/applepayVisa"}Apple Pay Visa erlauben{/s}',
                helpText: '{s name="fieldlabelhelp/applepayVisa"}Erlaubt Visa Karten über Apple Pay{/s}',
                name: 'applepayVisa',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/applepayMastercard"}Apple Pay Mastercard erlauben{/s}',
                helpText: '{s name="fieldlabelhelp/applepayMastercard"}Erlaubt Mastercard Karten über Apple Pay{/s}',
                name: 'applepayMastercard',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/applepayGirocard"}Apple Pay Girocard erlauben{/s}',
                helpText: '{s name="fieldlabelhelp/applepayGirocard"}Erlaubt Girocard Karten über Apple Pay{/s}',
                name: 'applepayGirocard',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            /*
           {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/applepayAmex"}Apple Pay American Express erlauben{/s}',
                helpText: '{s name="fieldlabelhelp/applepayAmex"}Erlaubt American Express Karten über Apple Pay{/s}',
                name: 'applepayAmex',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/applepayDisover"}Apple Pay Discover erlauben{/s}',
                helpText: '{s name="fieldlabelhelp/applepayDiscover"}Erlaubt Discover Karten über Apple Pay{/s}',
                name: 'applepayDiscover',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            */
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/applepayCertificate"}Apple Pay Zertifikat{/s}',
                helpText: '{s name="fieldlabelhelp/applepayCertificate"}Absoluter Pfad zum Zertifikat. Wird beim Hochladen automatisch gesetzt{/s}',
                name: 'applepayCertificate',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/applepayPrivateKey"}Apple Pay Private Key{/s}',
                helpText: '{s name="fieldlabelhelp/applepayPrivateKey"}Absoluter Pfad zum Private Key. Wird beim Hochladen automatisch gesetzt{/s}',
                name: 'applepayPrivateKey',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                inputType: 'password',
                fieldLabel: '{s name="fieldlabel/applepayPrivateKeyPassword"}Apple Pay Private Key Passwort{/s}',
                helpText: '{s name="fieldlabelhelp/applepayPrivateKeyPassword"}Kann auch dann gesetzt werden, wenn der Key unverschlüsselt ist{/s}',
                name: 'applepayPrivateKeyPassword',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/applepayDebug"}Apple Pay Debug{/s}',
                helpText: '{s name="fieldlabelhelp/applepayDebug"}Zeigt im Frontend die Debugging Informationen an{/s}',
                name: 'applepayDebug',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/allowDifferentAdresses"}Abweichende Lieferadressen zulassen{/s}',
                helpText: '{s name="fieldlabelhelp/allowDifferentAdresses"}Hinweis: Muss im PAYONE-Konto freigeschaltet sein!{/s}',
                name: 'allowDifferentAddresses',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/creditcardDefaultDescription"}Kreditkarte Zusätzliche Beschreibung{/s}',
                helpText: '{s name="fieldlabelhelp/creditcardDefaultDescription"}Zusätzliche Beschreibung der Zahlart bei Gruppierung{/s}',
                name: 'creditcardDefaultDescription',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/paypalExpressUseDefaultShipping"}Vorläufigen Versandkosten bei Paypal Express übergeben{/s}',
                helpText: '{s name="fieldlabelhelp/paypalExpressUseDefaultShipping"}Wenn aktiviert, werden die vorläufigen Versandkosten mit an Paypal Express übergeben{/s}',
                name: 'paypalExpressUseDefaultShipping',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/paypalV2ShowButton"}Paypal V2 BNPL Button anzeigen{/s}',
                helpText: '{s name="fieldlabelhelp/paypalV2ShowButton"}{/s}',
                name: 'paypalV2ShowButton',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/paypalV2MerchantId"}Paypal V2 Merchant ID{/s}',
                helpText: '{s name="fieldlabelhelp/paypalV2MerchantId"}Im Testmodus NICHT erforderlich. Da wird eine feste ID von Payone verwendet.{/s}',
                name: 'paypalV2MerchantId',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/paypalV2ButtonColor"}Paypal V2 Express Button Farbe{/s}',
                helpText: '{s name="fieldlabelhelp/paypalV2ButtonColor"}Paypal V2 Express Button Farbe{/s}',
                store: me.data.paypalv2expressbuttoncolor,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'paypalV2ButtonColor',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/paypalV2ButtonShape"}Paypal V2 Express Button Form{/s}',
                helpText: '{s name="fieldlabelhelp/paypalV2ButtonShape"}Paypal V2 Express Button Form{/s}',
                store: me.data.paypalv2expressbuttonshape,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'paypalV2ButtonShape',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/googlepayMerchantId"}GooglePay Merchant ID{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayMerchantId"}GooglePay Merchant ID{/s}',
                name: 'googlepayMerchantId',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/googlepayAllowVisa"}GooglePay Visa zulassen{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayAllowVisa"}GooglePay Visa zulassen{/s}',
                name: 'googlepayAllowVisa',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/googlepayAllowMasterCard"}GooglePay Mastercard zulassen{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayAllowMasterCard"}GooglePay Mastercard zulassen{/s}',
                name: 'googlepayAllowMasterCard',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/googlepayAllowPrepaidCards"}GooglePay Prepaid Karten zulassen{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayAllowPrepaidCards"}GooglePay Prepaid Karten zulassen{/s}',
                name: 'googlepayAllowPrepaidCards',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/googlepayAllowCreditCards"}GooglePay Kreditkarten zulassen{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayAllowCreditCards"}GooglePay Kreditkarten zulassen{/s}',
                name: 'googlepayAllowCreditCards',
                store: me.data.yesno,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                allowBlank: false,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: '{s name="fieldlabel/googlepayCountryCode"}GooglePay Acquirer Ländercode{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayCountryCode"}GooglePay Acquirer Ländercode{/s}',
                name: 'googlepayCountryCode',
                allowBlank: true,
                disabled: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/googlepayButtonColor"}Googlepay Button Farbe{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayButtonColor"}Googlepay Button Farbe{/s}',
                store: me.data.googlepaybuttoncolor,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'googlepayButtonColor',
                allowBlank: true,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="fieldlabel/googlepayButtonType"}GooglePay Button Typ{/s}',
                helpText: '{s name="fieldlabelhelp/googlepayButtonType"}GooglePay Button Typ{/s}',
                store: me.data.googlepaybuttontype,
                queryMode: 'local',
                displayField: 'display',
                valueField: 'value',
                name: 'googlepayButtonType',
                allowBlank: true,
                labelWidth: 200,
                default: 'buy'
            },
        ];
    },
    getPaymentStatus: function () {

        var me = this;
        return [
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/appointed"}Appointed{/s}',
                name: 'stateAppointed',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/capture"}Capture{/s}',
                name: 'stateCapture',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/paid"}Paid{/s}',
                name: 'statePaid',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/underpaid"}Underpaid{/s}',
                name: 'stateUnderpaid',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/cancelation"}Cancelation{/s}',
                name: 'stateCancelation',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/refund"}Refund{/s}',
                name: 'stateRefund',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/debit"}Debit{/s}',
                name: 'stateDebit',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminder1"}Reminder (1){/s}',
                name: 'stateReminder',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminder2"}Reminder (2){/s}',
                name: 'stateReminder2',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminder3"}Reminder (3){/s}',
                name: 'stateReminder3',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminder4"}Reminder (4){/s}',
                name: 'stateReminder4',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminder5"}Reminder (5){/s}',
                name: 'stateReminder5',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminderA"}Reminder (A){/s}',
                name: 'stateReminderA',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminderS"}Reminder (S){/s}',
                name: 'stateReminderS',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminderM"}Reminder (M){/s}',
                name: 'stateReminderM',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/reminderI"}Reminder (I){/s}',
                name: 'stateReminderI',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/VAutorisierung"}VAutorisierung{/s}',
                name: 'stateVauthorization',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/VSettlement"}VSettlement{/s}',
                name: 'stateVsettlement',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/transfer"}Transfer{/s}',
                name: 'stateTransfer',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/invoice"}Invoice{/s}',
                name: 'stateInvoice',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            },
            {
                xtype: 'combobox',
                fieldLabel: '{s name="forwarding/status/failed"}Failed{/s}',
                name: 'stateFailed',
                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200
            }
        ];
    }
});
//{/block}
