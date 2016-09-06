{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		{if $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<p class="error">{$fomr_field.caption}: [[File format is not supported]]</p>
		{elseif $key == 'UPLOAD_ERR_INI_SIZE' || $key == 'MAX_FILE_SIZE_EXCEEDED'}
			<p class="error">[[File size shouldn't be larger than 5 MB.]]</p>
		{else}
			<p class="error">{$key}</p>
		{/if}
	{/foreach}
	<div id="file_{$id}">
		{if $value.file_name ne null}
			{$value.file_name|escape:'html'} ({$filesize|string_format:"%.2f"} {$size_token})
			| <a class="delete_file"
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
			field_action="upload_file"
			field_target="file_field_content_{$id}"
			listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
			name="{$id}"
			class="autouploadField"
			id="input_file_{$id}"
			{if $value.file_name ne null}style="display:none;"{/if} />
{else}
	<div id="file_field_content_{$id}">
		<div class="errors"></div>
		<div id="file_{$id}">
			{if $value.file_name ne null}
				{if $value.saved_file_name}
					<a href="?listing_id={$listing.id}&amp;filename={$value.saved_file_name|escape:'url'}&amp;field_id={$id}">{$value.file_name|escape:'html'}</a>
				{else}
					<a href="{$value.file_url}">{$value.file_name|escape:'html'}</a>
				{/if}
				| <a class="delete_file"
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
				field_action="upload_file"
				field_target="file_field_content_{$id}"
				listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
				name="{$id}"
				class="autouploadField"
				id="input_file_{$id}"
				{if $value.file_name ne null}style="display:none;"{/if} />
	</div>

	<script type="text/javascript">
		$(function() {
			getFileFieldData('{$id}', '{if $listing_id}{$listing_id}{else}{$listing.id}{/if}', '{if $listing.type.id}{$listing.type.id}{/if}', '{$form_token}');
		});
	</script>
{/if}

