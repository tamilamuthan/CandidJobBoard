{capture assign='listingId'}{if $listing_id}{$listing_id}{else}{$listing.id}{/if}{/capture}
{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		<p class="error">
			{if $key == 'NOT_SUPPORTED_VIDEO_FORMAT' || $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
				[[Not supported video format]]
			{elseif $key == 'NOT_CONVERT_VIDEO'}
				[[Could not convert video file]]
			{elseif $key == 'UPLOAD_ERR_INI_SIZE' || $key == 'MAX_FILE_SIZE_EXCEEDED'}
				[[File size exceeds system limit]]
			{else}
				[[{$key}]]
			{/if}
		</p>
	{/foreach}

	<div id="classifieds_video_{$id}">
	{if $value.file_name ne null && $url != '/add-listing/'}
		{$value.file_name|escape:'html'} ({$filesize|string_format:"%.2f"} {$size_token})
		|
		<a class="delete_classifieds_video"
			form_token="{$form_token}"
			listing_id="{if $copy_listing_id}{$copy_listing_id}{else}{$listingId}{/if}"
			field_id="{$id}" file_id="{$value.file_id}"
			href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={if $copy_listing_id}{$copy_listing_id}{else}{$listingId}{/if}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
		{if $copy_listing_id}<input type="hidden" name="{$id}" value="{$value.file_id}" />{/if}
		<br/><br/>
	{/if}
	</div>

	<input type="file"
			field_id="{$id}"
			field_action="upload_classifieds_video"
			field_target="video_field_content_{$id}"
			listing_id="{if $copy_listing_id}{$copy_listing_id}{else}{$listingId}{/if}"
			name="{$id}"
			class="autouploadField"
			id="input_video_{$id}"
			{if $value.file_name ne null}style="display:none;"{/if} />
{else}
	<div id="video_field_content_{$id}">
		
		<div class="errors"></div>

		<div id="classifieds_video_{$id}">
		{if $value.file_name ne null && $url != '/add-listing/'}
			<a onclick="popUpWindow('{$GLOBALS.site_url}/video-player/?listing_id={$listingId}&amp;field_id={$id}', 320, ''); return false;" href="{$GLOBALS.site_url}/video-player/?listing_id={$listingId}&amp;field_id={$id}"> [[Watch a video]]</a>
			|
				<a class="delete_classifieds_video"
					form_token="{$form_token}"
					listing_id="{if $copy_listing_id}{$copy_listing_id}{else}{$listingId}{/if}"
					field_id="{$id}"
					file_id="{$value.file_id}"
					href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={if $copy_listing_id}{$copy_listing_id}{else}{$listingId}{/if}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
				{if $copy_listing_id}<input type="hidden" name="{$id}" value="{$value.file_id}" />{/if}
			<br/><br/>
		{/if}
		</div>

		<input type="file"
				field_id="{$id}"
				field_action="upload_classifieds_video"
				field_target="video_field_content_{$id}"
				listing_id="{if $copy_listing_id}{$copy_listing_id}{else}{$listingId}{/if}"
				name="{$id}"
				class="autouploadField"
				id="input_video_{$id}"
				{if $value.file_name ne null}style="display:none;"{/if} />

	</div>

	<script type="text/javascript">
		{literal}
		// check temporary uploaded data of field
		$(function() {
			{/literal}
				getClassifiedsVideoData('{$id}', '{$listingId}', '{$form_token}');
			{literal}
		});

		{/literal}
	</script>
{/if}

