{extends file="parent:backend/_base/layout.tpl"}

{block name="content/breadcrump"}
                        <li>
                            {$breadcrump.0}
                        </li>
                        <li>
                            {$breadcrump.1}
                        </li>
                        <li class="active">
                            <a href="{url controller="FcPayone" action="{$breadcrump.2}"}">{$breadcrump.3}</a> <span class="divider">/</span>
                        </li> 
{/block}

{block name="content/main"}
    <div class="col-md-12">

        <h3>API Protokolle</h3>
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
               >
            
            <thead>
                <tr>
                    <th data-field="id">{s name=mopt_apilog_payone/grid/column_id}ID{/s}</th>
                    <th data-field="request">{s name=mopt_apilog_payone/grid/column_request}Typ{/s}</th>
                    <th data-field="response">{s name=mopt_apilog_payone/grid/column_response}Status{/s}</th>
                    <th data-formatter="modeDetailsFormatter" data-field="liveMode">{s name=mopt_apilog_payone/grid/column_mode}Betriebsmodus{/s}</th>
                    <th data-field="merchantId">{s name=mopt_apilog_payone/grid/column_merchant}Merchant ID{/s}</th>
                    <th data-field="portalId">{s name=mopt_apilog_payone/grid/column_portal_id}Portal ID{/s}</th>
                    <th data-formatter="DateFormatter" data-field="creationDate.date">{s name=mopt_apilog_payone/grid/column_date}Datum{/s}</th>
                    <th data-toggle="tooltip" data-formatter="requestDetailsFormatter" data-field="requestArray">{s name=mopt_apilog_payone/grid/column_request_details}Request{/s}</th>
                    <th data-toggle="tooltip" data-formatter="responseDetailsFormatter" data-field="responseArray">{s name=mopt_apilog_payone/grid/column_response_details}Response{/s}</th>
                </tr>
            </thead>
        </table>
    </div>
{/block}
{block name="resources/javascript" append}  

    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
            $('.table').on('all.bs.table', function (e, name, args) {
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover();
            });
        });

        function requestDetailsFormatter(value) {
            return '<a data-placement="left" data-toggle="popover" data-trigger="focus" href="#" data-content="' + value + '" title="Details" data-html="true" class="">Details</a>';
        }

        function responseDetailsFormatter(value) {
            return '<a data-placement="left" data-toggle="popover" data-trigger="focus" href="#" data-content="' + value + '" title="Details" data-html="true" class="">Details</a>';
        }

        function DateFormatter(value) {
            return value.substr(0, 16);
        }

        function modeDetailsFormatter(value) {
            if (value === false)
            {
                return 'Test';
            } else {
                return 'Live';
            }
        }
    </script>    

{/block}
