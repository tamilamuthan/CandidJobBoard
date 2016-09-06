<a href="javascript:history.go(-1)" class="btn__back edit-listing-back">[[Back]]</a>
<h1 class="title__primary title__primary-small title__centered title__bordered">[[Edit {$listingTypeID}]]</h1>
{if $errors}
	{foreach from=$errors item="error_data" key="error_id"}
		<div class="alert alert-danger">
			{if $error_id == 'MAX_FILE_SIZE_EXCEEDED'}
				[[File size shouldn't be larger than 5 MB.]]
			{elseif $error_id == 'NOT_OWNER_OF_LISTING'}
				[[You're not the owner of this posting]]
			{elseif $error_id == 'NO_SUCH_FILE'}[[No such file found in the system]]
			{else}
				{$error_id} {$error_data}
			{/if}
		</div>
	{/foreach}
{else}
	{include file='field_errors.tpl'}
	<form method="post" action="" enctype="multipart/form-data" {if isset($listing.ApplicationSettings)}onsubmit="return validateForm('editListingForm');"{/if} id="editListingForm" class="form">
		<input type="hidden" name="action" value="save_info" />
		<input type="hidden" name="listing_id" id="listing_id" value="{$listing.id}" />

		{set_token_field}

        <div class="col-xs-12 col-sm-8 edit-listing--form">
            {foreach from=$pages item=form_fields key=page name=editBlock}
                {include file="input_form_default.tpl"}
            {/foreach}
            <div class="form-group form-group__btns text-center clearfix">
                <input type="submit" value="[[Save]]" class="btn btn__orange btn__bold" />
            </div>
        </div>
        <div class="col-sm-3 col-xs-12 well edit-listing--action pull-right">
            <div class="form-group form-group__btns text-center">
                <input type="submit" name="preview_listing" value="[[View {$listingTypeID}]]" class="btn btn__blue btn__bold" id="listingPreview"/>
                {if $original_listing.active}
                    <a class="btn btn__blue btn__bold" href="{$GLOBALS.site_url}/my-listings/{$listingTypeID|lower}/?action_deactivate=1&amp;listings[{$original_listing.sid}]=1">
						[[Make Hidden]]
					</a>
                {else}
					<a class="btn btn__blue btn__bold" href="{$GLOBALS.site_url}/pay-for-listing/?listing_id={$original_listing.sid}">
						[[Make Visible]]
					</a>
                {/if}
				<a class="btn btn__orange btn__bold" href="{$GLOBALS.site_url}/my-listings/{$listingTypeID|lower}/?action_delete=1&amp;listings[{$original_listing.sid}]=1" onclick="return confirm('[[Your {$listingTypeID|lower} will be removed permanently. Are you sure?]]')">[[Delete {$listingTypeID}]]</a>
            </div>
        </div>
	</form>
{/if}
