<select class="items_products {if $complexField}complexField{/if}" id="[{$complexStep}]" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}">
	<option value="">[[Please select product]]</option>
	{foreach from=$products item=list_value}
			<option value='{$list_value.sid}' {if $list_value.sid == $value}selected="selected"{/if} >[[{$list_value.name}]]</option>
	{/foreach}
	{if $id == 'products'}<option value="-1" {if $value == -1}selected="selected"{/if}>[[Custom Item]]</option>{/if}
</select>
{foreach from=$products item=product}
	<input type="hidden" name="price_per_unit_{$product.sid}_{$product.number_of_listings}" id="price_per_unit_{$product.sid}_{$product.number_of_listings}" value="{$product.price}" />
	<input type="hidden" name="number_{$product.sid}" id="number_{$product.sid}" value="{$product.number_of_listings}" />
	<input type="hidden" name="price_type_{$product.sid}" id="price_type_{$product.sid}" value="0" />
{/foreach}

