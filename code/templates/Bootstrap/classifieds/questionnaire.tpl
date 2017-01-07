{foreach from=$form_fields item=form_field}
<div class="form-group">
    <label class="form-label">[[$form_field.caption]] <span class="inputReq">{if $form_field.is_required}*{/if}</span></label>
    {if $form_field.type == 'list'}
        {input property=$form_field.id template='radiobuttons.tpl' object=$questionsObject}
    {elseif $form_field.type == 'multilist'}
        {input property=$form_field.id template='checkboxes.tpl' object=$questionsObject}
    {else}
        {input property=$form_field.id}
   {/if}
</div>
{/foreach}
