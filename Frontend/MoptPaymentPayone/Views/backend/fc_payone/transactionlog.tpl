{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_payone_transaction_log/main}
{block name="content/main"}
    <div class="col-md-9">

        <h3>{s name="window_title"}Transaktionsstatus Protokolle{/s}</h3>
        <table data-toggle="table" 
               data-url="ajaxtransactionstatus"
               data-search="true"
               data-show-refresh="true"
               data-show-toggle="true"
               data-show-columns="true"
               data-pagination="true"
               data-side-pagination="server"
               data-page-list="[5, 10, 20, 50, 100, 200]"
               data-locale="{$locale}"
               >
            <thead>
                <tr>
                    <th data-field="id">{s name="mopt_payone_transaction_log/grid/column_id"}ID{/s}</th>
                    <th data-field="transactionId">{s name="mopt_payone_transaction_log/grid/column_transactionId"}Transaktionsid{/s}</th>
                    <th data-field="orderNr">{s name="mopt_payone_transaction_log/grid/column_orderNr"}Bestellnummer{/s}</th>
                    <th data-formatter="modeDetailsFormatter" data-field="liveMode">{s name="mopt_payone_transaction_log/grid/column_mode"}Betriebsmodus{/s}</th>
                    <th data-field="portalId">{s name="mopt_payone_transaction_log/grid/column_portal_id"}Portal ID{/s}</th>
                    <th data-field="status">{s name="mopt_payone_transaction_log/grid/column_status"}Status{/s}</th>
                    <th data-formatter="DateFormatter" data-field="transactionDate.date">{s name="mopt_payone_transaction_log/grid/column_transaction_date"}Transaktionsdatum{/s}</th>
                    <th data-formatter="idDetailsFormatter" data-field="details">{s name="details/title"}Details{/s}</th>
                </tr>
            </thead>
        </table>
    </div>
{/block}
{block name="resources/javascript" append}
        {include file='backend/fc_payone/include/logs.js'}
{/block}
