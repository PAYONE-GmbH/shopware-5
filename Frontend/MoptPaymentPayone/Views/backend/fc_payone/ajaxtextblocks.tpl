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
    <h3>Textbausteine</h3>
    <span>
        Stellen Sie hier die Texte in Bezug auf das PAYONE Payment Plugin ein, die dem Käufer im Zahlungsprozess angezeigt werden<BR>
    </span>
    <hr>
    <div class="row">
        <div class="col-md-9">
            <div class="btn-group" style="padding-left: 15px">
                <button type="button" class="btn-payone btn-language btn dropdown-toggle" data-toggle="dropdown">
                    <span class="selection">Deutsch </span><span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-filter-locale" role="menu">
                    <li><a href="#">Deutsch</a></li>
                    <li><a href="#">Englisch</a></li>
                    <li><a href="#">Niederländisch</a></li>
                </ul>
            </div>
        </div>
    </div>
    <table id ="table"
           data-toggle="table" 
           data-url="ajaxgettextblocks"
           data-query-params ="queryParams"
           data-search="true"
           data-show-refresh="true"
           data-show-toggle="true"
           data-show-columns="true"
           data-pagination="true"
           data-side-pagination="server"
           data-page-list="[5, 10, 20, 50, 100, 200]"
           data-editable-url="ajaxsavetextblocks"
           data-id-field="id"
           >
        <thead>
            <tr>
                <th data-field="id">ID</th>
                <th data-field="namespace">Namespace</th>
                <th data-field="name">Name</th>
                <th data-editable="true" data-field="value">Wert</th>
            </tr>
        </thead>
    </table>
</div>
{/block}
{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file="backend/_resources/js/bootstrap-editable.js"}"></script>
     <script type="text/javascript" src="{link file="backend/_resources/js/bootstrap-table-editable.js"}"></script>

    <script type="text/javascript">

        function queryParams(params) {
            return {
                localeId: localeId,
                offset: params.offset,
                limit: params.limit
            };
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
            $('.table').on('all.bs.table', function (e, name, args) {
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover();
            });
        });
        //requestDetailsFormatter
        function requestDetailsFormatter(value) {
            return '<a data-toggle="popover" data-trigger="focus" href="#" data-content="' + value + '" title="Details" data-html="true" class="">requestDetails</a>';
        }
    </script>    

{/block}
