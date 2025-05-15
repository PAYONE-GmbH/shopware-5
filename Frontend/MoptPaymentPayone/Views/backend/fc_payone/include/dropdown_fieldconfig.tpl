{namespace name=backend/mopt_config_payone/main}
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status"
                {if !empty($style)} style="{$style}"{/if}
        >
            <option value="tel">{s name="fieldvalue/numeric"}Numerisch{/s}</option>
            <option value="password">{s name="fieldvalue/password"}Passwort{/s}</option>
            <option value="text">{s name="fieldvalue/text"}Text{/s}</option>
            <option value="select">{s name="fieldvalue/choice"}Auswahl{/s}</option>
        </select>