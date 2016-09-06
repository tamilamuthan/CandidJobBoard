<div class="form-group form-group__half margin">
    <label class="form-label">[[{$form_fields['username'].caption}]] {if $form_fields['username'].is_required}*{/if}</label>
    {input property='username'}
</div>
<div class="form-group form-group__half margin pull-right">
    <label class="form-label">[[{$form_fields['FullName'].caption}]] {if $form_fields['FullName'].is_required}*{/if}</label>
    {input property='FullName'}
</div>
{input property='password' template = "password_in_row.tpl"}
<div class="form-group form-group__half margin">
    <label class="form-label">[[{$form_fields['CompanyName'].caption}]] {if $form_fields['CompanyName'].is_required}*{/if}</label>
    {input property='CompanyName'}
</div>
<div class="form-group form-group__half pull-right margin">
    <label class="form-label">[[{$form_fields['WebSite'].caption}]] {if $form_fields['WebSite'].is_required}*{/if}</label>
    {input property='WebSite'}
</div>
<div class="form-group">
    <label class="form-label">[[{$form_fields['GooglePlace'].caption}]] {if $form_fields['GooglePlace'].is_required}*{/if}</label>
    {input property='Location'}
    {input property='GooglePlace'}
</div>
<div class="form-group">
    <label class="form-label">[[{$form_fields['Logo'].caption}]] {if $form_fields['Logo'].is_required}*{/if}</label>
    {input property='Logo'}
</div>
<div class="form-group">
    <label class="form-label">[[{$form_fields['CompanyDescription'].caption}]] {if $form_fields['CompanyDescription'].is_required}*{/if}</label>
    {input property='CompanyDescription'}
</div>