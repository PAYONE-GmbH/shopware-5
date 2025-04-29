{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_apilog_payone/main}
{block name="content/main"}
    <div class="col-md-9">

        <h3>{s namespace="backend/mopt_config_payone/main" name="apiprotocols"}API-Protokolle{/s}</h3>
        <table data-toggle="table" 
               data-url="ajaxapilog"
               data-search="true"
               data-show-refresh="true"
               data-show-toggle="true"
               data-show-columns="true"
               data-pagination="true"
               data-side-pagination="server"
               data-page-list="[5, 10, 20, 50, 100, 200]"
               data-height="460"
               data-locale="{$locale}"
               >
            
            <thead>
                <tr>
                    <th id="col_id" data-field="id">{s name="mopt_apilog_payone/grid/column_id"}ID{/s}</th>
                    <th id="col_request" data-field="request">{s name="mopt_apilog_payone/grid/column_request"}Typ{/s}</th>
                    <th id="col_response" data-field="response">{s name="mopt_apilog_payone/grid/column_response"}Status{/s}</th>
                    <th id="col_details" data-formatter="modeDetailsFormatter" data-field="liveMode">{s name="mopt_apilog_payone/grid/column_mode"}Betriebsmodus{/s}</th>
                    <th id="col_merchantId" data-field="merchantId">{s name="mopt_apilog_payone/grid/column_merchant"}Merchant ID{/s}</th>
                    <th id="col_portalId" data-field="portalId">{s name="mopt_apilog_payone/grid/column_portal_id"}Portal ID{/s}</th>
                    <th id="col_date" data-formatter="DateFormatter" data-field="creationDate.date">{s name="mopt_apilog_payone/grid/column_date"}Datum{/s}</th>
                    <th id="DateFormatter" data-toggle="tooltip" data-formatter="idDetailsFormatter" data-field="requestArray">{s name="mopt_apilog_payone/grid/column_request_details"}Request{/s}</th>
                    <th id="idDetailsFormatter" data-toggle="tooltip" data-formatter="idDetailsFormatter" data-field="responseArray">{s name="mopt_apilog_payone/grid/column_response_details"}Response{/s}</th>
                </tr>
            </thead>
        </table>
    </div>
{/block}
{block name="resources/javascript" append}
    {include file='backend/fc_payone/include/logs.js'}
{/block}
