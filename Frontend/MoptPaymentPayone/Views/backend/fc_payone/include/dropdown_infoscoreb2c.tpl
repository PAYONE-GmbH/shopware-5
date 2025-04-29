{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error menu-level-experte">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="IH">{s name="fieldvalue/IH"}Infoscore (harte Kriterien){/s}</option>
            <option value="IA">{s name="fieldvalue/IA"}Infoscore (alle Merkmale){/s}</option>
            <option value="IB">{s name="fieldvalue/IB"}Infoscore (alle Merkmale + Boniscore){/s}</option>
            <option value="CE">{s name="fieldvalue/CE"}Boniversum VERITA Score{/s}</option>
            <option value="NO">{s name="fieldvalue/NONE"}Keine Prüfung{/s}</option>
        </select>
        <span class="bi form-control-feedback bi-remove" aria-hidden="true"></span>
        <span id="{$id}-status" class="sr-only">(success)</span>
        <div class="help-block with-errors"></div>
    </div>
</div>
