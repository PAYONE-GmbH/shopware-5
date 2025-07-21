{namespace name=backend/mopt_config_payone/main}
<select name="{$id}" id="{$id}" {if !empty($style)} style="{$style}"{/if} class="form-control">
    <option value="1">{s name ="fieldvalue/standard"}Standard{/s}</option>
    <option value="0">{s name ="fieldvalue/userdefined"}Benutzerdefiniert{/s}</option>
</select>