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
                        },
                        enableAutoCardtypeDetection: {
                            fieldLabel: '{s name=enableAutoCardtypeDetection}Automatische Kreditkartenerkennung{/s}',
                            name: 'enableAutoCardtypeDetection',
                            allowBlank: true
                        },
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
                        },
                        defaultTranslationIframeCardpan: {
                            fieldLabel: '{s name=IframeCardpan}Platzhalter{/s}',
                            name: 'defaultTranslationIframeCardpan',
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
                        },
                        defaultTranslationIframeCvc: {
                            fieldLabel: '{s name=IframeCvc}Platzhalter{/s}',
                            name: 'defaultTranslationIframeCvc',
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
                    title: '{s name=defaultTranslation}Standardübersetzung{/s}',
                    layout: 'column',
                    fields: {
                        defaultTranslationIframeMonth1: {
                            fieldLabel: '{s name=iframeMonth1}Januar{/s}',
                            name: 'defaultTranslationIframeMonth1',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth2: {
                            fieldLabel: '{s name=iframeMonth2}Februar{/s}',
                            name: 'defaultTranslationIframeMonth2',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth3: {
                            fieldLabel: '{s name=iframeMonth3}März{/s}',
                            name: 'defaultTranslationIframeMonth3',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth4: {
                            fieldLabel: '{s name=iframeMonth4}April{/s}',
                            name: 'defaultTranslationIframeMonth4',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth5: {
                            fieldLabel: '{s name=iframeMonth5}Mai{/s}',
                            name: 'defaultTranslationIframeMonth5',
                            allowBlank: true
                        },                        
                        defaultTranslationIframeMonth6: {
                            fieldLabel: '{s name=iframeMonth6}Juni{/s}',
                            name: 'defaultTranslationIframeMonth6',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth7: {
                            fieldLabel: '{s name=iframeMonth7}Juli{/s}',
                            name: 'defaultTranslationIframeMonth7',
                            allowBlank: true
                        },                        
                        defaultTranslationIframeMonth8: {
                            fieldLabel: '{s name=iframeMonth8}August{/s}',
                            name: 'defaultTranslationIframeMonth8',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth9: {
                            fieldLabel: '{s name=iframeMonth9}September{/s}',
                            name: 'defaultTranslationIframeMonth9',
                            allowBlank: true
                        },                        
                        defaultTranslationIframeMonth10: {
                            fieldLabel: '{s name=iframeMonth10}Oktober{/s}',
                            name: 'defaultTranslationIframeMonth10',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth11: {
                            fieldLabel: '{s name=iframeMonth11}November{/s}',
                            name: 'defaultTranslationIframeMonth11',
                            allowBlank: true
                        },
                        defaultTranslationIframeMonth12: {
                            fieldLabel: '{s name=iframeMonth12}Dezember{/s}',
                            name: 'defaultTranslationIframeMonth12',
                            allowBlank: true
                        }
                    }
                },                
                {
                    title: '{s name=errorConfig}Fehlerausgabe und eigene Fehlermeldungen{/s}',
                    layout: 'column',
                    fields: {
                        showErrors: '{s name=active}Aktiv{/s}',
                        defaultTranslationIframeinvalidCardpan: {
                            fieldLabel: '{s name=invalidCardpan}Ungültige Kreditkartennummer{/s}',
                            name: 'defaultTranslationIframeinvalidCardpan',
                            allowBlank: true
                        },
                        defaultTranslationIframeinvalidCvc: {
                            fieldLabel: '{s name=invalidCvc}Ungültige Kartenprüfziffer{/s}',
                            name: 'defaultTranslationIframeinvalidCvc',
                            allowBlank: true
                        }, 
                        defaultTranslationIframeinvalidPanForCardtype: {
                            fieldLabel: '{s name=invalidPanForCardtype}Ungültige Kreditkartennummer für den Kartentyp{/s}',
                            name: 'defaultTranslationIframeinvalidPanForCardtype',
                            allowBlank: true
                        },
                        defaultTranslationIframeinvalidCardtype: {
                            fieldLabel: '{s name=invalidCardtype}Ungültiger Kartentyp{/s}',
                            name: 'defaultTranslationIframeinvalidCardtype',
                            allowBlank: true
                        },    
                        defaultTranslationIframeinvalidExpireDate: {
                            fieldLabel: '{s name=invalidExpireDate}Ungültiges Verfallsdatum{/s}',
                            name: 'defaultTranslationIframeinvalidExpireDate',
                            allowBlank: true
                        },
                        defaultTranslationIframeinvalidIssueNumber: {
                            fieldLabel: '{s name=invalidIssueNumber}Ungültige Ausstellungsnummer{/s}',
                            name: 'defaultTranslationIframeinvalidIssueNumber',
                            allowBlank: true
                        }, 
                        defaultTranslationIframetransactionRejected: {
                            fieldLabel: '{s name=transactionRejected}Transaktion abgelehnt{/s}',
                            name: 'defaultTranslationIframetransactionRejected',
                            allowBlank: true
                        }                       
                    }
                }
            ]
        };
    }
});
             
