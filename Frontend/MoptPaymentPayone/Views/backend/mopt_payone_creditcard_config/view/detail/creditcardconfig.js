//{namespace name=backend/mopt_payone_creditcard_config/main}
Ext.define('Shopware.apps.MoptPayoneCreditcardConfig.view.detail.Creditcardconfig', {
    extend: 'Shopware.model.Container',
    padding: 20,
    
    
    
    
    configure: function () {

        var me = this;
        
        me.customStore =  Ext.create('Ext.data.Store', {
                                fields: [
                                    { type: 'boolean', name: 'value' },
                                    { type: 'string', name: 'name' }
                                ],
                                data: [
                                    { value: true, name: '{s name=customIframeStandard}Standard{/s}' },
                                    { value: false, name: '{s name=customIframeCustom}Benutzerdefiniert{/s}' }
                                ]
                            });
                            
        me.inputFieldStore = Ext.create('Ext.data.Store', {
                                fields: [
                                    { type: 'string', name: 'value' },
                                    { type: 'string', name: 'name' }
                                ],
                                data: [
                                    { value: 'tel', name: '{s name=fieldTypeNumeric}Numerisch{/s}' },
                                    { value: 'password', name: '{s name=fieldTypePassword}Passwort{/s}' },
                                    { value: 'text', name: '{s name=fieldTypeText}Text{/s}' },
                                    { value: 'select', name: '{s name=fieldTypeSelect}Auswahl{/s}' }
                                ]
                            });

        return {
            controller: 'MoptPayoneCreditcardConfig',
            fieldSets: [
                {
                    title: '{s name=generalConfig}Allgemeine Konfiguration{/s}',
                    layout: 'column',
                    fields: {
                        integrationType: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=integrationType}Anfragetyp{/s}',
                            name: 'integrationType',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: Ext.create('Ext.data.Store', {
                                fields: [
                                    { type: 'int', name: 'value' },
                                    { type: 'string', name: 'name' }
                                ],
                                data: [
                                    { value: 0, name: '{s name=integrationTypeHostedIframe}hosted-iFrame{/s}' },
                                    { value: 1, name: '{s name=integrationTypeAjaxCall}AJAX{/s}' }
                                ]
                            })
                        },
                        shopId: {
                            fieldLabel: '{s name=shop}Shop{/s}',
                            name: 'shopId',
                            allowBlank: false
                        },
                        isDefault: {
                            fieldLabel: '{s name=isDefault}Default{/s}',
                            name: 'isDefault',
                            allowBlank: true
                        },
                        merchantId: {
                            xtype: 'textfield',
                            fieldLabel: '{s name=fieldlabel/merchantId}Merchant-ID{/s}',
                            helpText: '{s name=fieldlabelhelp/merchantId}ID des zu verwendenden Accounts{/s}',
                            name: 'merchantId',
                            allowBlank: false
                        },
                        portalId: {
                            xtype: 'textfield',
                            fieldLabel: '{s name=fieldlabel/portalId}Portal-ID{/s}',
                            helpText: '{s name=fieldlabelhelp/portalId}ID des zu verwendenden Zahlungsportal{/s}',
                            name: 'portalId',
                            allowBlank: false
                        },
                        subaccountId: {
                            xtype: 'textfield',
                            fieldLabel: '{s name=fieldlabel/subaccountId}Subaccount-ID{/s}',
                            helpText: '{s name=fieldlabelhelp/subaccountId}ID des zu verwendenden SubAccounts{/s}',
                            name: 'subaccountId',
                            allowBlank: false
                        },
                        apiKey: {
                            xtype: 'textfield',
                            fieldLabel: '{s name=fieldlabel/apiKey}Schlüssel{/s}',
                            helpText: '{s name=fieldlabelhelp/apiKey}Schlüssel des zu verwendenden Zahlungsportal{/s}',
                            name: 'apiKey',
                            allowBlank: false
                        },
                        liveMode: '{s name=fieldlabel/liveMode}Livemodus{/s}',
                        checkCc: '{s name=fieldlabel/checkCc}Kreditkartenprüfziffer abfragen{/s}',
                        creditcardMinValid: {
                            xtype: 'numberfield',
                            fieldLabel: '{s name=fieldlabel/creditcardMinValid}Gültigkeit der Kreditkarte{/s}',
                            helpText: '{s name=fieldlabelhelp/creditcardMinValid}Gültigkeit der Kreditkarte in Tagen zudem eine Kreditkarte im Checkout akzeptiert wird.{/s}',
                            name: 'creditcardMinValid',
                            allowBlank: false
                        }
                    }
                },
                {
                    title: '{s name=fieldConfigurationCardno}Feldkonfiguration Kreditkartennummer{/s}',
                    layout: 'column',
                    fields: {
                        cardnoFieldType: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=fieldType}Feldtyp{/s}',
                            name: 'cardnoFieldType',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.inputFieldStore
                        },
                        cardnoInputChars: {
                            fieldLabel: '{s name=inputChars}Zeichenanzahl{/s}',
                            name: 'cardnoInputChars',
                            allowBlank: false
                        },
                        cardnoInputCharsMax: {
                            fieldLabel: '{s name=inputCharsMax}Zeichenanzahl Max{/s}',
                            name: 'cardnoInputCharsMax',
                            allowBlank: false
                        },
                        cardnoCustomIframe: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=iframe}iFrame{/s}',
                            name: 'cardnoCustomIframe',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardnoIframeWidth: {
                            fieldLabel: '{s name=iframeWidth}iFrame Breite{/s}',
                            name: 'cardnoIframeWidth',
                            allowBlank: true
                        },
                        cardnoIframeHeight: {
                            fieldLabel: '{s name=iframeHeight}iFrame Höhe{/s}',
                            name: 'cardnoIframeHeight',
                            allowBlank: true
                        },
                        cardnoCustomStyle: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=style}Stil{/s}',
                            name: 'cardnoCustomStyle',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardnoInputCss: {
                            fieldLabel: '{s name=css}CSS{/s}',
                            name: 'cardnoInputCss',
                            allowBlank: true
                        }
                    }
                },
                {
                    title: '{s name=fieldConfigurationCardcvc}Feldkonfiguration Kartenprüfziffer{/s}',
                    layout: 'column',
                    fields: {
                        cardcvcFieldType: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=fieldType}Feldtyp{/s}',
                            name: 'cardcvcFieldType',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.inputFieldStore
                        },
                        cardcvcInputChars: {
                            fieldLabel: '{s name=inputChars}Zeichenanzahl{/s}',
                            name: 'cardcvcInputChars',
                            allowBlank: false
                        },
                        cardcvcInputCharsMax: {
                            fieldLabel: '{s name=inputCharsMax}Zeichenanzahl Max{/s}',
                            name: 'cardcvcInputCharsMax',
                            allowBlank: false
                        },
                        cardcvcCustomIframe: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=iframe}iFrame{/s}',
                            name: 'cardcvcCustomIframe',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardcvcIframeWidth: {
                            fieldLabel: '{s name=iframeWidth}iFrame Breite{/s}',
                            name: 'cardcvcIframeWidth',
                            allowBlank: true
                        },
                        cardcvcIframeHeight: {
                            fieldLabel: '{s name=iframeHeight}iFrame Höhe{/s}',
                            name: 'cardcvcIframeHeight',
                            allowBlank: true
                        },
                        cardcvcCustomStyle: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=style}Stil{/s}',
                            name: 'cardcvcCustomStyle',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardcvcInputCss: {
                            fieldLabel: '{s name=css}CSS{/s}',
                            name: 'cardcvcInputCss',
                            allowBlank: true
                        }
                    }
                },
                {
                    title: '{s name=fieldConfigurationCardmonth}Feldkonfiguration Gültigkeitsmonat{/s}',
                    layout: 'column',
                    fields: {
                        cardmonthFieldType: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=fieldType}Feldtyp{/s}',
                            name: 'cardmonthFieldType',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.inputFieldStore
                        },
                        cardmonthInputChars: {
                            fieldLabel: '{s name=inputChars}Zeichenanzahl{/s}',
                            name: 'cardmonthInputChars',
                            allowBlank: false
                        },
                        cardmonthInputCharsMax: {
                            fieldLabel: '{s name=inputCharsMax}Zeichenanzahl Max{/s}',
                            name: 'cardmonthInputCharsMax',
                            allowBlank: false
                        },
                        cardmonthCustomIframe: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=iframe}iFrame{/s}',
                            name: 'cardmonthCustomIframe',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardmonthIframeWidth: {
                            fieldLabel: '{s name=iframeWidth}iFrame Breite{/s}',
                            name: 'cardmonthIframeWidth',
                            allowBlank: true
                        },
                        cardmonthIframeHeight: {
                            fieldLabel: '{s name=iframeHeight}iFrame Höhe{/s}',
                            name: 'cardmonthIframeHeight',
                            allowBlank: true
                        },
                        cardmonthCustomStyle: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=style}Stil{/s}',
                            name: 'cardmonthCustomStyle',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardmonthInputCss: {
                            fieldLabel: '{s name=css}CSS{/s}',
                            name: 'cardmonthInputCss',
                            allowBlank: true
                        }
                    }
                },
                {
                    title: '{s name=fieldConfigurationCardyear}Feldkonfiguration Gültigkeitsjahr{/s}',
                    layout: 'column',
                    fields: {
                        cardyearFieldType: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=fieldType}Feldtyp{/s}',
                            name: 'cardyearFieldType',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.inputFieldStore
                        },
                        cardyearInputChars: {
                            fieldLabel: '{s name=inputChars}Zeichenanzahl{/s}',
                            name: 'cardyearInputChars',
                            allowBlank: false
                        },
                        cardyearInputCharsMax: {
                            fieldLabel: '{s name=inputCharsMax}Zeichenanzahl Max{/s}',
                            name: 'cardyearInputCharsMax',
                            allowBlank: false
                        },
                        cardyearCustomIframe: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=iframe}iFrame{/s}',
                            name: 'cardyearCustomIframe',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardyearIframeWidth: {
                            fieldLabel: '{s name=iframeWidth}iFrame Breite{/s}',
                            name: 'cardyearIframeWidth',
                            allowBlank: true
                        },
                        cardyearIframeHeight: {
                            fieldLabel: '{s name=iframeHeight}iFrame Höhe{/s}',
                            name: 'cardyearIframeHeight',
                            allowBlank: true
                        },
                        cardyearCustomStyle: {
                            xtype: 'combobox',
                            fieldLabel: '{s name=style}Stil{/s}',
                            name: 'cardyearCustomStyle',
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            editable: false,
                            allowBlank: false,
                            store: me.customStore
                        },
                        cardyearInputCss: {
                            fieldLabel: '{s name=css}CSS{/s}',
                            name: 'cardyearInputCss',
                            allowBlank: true
                        }
                    }
                },
                {
                    title: '{s name=defaultStyle}Standardstil{/s}',
                    layout: 'column',
                    fields: {
                        standardInputCss: {
                            fieldLabel: '{s name=inputCss}Felder: Eingabe{/s}',
                            name: 'standardInputCss',
                            allowBlank: false
                        },
                        standardInputCssSelected: {
                            fieldLabel: '{s name=inputCssSelected}Felder: Auswahl{/s}',
                            name: 'standardInputCssSelected',
                            allowBlank: false
                        },
                        standardIframeHeight: {
                            fieldLabel: '{s name=iframeHeight}iFrame Höhe{/s}',
                            name: 'standardIframeHeight',
                            allowBlank: false
                        },
                        standardIframeWidth: {
                            fieldLabel: '{s name=iframeWidth}iFrame Breite{/s}',
                            name: 'standardIframeWidth',
                            allowBlank: false
                        }
                    }
                },
                {
                    title: '{s name=errorConfig}Fehlerausgabe{/s}',
                    layout: 'column',
                    fields: {
                        showErrors: '{s name=active}Aktiv{/s}',
                        errorLocaleId: {
                            fieldLabel: '{s name=language}Sprache{/s}',
                            name: 'errorLocaleId',
                            allowBlank: false
                        }
                    }
                }
            ]
        };
    }
});
             