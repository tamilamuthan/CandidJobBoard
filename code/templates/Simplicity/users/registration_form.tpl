<h1 class="title__primary title__primary-small title__centered title__bordered">[[Create {$user_group_info.name} Profile]]</h1>
{module name="social" function="social_plugins"}
<div class="text-center form-group cloud">
	{if $user_group_info.id == 'JobSeeker'}
		[[I already have a Job Seeker account.]]
	{else}
		[[I already have an Employer account.]]
	{/if}
	<a class="link" href="{$GLOBALS.user_site_url}/login/{if $smarty.request.return_url}?return_url={$smarty.request.return_url|escape:'url'}{/if}">[[Sign me in]]</a>
</div>
{foreach from=$errors item=error key=field_caption}
	<div class="alert alert-danger">
		{if $error eq 'EMPTY_VALUE'}
			{assign var="field_caption" value=$field_caption|tr}
			[[Please enter '$field_caption']]
		{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
			[[File size shouldn't be larger than 5 MB.]]
		{elseif $error eq 'NOT_UNIQUE_VALUE'}
			[[This email address is already in use.]]
		{elseif $error eq 'NOT_CONFIRMED'}
			[[Passwords did not match]]
		{elseif $error eq 'NOT_VALID_ID_VALUE'}
			[[You can use only alphanumeric characters for]] '{$field_caption}'
		{elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
			[[Please enter valid email address]]
		{elseif $error eq 'NOT_VALID'}
			[[Please enter valid]] [[{$field_caption}]]
		{elseif $error eq 'NOT_ACCEPTED_TERMS'}
			[[Please accept the Terms of Use to Register]]
		{elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
			[[Image format is not supported]]
		{else}
			[[{$error}]]
    	{/if}
	</div>
{/foreach}
<form class="form" method="post" action="" enctype="multipart/form-data" id="registr-form">
	<input type="hidden" name="action" value="register" />
	<input type="hidden" name="return_url" value="{$smarty.request.return_url|escape}" />
	{set_token_field}
	{if $user_group_info.id == 'JobSeeker'}
		{foreach from=$form_fields item=form_field}
			{if $form_field.type == 'password'}
				{input property=$form_field.id}
			{else}
				<div class="form-group">
					<label class="form-label">[[$form_field.caption]] {if $form_field.is_required}*{/if}</label>
					{input property=$form_field.id}
				</div>
			{/if}
		{/foreach}
	{else}
        {include file="../users/registration_fields.tpl"}
	{/if}
	<div class="form-group">
		<label class="form-label hidden-xs-480"></label>
		<div class="form--move-left">
			<input type="checkbox" name="terms" checked="checked" id="terms" />
			<span>
				<a class="link" target="_blank" href="{$GLOBALS.site_url}/terms-of-use/">[[I agree to the terms of use]] *</a>
			</span>
		</div>
	</div>
	<div class="form-group form-group__btns text-center">
		<input type="hidden" name="user_group_id" value="{$user_group_info.id}" />
		<input type="submit" class="btn btn__orange btn__bold" value="[[Register]]" />
	</div>
</form>
{javascript}
	<script type="text/javascript" language="JavaScript">
		function checkField( obj, name ) {
			if (obj.val() != "") {
				var options = {
					data: { isajaxrequest: 'true', type: name },
					success: showResponse
				};
				$("#registr-form").ajaxSubmit( options );
			}
			function showResponse(responseText, statusText, xhr, $form) {
				var mes = "";
				switch(responseText) {
					case 'NOT_VALID_EMAIL_FORMAT':
						obj.closest('.form-group').find('.form-label').addClass('form-label__error').text('[[Please enter valid email address]]');
						break;
					case 'NOT_UNIQUE_VALUE':
						obj.closest('.form-group').find('.form-label').addClass('form-label__error').text('[[This email address is already in use.]]');
						break;
					case '1':
						mes = "";
						if (name == 'username') {
							obj.closest('.form-group').find('.form-label').removeClass('form-label__error').text('Email {if $form_fields["username"].is_required}*{/if}');
						}
						else {
							obj.closest('.form-group').find('.form-label').removeClass('form-label__error').text(name + ' {if $form_fields[name].is_required}*{/if}');
						}
						break;
				}
				$("#am_" + name).text(mes);
			}
		};
	</script>
{/javascript}
{if $instructionsExist}
	{javascript}
		<script type="text/javascript">
			$("document").ready(function() {
				var elem = $(".instruction").prev();
				elem.children().focus(function() {
					$(this).parent().next(".instruction").children(".instr_block").show();
				});
				elem.children().blur(function() {
					$(this).parent().next(".instruction").children(".instr_block").hide();
				});
			});
			CKEDITOR.on('instanceReady', function(e) {
				e.editor.on('focus', function() {
					$("#instruction_"+ e.editor.name).show();
				});
				e.editor.on('blur', function() {
					$("#instruction_"+e.editor.name).hide();
				});
				return;
			});
		</script>
	{/javascript}
{/if}