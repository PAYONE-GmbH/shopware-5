{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    {if !empty($content)} <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="{s name="fieldlabel/help"}PAYONE Hilfe{/s}" data-content="{$content}">{/if}
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="0">{s name="fieldvalue/donotcheck"}Nicht durchführen{/s}</option>
            <option value="1">{s name="fieldvalue/basic"}Basic{/s}</option>
            <option value="2">{s name="fieldvalue/person"}Person{/s}</option>
            <option value="3">{s name="fieldvalue/boniversum_basic"}Boniversum Basic{/s}</option>
            <option value="4">{s name="fieldvalue/boniversum_person"}Boniversum Person{/s}</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>
