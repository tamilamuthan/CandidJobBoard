<script type="text/javascript">

	function windowMessage(message) {
		$("#messageBox").dialog( 'destroy' ).html(message);
		$("#messageBox").dialog({
			width: 370,
			height: 170,
			title: '[[Error]]',
			buttons: {
				OK: function(){
					$(this).dialog('close');
				}
			}
			
		}).dialog('open');
		return false;
	}
</script>

{capture assign="trToDelete"}[[Are you sure you want to delete this product?]]{/capture}
{capture assign="trToCannotActivateProduct"}[[The product cannot be activated. Please change the availability date.]]{/capture}
{capture assign="trToProductForEmployers"}[[The product cannot be activated. This product is only for Employers. Please change the User Group.]]{/capture}
<div id="messageBox"></div>

{breadcrumbs}[[{$userGroup.name} Products]]{/breadcrumbs}
<div class="right">
	<a href="{$GLOBALS.site_url}/add-product/?user_group_sid={$userGroup.sid}" class="grayButton">[[Add New Product]]</a>
</div>
<h1><img src="{image}/icons/shoppingcart32.png" border="0" alt="" class="titleicon"/>[[{$userGroup.name} Products]]</h1>
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'PRODUCT_IS_IN_USE'} [[This product is in use. To delete the product, you need to first remove it from invoices and user subscriptions using it.]]{/if}
		</p>
	{/foreach}
{/if}
<div class="box" id="displayResults">
	<div class="box-header"><br/></div>
	<div class="innerpadding">
		<div id="displayResultsTable">
			<table width="100%">
				<thead>
					<tr>
						<th>[[Name]]</th>
						<th>[[Price]]</th>
						<th>[[Status]]</th>
						<th colspan="2" class="actions" width="1%">[[Actions]]</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$products item=product}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>
							<a href="{$GLOBALS.site_url}/edit-product/?sid={$product.sid}" title="[[Edit]]">
								<strong>[[{$product.name|escape}]]</strong>
							</a>
							{if $userGroup.default_product == $product.sid}
								<div style="margin: 5px 0;">
									<small>
										[[Assigned to {$userGroup.name|lower} upon registration]]
									</small>
								</div>
							{/if}
						</td>
						<td>
							{capture assign="productPrice"}{tr type="float"}{$product.price}{/tr}{/capture}
							{if $product.period}
								{if $product.period_name == 'unlimited'}
									{currencyFormat amount=$productPrice}
								{else}
									{currencyFormat amount=$productPrice} [[per]] {$product.period} {if $product.period > 1 }[[{$product.period_name|capitalize}s]]{else}[[{$product.period_name|capitalize}]]{/if}
								{/if}
							{else}
								{currencyFormat amount=$productPrice}
							{/if}
						</td>
						<td>{if $product.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>

						{if $product.active == 1}
							<td nowrap="nowrap"><input type="button" value="[[Deactivate]]" class="deletebutton" onclick="location.href='{$GLOBALS.site_url}/products/{$userGroup.id|lower}/?action=deactivate&sid={$product.sid}'"/></td>
						{else}
							<td nowrap="nowrap"><input type="button" value="[[Activate]]" class="editbutton greenbtn" {if $product.expired}onclick="windowMessage('{$trToCannotActivateProduct|escape}');"{elseif $product.invalid_user_group}onclick="windowMessage('{$trToProductForEmployers|escape}');"{else}onclick="location.href='{$GLOBALS.site_url}/products/{$userGroup.id|lower}/?action=activate&sid={$product.sid}'"/>{/if}</td>
						{/if}
						<td nowrap="nowrap">
							{if $product.subscribed_users || $product.invoices}
							{else}
								<a href="{$GLOBALS.site_url}/products/{$userGroup.id|lower}/?action=delete&sid={$product.sid}" onClick="return confirm('{$trToDelete|escape}');" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
							{/if}
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
