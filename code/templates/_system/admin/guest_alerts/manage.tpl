{capture assign="trAskToDelete"}[[Are you sure you want to delete selected alert?]]{/capture}
<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
<div class="clr"><br /></div>
{if !empty($errors)}
	{foreach from=$errors item="error"}
	<p class="error">[[{$error}]]</p>
	{/foreach}
{/if}
<div class="clr"><br /></div>
<form method="post" name="guestAlerts_form">
	<input type="hidden" name="action_name" id="action_name" value="" />
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
					{foreach from=$guestAlerts item=guestAlert name=alerts_block}
						<tr class="{cycle values = 'evenrow,oddrow'}" id="guestAlerts[{$guestAlert.sid}]">
							<td><input type="checkbox" name="guestAlerts[]" value="{$guestAlert.sid}" id="checkbox_{$smarty.foreach.alerts_block.iteration}" /></td>
							<td><strong><a href="mailto:{$guestAlert.email}">{$guestAlert.email}</a></strong></td>
							<td>{$guestAlert.subscription_date|date}</td>
							<td>[[{$guestAlert.email_frequency}]]</b></td>
							<td>{$guestAlert.last_send|date}</td>
							<td>
								{if $guestAlert.status}
									[[Active]]
								{else}
									[[Not Active]]
								{/if}
							</td>
							<td nowrap="nowrap">
								{if $guestAlert.status}
									<a href="{$GLOBALS.site_url}/guest-alerts/?action_name=deactivate&amp;guestAlerts[]={$guestAlert.sid}" title="[[Deactivate]]" class="deletebutton">[[Deactivate]]</a>
								{else}
									<a href="{$GLOBALS.site_url}/guest-alerts/?action_name=activate&amp;guestAlerts[]={$guestAlert.sid}" title="[[Activate]]" class="editbutton">[[Activate]]</a>
								{/if}
							</td>
							<td nowrap="nowrap">
								<a href="{$GLOBALS.site_url}/guest-alerts/?action_name=delete&amp;guestAlerts[]={$guestAlert.sid}" title="[[Delete]]" class="deletebutton"
									onclick="return confirm('{$trAskToDelete|escape:"javascript"|escape:"html"}');">[[Delete]]
								</a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>
