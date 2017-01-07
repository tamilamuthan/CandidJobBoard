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

{capture assign="trToDelete"}[[Are you sure you want to delete this badge?]]{/capture}
{capture assign="trToCannotActivateBadge"}[[The badge cannot be activated. Please change the availability date.]]{/capture}
{capture assign="trToBadgeForEmployers"}[[The badge cannot be activated. This badge is only for Employers. Please change the User Group.]]{/capture}
<div id="messageBox"></div>

{breadcrumbs}[[{$userGroup.name} Badges]]{/breadcrumbs}
<div class="right">
	<a href="{$GLOBALS.site_url}/add-badge/?user_group_sid={$userGroup.sid}" class="grayButton">[[Add New Badge]]</a>
</div>
<h1>[[{$userGroup.name} Badges]]</h1>
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'PRODUCT_IS_IN_USE'} [[This badge is in use..]]{/if}
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
					    <th></th>
						<th>[[Name]]</th>
						<th>[[Description]]</th>
						<th>[[Status]]</th>
						<th colspan="2" class="actions" width="1%">[[Actions]]</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$badges item=badge}
					<tr class="{cycle values = 'evenrow,oddrow'}">
					    <td style="background-color:white">
                           {if $badge.file}
					       <img src="http://localhost/gradlead/code/files/files/{$badge.file}" alt="" border="0" />
					        {else}
					            No Image
					        {/if}
					    </td>
						<td>
							<a href="{$GLOBALS.site_url}/edit-badge/?sid={$badge.sid}" title="[[Edit]]">
								<strong>[[{$badge.name|escape}]]</strong>
							</a>
						</td>
						<td>
                        [[ {$badge.detailed_description}]]
						</td>
						<td>{if $badge.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>

						{if $badge.active == 1}
							<td nowrap="nowrap"><input type="button" value="[[Deactivate]]" class="deletebutton" onclick="location.href='{$GLOBALS.site_url}/badges/{$userGroup.id|lower}/?action=deactivate&sid={$badge.sid}'"/></td>
						{else}
							<td nowrap="nowrap"><input type="button" value="[[Activate]]" class="editbutton greenbtn" {if $badge.expired}onclick="windowMessage('{$trToCannotActivateBadge|escape}');"{elseif $badge.invalid_user_group}onclick="windowMessage('{$trToBadgeForEmployers|escape}');"{else}onclick="location.href='{$GLOBALS.site_url}/badges/{$userGroup.id|lower}/?action=activate&sid={$badge.sid}'"/>{/if}</td>
						{/if}
						<td nowrap="nowrap">
							<a href="{$GLOBALS.site_url}/badges/{$userGroup.id|lower}/?action=delete&sid={$badge.sid}" onClick="return confirm('{$trToDelete|escape}');" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
