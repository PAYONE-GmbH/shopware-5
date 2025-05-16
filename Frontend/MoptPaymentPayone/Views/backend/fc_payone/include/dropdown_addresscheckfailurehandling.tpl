<div class="form-group has-feedback has-error menu-level-experte">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="0">{s name="fieldvalue/cancel"}Vorgang abbrechen{/s}</option>
            <option value="1">{s name="fieldvalue/reenter"}Neueingabe der Adresse die zum Fehler geführt hat{/s}</option>
            <option value="2">{s name="fieldvalue/doconsumerscore"}Anschließende Bonitätsprüfung durchführen{/s}</option>
            <option value="3">{s name="fieldvalue/continue"}fortfahren{/s}</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>