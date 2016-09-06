<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
{capture name="confirmToDelete"}[[Are you sure you want to delete this {$listingsType.name|lower}?]]{/capture}
<div class="clr"><br/></div>
<form method="post" action="{$GLOBALS.site_url}/listing-actions/" name="resultsForm">
	<input type="hidden" name="action_name" id="action_name" value="">
	<input type="hidden" name="listingTypeId" value="{$listingsType.id}">

	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>

		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
						{foreach from=$listings item=listing name=listings_block}
							<tr class="{cycle values = 'evenrow,oddrow'}">
								<td><input type="checkbox" name="listings[{$listing.id}]" value="1" id="checkbox_{$smarty.foreach.listings_block.iteration}" /></td>
								<td><a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$listing.id}">{$listing.Title|escape:'html'}</a></td>
								<td>
									<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$listing.user.sid}">
										{if $listing.type.id == 'Job'}
											{$listing.user.CompanyName|escape}
										{else}
											{$listing.user.FullName|escape}
										{/if}
									</a>
								</td>
								{if $listing.type.id == 'Job'}
									<td>[[{$listing.product.name}]]</td>
								{/if}
								<td>{$listing.activation_date|date:null:true}</td>
								{if $listing.type.id == 'Job'}
									<td><a href="{$GLOBALS.site_url}/system/applications/view/?user_sid={$listing.user.id}&amp;appJobId={$listing.id}">{$listing.applications}</a></td>
								{/if}
								<td>
									{if $listing.active == 1}
										[[Active]]
									{else}
										[[Not Active]]
									{/if}
								</td>
								<td nowrap="nowrap">
									{if $listing.active}
										<a href="{$GLOBALS.site_url}/listing-actions/?action_name=deactivate&amp;listings[{$listing.id}]=1&amp;listingTypeId={$listing.type.id}" class="deletebutton">[[Deactivate]]</a>
									{else}
										<a href="{$GLOBALS.site_url}/listing-actions/?action_name=activate&amp;listings[{$listing.id}]=1&amp;listingTypeId={$listing.type.id}" class="editbutton greenbtn" style="text-align: center;">[[Activate]]</a>
									{/if}
								</td>
								<td nowrap="nowrap" style="border-left: 0px;"><a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$listing.id}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
								<td nowrap="nowrap" style="border-left: 0px;"><a href="{$GLOBALS.site_url}/listing-actions/?action_name=delete&amp;listings[{$listing.id}]=1&amp;listingTypeId={$listing.type.id}" onclick="return confirm('{$smarty.capture.confirmToDelete|escape}')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>
{capture name="trLoading"}[[Please wait ...]]{/capture}
<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	var progBar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";

	function isPopUp(button, textChooseAction, textChooseItem, textToDelete) {
		if (isActionEmpty(button, textChooseAction, textChooseItem)) {
			var action = $("#selectedAction_" + button).val();
			switch (action) {
				case "delete":
					if (confirm(textToDelete)) {
						submitForm(action);
					}
					break;
				default:
					submitForm(action);
					break;
			}
		}
		$("#selectedAction_" + button).val('');
	}
</script>
