{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		{if $key === 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<p class="error">[[Not supported file format]]</p>
		{elseif $key === 'UPLOAD_ERR_INI_SIZE'}
			<p class="error">[[File size shouldn't be larger than 5 MB.]]</p>
		{else}
			<p class="error">[[{$key}]]</p>
		{/if}
	{/foreach}

	<input type="file"
		id="autoloadFileSelect_{$id}"
		field_id="{$id}"
		field_action="upload_profile_logo"
		field_target="logo_field_content_{$id}"
		name="{$id}"
		class="autouploadField"
		{if $value.file_name ne null}style="display:none;"{/if} />

	{if $value.file_name ne null}
		<div id="profile_logo_{$id}" style="float:left;">
			<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
			<br/><br/>
			<a class="delete_profile_logo"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   user_sid="{$user_info.sid}"
			   href="{$GLOBALS.user_site_url}/users/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
			<br/><br/>
		</div>
		<span id="extra_field_info_{$id}" style="display:none;">
	{/if}
{else}
	<div id="logo_field_content_{$id}">
		<input type="file"
			   id="autoloadFileSelect_{$id}"
			   field_id="{$id}"
			   field_action="upload_profile_logo"
			   field_target="logo_field_content_{$id}"
			   name="{$id}"
			   class="autouploadField"
			   {if $value.file_name ne null}style="display:none;"{/if} />

		{if $value.file_name ne null}
			<div id="profile_logo_{$id}" style="float:left;">
				<img src="{$value.file_url|escape:'html'}" alt="" border="0" />
				<br/><br/>
				<a class="delete_profile_logo"
				   field_id="{$id}"
				   file_id="{$value.file_id}"
				   user_sid="{$user_info.sid}"
				   href="{$GLOBALS.user_site_url}/users/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
				<br/><br/>
			</div>
			<span id="extra_field_info_{$id}" style="display:none;"></span>
		{/if}
	</div>
{/if}