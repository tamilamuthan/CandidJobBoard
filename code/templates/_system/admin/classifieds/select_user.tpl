{title}[[Post {$listingType.id}]]{/title}
<script type="text/javascript">

$(function() {
	var progBar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";
	$('#choose').click(function(){
		$("#messageBox").dialog('destroy').html("[[Please wait ...]]" + progBar);
		$("#messageBox").dialog({
			width: 300,
			modal: true,
			title: "[[Choose User Group]]"
		}).dialog( 'open' );
		$.get("{$GLOBALS.site_url}/choose-user/?listing_type_id={$listingType.id}", function(data){
			$("#messageBox").html(data);
		});
		return false;
	});
});

</script>
{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/">
		[[{$listingType.name} Postings]]
	</a>
	&#187;
	[[Add New {$listingType.name}]]
{/breadcrumbs}
	<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[Add New {$listingType.name}]] </h1>
{foreach from=$errors key=error item=message}
	{if $error eq "USER_NOT_FOUND"}
		<p class="error">[[User '{$username}' not found]]</p>
	{elseif $error eq  "USER_NOT_SELECTED"}
		<p class="error">[[Please select a user]]</p>
	{/if}
{/foreach}
<fieldset style="width: 600px;">
	<legend>
		{if $listingType.id == 'Job'}
			[[Select Employer]]
        {elseif $listingType.id == 'Opportunity'}
            [[Select Investor]]
        {elseif $listingType.id == 'Idea'}
            [[Select Entrepreneur]]
		{else}
			[[Select Job Seeker]]
		{/if}
	</legend>
	<form method="get" action="{$GLOBALS.site_url}/add-listing/">
		<input type="hidden" name="listing_type_id" value="{$listingType.id|lower}" />
		<input type="hidden" name="action" value="userVerify" />
        <table>
            <tr>
                <td><input type="text" name="username" id="username" value="{$username}" {if $listingType.id == 'Job'}placeholder="[[Enter Employer Email]]"{/if} /></td>
                <td>[[or]]</td>
                <td><input type="button" name="choose" id="choose" value="{if $listingType.id == 'Job'}[[Select Employer from the list]]{else}[[Choose User]]{/if}" class="grayButton right" /></td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="align_right">
                        <input type="button" name="next" value="[[Cancel]]" class="grayButton"  onclick="location.href='{$GLOBALS.site_url}/manage-{$listingType.link}/?restore=1'" />
                        <input type="submit" name="next" class="grayButton"  value="[[Next]]" />
                    </div>
                </td>
            </tr>
        </table>
	</form>
</fieldset>