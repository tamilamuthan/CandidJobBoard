{title}[[Post a {$listingTypeID}]]{/title}
<h1 class="title__primary title__primary-small title__centered title__bordered">[[Select a Product]]</h1>
<form id="listing-product-choice-form" method="post" action="">
	{foreach from=$products_info item="product" name="products" key="contract_id" }
		<p>
			<input type="radio" value="{$contract_id}" name="contract_id" id="product-{$contract_id}" />
			<label for="product-{$contract_id}"><span class="strong">[[{$product.product_name|escape}]]</span></label>
		</p>
	{/foreach}
	<input type="hidden" name="listing_id" value="{$listing_id}" />
	<input type="hidden" name="listing_type_id" value="{$listingTypeID|escape}" />
	{if $cloneJob}<input type="hidden" name="tmp_listing_id" value="{$tmp_listing_id}" />{/if}
	<div id="listing-product-choice-message"></div>
	<div class="form-group form-group__btns">
		<input type="submit" value="[[Next]]" class="btn btn__orange btn__bold" />
	</div>
</form>
