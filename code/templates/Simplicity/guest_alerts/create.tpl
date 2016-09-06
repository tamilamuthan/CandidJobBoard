{if $is_data_submitted && !$errors}
    <div class="alert alert-success text-center">[[You applied successfully]]</div>
    <div class="alert alert-info text-center">[[You can cancel this at any time.]]</div>
{/if}

{if !empty($errors)}
	{include file="../users/field_errors.tpl"}
{/if}

<form action="{$GLOBALS.site_url}/guest-alerts/create/" method="post" id="create-alert" class="form">
    <input type="hidden" name="searchId" value="{$searchId|escape}" />
    <input type="hidden" name="action" value="save" />
    {foreach from=$form_fields item="formField"}
        <div class="form-group">
            {if $formField.id == 'email'}
                {input property=$formField.id template="email_placeholder.tpl"}
            {else}
                {input property=$formField.id}
            {/if}
        </div>
	{/foreach}
    <div class="form-group text-center">
		<input type="submit" name="save" value="[[Create alert]]" class="btn__submit-modal btn btn__orange btn__bold" onclick="return saveAlert();"/>
	</div>
</form>
{literal}
    <script type="text/javascript">
        function saveAlert() {
            var options = {
                target: '.modal-body',
                url:  $('#create-alert').attr('action'),
            };
            $('#create-alert').ajaxSubmit(options);
            return false;
        }
    </script>
{/literal}