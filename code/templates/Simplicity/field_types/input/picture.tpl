<div class="profile-logo">
	<div id="file_{$id}">
		{if $value.file_name ne null}
			<img src="{$value.file_url|escape}" alt="" border="0" width="{$width}" height="{$height}" />&nbsp;
			<a class="delete-file" href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}"
			   form_token="{$form_token}"
			   listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   data-type="picture"
				>[[Remove]]</a><br/><br/>
			{if $value.url}
				<input type="hidden" name="{$id}_url" value="{$value.url|escape}" />
			{/if}
		{/if}
	</div>
	<input id="input_file_{$id}" type="file" name="{$id}" class="form-control" />
</div>
