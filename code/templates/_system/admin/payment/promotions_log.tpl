{breadcrumbs}<a href="{$GLOBALS.site_url}/promotions/" title="">[[Discounts]]</a> &#187; [[Discounts Log]]{/breadcrumbs}
<h1><img src="{image}/icons/paperstar32.png" border="0" alt="" class="titleicon"/>[[Discounts Log]]</h1>
<div class="clr"></div>
{if !empty($errors)}
	{include file="errors.tpl"}
{/if}
<h3>[[Discount Code Info]]:</h3>
<span>[[Discount Code]]: <strong>{$promotionInfo.code|escape}</strong></span><br/>
<span>[[Discount]]:
	<strong>
		{if $promotionInfo.type == 'percentage'}
			{$promotionInfo.discount}%
		{else}
			{capture assign="discount"}{tr type="float"}{$promotionInfo.discount}{/tr}{/capture}
			{currencyFormat amount=$promotionInfo.discount}
		{/if}
	</strong>
</span>
<br/>
<span>[[Start Date]]: <strong>{$promotionInfo.start_date|date}</strong></span><br/>
<span>[[Expiry Date]]: <strong>{$promotionInfo.end_date|date}</strong></span><br/>
<span>[[Status]]: <strong>{if $promotionInfo.active == 1}[[Active]]{elseif $promotionInfo.active == 2}[[Expired]]{else}[[Not Active]]{/if}</strong></span><br/>
<div class="clr"></div>
<h2>[[Log of users used the code]]:</h2>
<div class="clr"></div>

<form method="post" name="promotionsLogForm">
    <div class="box" id="displayResults">
        <div class="box-header">
			{capture name="tableHeaderContent"}
				<div class="resultsnumber">[[Usage number]]: {$resultsNumber}</div>
				<div class="pagination">
					{foreach from=$pages item=page}
						{if $page == $currentPage}
							<strong>{$page}</strong>
						{else}
							{if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
							<a href="?page={$page}{if $sorting_field ne null}&amp;sorting_field={$sorting_field}{/if}{if $sorting_order ne null}&amp;sorting_order={$sorting_order}{/if}&amp;items_per_page={$items_per_page}{$searchFields}">{$page}</a>
							{if $page == 1 && $currentPage > 4} ... {/if}
						{/if}
					{/foreach}
				</div>
				<div class="numberPerPage">
					<label for="items_per_page">[[per page]]:</label>
					<select id="items_per_page" name="items_per_page" onchange="window.location = '?&items_per_page='+this.value;" class="perPage">
						<option value="10" {if $items_per_page == 10}selected="selected"{/if}>10</option>
						<option value="20" {if $items_per_page == 20}selected="selected"{/if}>20</option>
						<option value="50" {if $items_per_page == 50}selected="selected"{/if}>50</option>
						<option value="100" {if $items_per_page == 100}selected="selected"{/if}>100</option>
					</select>
				</div>
			{/capture}
            {$smarty.capture.tableHeaderContent}
        </div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						<tr>
							<th>[[Username]]</th>
							<th>[[User Group]]</th>
							<th>[[Applied to]]</th>
							<th>[[Date]]</th>
							<th>[[Discount Amount]]</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$promotions item='promotion' name='promotion_block'}
							<tr class="{cycle values='evenrow,oddrow'}">
								<td>{$promotion.user.username}</td>
								<td>[[{$promotion.user.userGroupID}]]</td>
								<td>{implode(', ', $promotion.products)}</td>
								<td>{$promotion.date|date}</td>
								<td>
									{capture assign="promotionAmount"}{tr type="float"}{$promotion.amount}{/tr}{/capture}
									{currencyFormat amount=$promotionAmount sign=$currency.currency_sign}
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{$smarty.capture.tableHeaderContent}
		</div>
	</div>
</form>