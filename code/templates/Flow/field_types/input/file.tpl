{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{* todo: is deprecated? *}
	{foreach from=$errors key=key item=error}
		<div class="alert alert-danger">
			{if $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
				{$fomr_field.caption}: [[File format is not supported]]
			{elseif $key == 'UPLOAD_ERR_INI_SIZE' || $key == 'MAX_FILE_SIZE_EXCEEDED'}
				{$fomr_field.caption}: [[File size shouldn't be larger than 5 MB.]]
			{else}
				{$key}
			{/if}
		</div>
	{/foreach}

	<div id="file_{$id}">
		{if $value.file_name ne null}
			{$value.file_name|escape:'html'} ({$filesize|string_format:"%.2f"} {$size_token})
			| <a class="delete-file"
				 form_token="{$form_token}"
				 listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
				 field_id="{$id}"
				 file_id="{$value.file_id}"
				 href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listing.id}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
			<br/><br/>
		{/if}
	</div>

	<input type="file"
			field_id="{$id}"
			field_action="upload_file{$id}"
			field_target="file_field_content_{$id}"
			listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
			name="{$id}"
			class="autouploadField"
			id="input_file_{$id}"
			{if $value.file_name ne null}style="display:none;"{/if} />

{else}
	<div id="file_field_content_{$id}" class="form--move-left">
		<div class="errors"></div>
		<div id="file_{$id}">
			{if $value.file_name ne null}
				{if $value.saved_file_name}
					<a class="link" href="?listing_id={$listing.id}&amp;filename={$value.saved_file_name|escape:'url'}&amp;field_id={$id}">{$value.file_name|escape:'html'}</a>
				{else}
					<a class="link" href="{$value.file_url}">{$value.file_name|escape:'html'}</a>
				{/if}
				| <a class="delete-file link"
					 form_token="{$form_token}"
					 listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
					 field_id="{$id}"
					 file_id="{$value.file_id}"
					 href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listing.id}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
			{/if}
		</div>

		<input type="file"
				field_id="{$id}"
				field_action="upload_file{$id}"
				field_target="file_field_content_{$id}"
				listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
				name="{$id}"
				class="form-control"
				id="input_file_{$id}"
				{if $value.file_name ne null}style="display:none;"{/if} />
	</div>

	{javascript}
		<script type="text/javascript">
			// check temporary uploaded data of field
			$(function() {
				getFileFieldData('{$id}', '{if $listing_id}{$listing_id}{else}{$listing.id}{/if}', '{if $listing.type.id}{$listing.type.id}{/if}', '{$form_token}');
			});
		</script>
	{/javascript}
{/if}

