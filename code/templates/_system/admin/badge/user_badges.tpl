{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">[[{$user_group_info.name} Profiles]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$user_group_info.name}</a> &#187; Badges]]{/breadcrumbs}


<script type="text/javascript">
var progbar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif'>";
$(function() {
	$(".addBadge").click(function(){
		$("#dialog").dialog('destroy');
		$("#dialog").attr({ title: "[[Loading]]"});
		$("#dialog").html(progbar).dialog({ width: 180});
		var link = $(this).attr("href");
		$.get(link, function(data){
			$("#dialog").dialog('destroy');
			$("#dialog").attr({ title: "[[Add New Badge]]"});
			$("#dialog").dialog({
				width: 560,
				close: function(event, ui) {
                    location.href = '{$GLOBALS.site_url}/user-badges/?user_sid={$user_sid}';
                }
			}).html(data);
		});
		return false;
	});
});
function deleteBadge(link) 
{
	if (confirm('[[Are you sure you want to delete this user badge?]]'))
		location.href=link;
}
</script>

<h1>[[Manage User Badges]]</h1>
<p><a href="{$GLOBALS.site_url}/add-user-badge/?user_sid={$user_sid}" target="_blank" class="addBadge grayButton">[[Add New Badge]]</a></p>

<div id="dialog" style="display: none"></div>
<table>
	<thead>
		<tr>
            <th></th>
			<th>[[Badge Name]]</th>
			<th>[[Description]]</th>
			<th>[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$achievements item=ach}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td style="background-color:white">
                    {if $ach.badge.file}
				       <img src="http://localhost/gradlead/code/files/files/{$ach.badge.file}" alt="" border="0" />
				    {else}
				       No Image
				    {/if}
				</td>
				<td>[[{$ach.badge.name}]]</td>
				<td>[[{$ach.badge.detailed_description}]]</td>
				<td>
					<input type="button" name="button" value="[[Remove]]" class="deletebutton" onclick="deleteBadge('{$GLOBALS.site_url}/user-badges/?action=remove&user_sid={$user_sid}&achievement_id={$ach.id}');">
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
