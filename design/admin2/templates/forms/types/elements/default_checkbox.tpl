{* Template for rendering default element (as a checkbox) . Params:
- $input_id
- $default_value - variable contains "0" or "on" *}

<div class="form-field-attribute attribute-mandatory-holder">
    {'Default value: '|i18n( 'extension/formmaker/admin' )} <input type="checkbox" {if $default_value}checked="checked"{/if} />
    <input type="hidden" name="formelement_{$input_id}[default]" value="{$default_value}" />
</div>