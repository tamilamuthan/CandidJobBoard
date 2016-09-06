<div class="form-group form-group__half margin">
    <label class="form-label">[[Password]] {if $form_fields.password.is_required}*{/if}</label>
    <input type="password" name="{$id}[original]" class="form-control" />
</div>
<div class="form-group form-group__half margin pull-right">
    <label class="form-label">[[Confirm Password]] {if $form_fields.password.is_required}*{/if}</label>
    <input type="password" name="{$id}[confirmed]" class="form-control" />
</div>