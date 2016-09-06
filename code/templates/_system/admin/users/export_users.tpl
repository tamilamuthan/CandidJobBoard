{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-users/{$userGroup.id|lower}/">
		[[{$userGroup.name} Profiles]]
	</a>
	&#187;
	[[Export {$userGroup.name}s]]
{/breadcrumbs}
<h1><img src="{image}/icons/boxupload32.png" border="0" alt="" class="titleicon" />[[Export {$userGroup.name}s]]</h1>

<form method="post">
	<table class="basetable">
		<input type="hidden" name="action" value="export">
		<input type="hidden" name="user_group_id" value="{$userGroup.id}">
		<thead>
			<tr>
				<th colspan="6">[[Export Filter]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="evenrow"><td>[[Username]]: </td><td colspan="5">{search property="username"}</td></tr>
			<tr class="oddrow"><td>[[Product]]:</td><td class="product-multilist" colspan="5">{search property="product" template="multilist.tpl"}</td></tr>
			<tr class="evenrow"><td>[[Registration Date]]: </td><td colspan="5">{search property="registration_date"}</td></tr>
		</tbody>
		<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
		<thead>
			<tr>
				<th colspan="6">[[User Properties to Export]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow">
				{assign var="i" value=0}
				{foreach from=$userSystemProperties.system item=property_id name=system_properties}
					{assign var="i" value=$i+1}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property_id}]" value="1" /> {$property_id}</td>
					{if $i % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
				{/foreach}
				{foreach from=$userCommonProperties item=properties key=groupName}
					{if $userGroup.id == $groupName}
						{foreach from=$properties item=property name="properties"}
							{assign var="i" value=$i+1}
							<td colspan="2"><input type="checkbox" name="export_properties[{$property.id}]" value="1" /> [[{$property.caption}]]</td>
							{if $i % 3 == 0}
								</tr><tr class="{cycle values="evenrow,oddrow"}">
							{/if}
						{/foreach}
					{/if}
				{/foreach}
			</tr>
				<tr class="{cycle values="evenrow,oddrow"}"><td colspan="6"><a href="#" onClick="check_all();return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all();return false;">[[Deselect]]</a> [[All]]</td>
			</tr>
		</tbody>
		<tr id="clearTable">
			<td colspan="6" align="right">
                <div class="clr"><br/></div>
                <div class="floatRight">
                    <input type="submit" value="[[Export]]" class="greenButton" />
                </div>
            </td>
		</tr>
	</table>
</form>

<script language="Javascript">
$(function(){ldelim}
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	
	$("#registration_date_notless, #registration_date_notmore").datepicker({
		dateFormat: dFormat,
		showOn: 'both',
		yearRange: '-99:+99',
		buttonImage: '{image}icons/icon-calendar.png'
	});
	
});

function check_all()
{
	$('.basetable :checkbox').each(function() {
		this.checked = true;
	});
}

function uncheck_all()
{
	$('.basetable :checkbox').each(function() {
		this.checked = false;
	});
}
</script>
