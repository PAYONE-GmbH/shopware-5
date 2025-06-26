{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    <div class="col-md-12">    
        <h3>Textbausteine</h3>
        <span>
            Stellen Sie hier die Texte in Bezug auf das PAYONE Payment Plugin ein, die dem Käufer im Zahlungsprozess angezeigt werden<BR>
        </span>
        <hr>
        <div class="row">
            <div class="col-md-12">
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

        <div class="col-md-12">
            <span class="col-md-5">Name</span>
            <span class="col-md-5">Wert</span>
        </div>

        {foreach from=$data item=one name=oneitem}
            {include file="parent:backend/fc_payone/smarty_include/bootstrap_input.tpl" item=$one}
        {/foreach}
    </div>
{/block}

{block name="resources/javascript" append}  

    <script type="text/javascript">

        $(document).ready(function () {
            //toggle `popup` / `inline` mode
            $.fn.editable.defaults.mode = 'inline';
            //make username editable
            $('.inputelement').editable();
            $('#edit-img').click(function (e) {
                e.stopPropagation();
                $('.inputelement').editable('toggle');
            });

        });
    {/block}   