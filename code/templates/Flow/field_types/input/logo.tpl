{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}

	{foreach from=$errors key=key item=error}
		{if $key === 'FILE_SIZE_EXCEEDS_SYSTEM_LIMIT'}
			<p class="error">[[File size shouldn't be larger than 5 MB.]]</p>
		{else}
			<p class="error">[[{$key}]]</p>
		{/if}
	{/foreach}

	{if $value.file_name ne null}
		<div id="profile_logo_{$id}">
			{$value.file_name} ({$filesize|string_format:"%.2f"} {$size_token}) |
			<a class="delete_profile_logo"
			   form_token="{$form_token}"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>
			<br /><br />
		</div>
	{/if}
	<input type="file"
		   id="autoloadFileSelect_{$id}"
		   field_id="{$id}"
		   field_action="upload_profile_logo"
		   field_target="logo_field_content_{$id}"
		   name="{$id}"
		   class="autouploadField"
			{if $value.file_name ne null}style="display:none;"{/if} />

{else}
	<div id="logo_field_content_{$id}" class="form--move-left profile-logo">

		{if $value.file_name ne null}
			<div id="profile_logo_{$id}">
				<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
				<br/>
				<a class="delete_profile_logo"
				   form_token="{$form_token}"
				   field_id="{$id}"
				   file_id="{$value.file_id}"
				   href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>
			</div>
		{/if}
		<input type="file"
			   id="autoloadFileSelect_{$id}"
			   field_id="{$id}"
			   field_action="upload_profile_logo"
			   field_target="logo_field_content_{$id}"
			   name="{$id}"
			   class="form-control"
			   {if $value.file_name ne null}style="display:none;"{/if} />
	</div>
{/if}