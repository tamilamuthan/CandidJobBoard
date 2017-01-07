<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.form.js"></script>
<script type="text/javascript">
	function formSubmit() {
		var options = {
				  target: "#dialog",
				  url:  $("#addBadgeForm").attr("action")
				}; 
		$("#addBadgeForm").ajaxSubmit(options);
		return false;
	}
	{if $achievement_added == 1}
		var progbar = "<img src='{$GLOBALS.site_url}/../system/ext/jquery/progbar.gif' />";
		$("#dialog").dialog('destroy').html("[[Please wait ...]]" + progbar).dialog( {ldelim}width: 200{rdelim});
		parent.document.location.reload();
	{/if}
</script>

{if $errors}
    {foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'UNDEFINED_BADGE_SID'} [[Badge ID is not defined]]{/if}
		</p>
	{/foreach}
{/if}

<form action="{$GLOBALS.site_url}/add-user-badge/" method="POST" id="addBadgeForm" onsubmit='return formSubmit();'>
	[[Select Badge]]:
	<select name="badge_sid" id="badge_sid">
	{foreach from=$badges item=badge}
		<option value="{$badge.sid}">[[{$badge.name}]]</option>
	{/foreach}
	</select>
	<br/>
	<input type="hidden" name="user_sid" value="{$user_sid}" />
	<input type="hidden" name="action" value="add_badge" />
	<span class="greenButtonEnd"><input type="submit" id="add" name="add" value="[[Add]]" class="greenButton" /></span>
</form>
