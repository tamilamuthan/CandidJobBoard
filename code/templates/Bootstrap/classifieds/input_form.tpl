{title}[[Post a {$listingTypeID}]]{/title}
{if $nextPage || $prevPage}
	<div class="bread-crumb">
		{foreach from=$pages item=page name=page_block}
			<div class="input-form-bc">{if $page.sid == $pageSID}<b>&gt;&nbsp;[[{$page.page_name}]]</b>{else}{if $page.order <= $currentPage.order}<a href="{$GLOBALS.site_url}/add-listing/{$listingTypeID|htmlentities}/{$page.page_id}/{$listingSID}">&gt;&nbsp;[[{$page.page_name}]]</a>{else}&gt;&nbsp;[[{$page.page_name}]]{/if}{/if}{if !$smarty.foreach.page_block.last}{/if}&nbsp;</div>
		{/foreach}
	</div>
{/if}
{if $listingTypeID == 'Job'}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Post a Job]]</h1>
{elseif $listingTypeID == 'Opportunity'}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Post an Opportunity]]</h1>
{elseif $listingTypeID == 'Idea'}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Create New Idea]]</h1>
{else}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Create New Resume]]</h1>
{/if}

<div>[[{$currentPage.description}]]</div>

{include file='field_errors.tpl'}
<form method="post" action="{$GLOBALS.site_url}/add-listing/{$listingTypeID|htmlentities}/{$currentPage.page_id}/{$listingSID}"
	  enctype="multipart/form-data" {if $form_fields.ApplicationSettings}onsubmit="return validateForm('add-listing-form');"{/if}
	  id="add-listing-form" class="form">
	<input type="hidden" name="productSID" value="{$productSID}">
	<input type="hidden" name="contract_id" value="{$contract_id}" />
	<input type="hidden" name="listing_type_id" value="{$listingTypeID|htmlentities}" />
	<input type="hidden" id="listing_id" name="listing_id" value="{$listing_id}" />
	{if ($contract_id eq 0)}<input type="hidden" name="proceed_to_posting" value="done" />{/if}
	{set_token_field}

	{include file="input_form_default.tpl"}

	<div class="form-group form-group__btns text-center">
		<input type="hidden" name="action_add" id="hidden_action_add" value=""/>
		<input type="submit" name="preview_listing" value="[[Preview]]" class="btn btn__orange btn__bold" id="listingPreview"/>
	</div>
</form>