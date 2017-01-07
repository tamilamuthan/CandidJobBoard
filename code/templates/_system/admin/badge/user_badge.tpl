<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.form.js"></script>
<script>
	{if $errors}
		reloadPage = false;
	{/if}
	{literal}
	function formSubmit() {
		var options = {
		  	target: "#dialog",
		  	url:  $("#changeExpirationDate").attr("action"),
		  	success: function() {
		  		if (reloadPage) {
					$("#dialog").dialog("close");
					parent.document.location.reload();
		  		}
			}
		};
		$("#changeExpirationDate").ajaxSubmit(options);
		return false;
	}
</script>
{/literal}
<p><b>Username:</b> {$user.username}</p>
{if $deleted == 'yes'}
	<p class="error">[[Badge(s) was deleted]]</p>
	{literal}
	<script> var parentReload = true;</script>
	{/literal}
{elseif $deleted == 'no'}
	<p class="error">[[Achievement(s) was not deleted]]</p>
{else}
{foreach from=$errors item=val key=error}
	{if $error eq 'WRONG_DATE_FORMAT'}
		<p class="error">[[Please enter the date in valid format]]</p>
	{/if}
{/foreach}

<form method="post" action='{$GLOBALS.site_url}/user-badge/' id="changeExpirationDate" onsubmit='return formSubmit();'>
<input type='hidden' name='user_sid' value='{$user_sid}' />
<input type='hidden' name='action' id='action' value='' />
{foreach from=$achievementsInfo item=achievementInfo}
{if $countAchievements == 1}
	<input type="hidden" name="achievement_sids[{$achievementInfo.id}]" value="1" />
	<table border="1" cellspacing="0" cellpadding="3" width="530">
		<tr>
			<td width="50%">[[Badge]]:</td>
			<td width="50%">[[{$achievementInfo.badge.name|default:"&nbsp;"}]]</td>
		</tr>
		<tr>
			<td>[[Subscription date]]:</td>
			<td>[[{$achievementInfo.creation_date|default:"&nbsp;"}]]</td>
		</tr>
		<tr>
			<td>[[Subscription expiration date]]:</td>
			<td><input type="text" class="displayDate" style="z-index:99999;" name="expired_date[{$achievementInfo.id}]" value="[[{$achievementInfo.expired_date|default:"Never Expire"}]]"  id="expired_date_{$achievementInfo.id}"/></td>
		</tr>
		<tr>
			<td>[[Subscription price]]:</td>
			<td>{tr type="float"}{$achievementInfo.price}{/tr}</td>
		</tr>
	</table>
{else}
	<h3><input type="checkbox" name="achievement_sids[{$achievementInfo.id}]" value="1" />[[{$achievementInfo.badge.name|default:"&nbsp;"}]]</h3>
	<table border="1" cellspacing="0" cellpadding="3" width="530">
		<tr>
			<td>[[Subscription date]]:</td>
			<td>[[{$achievementInfo.creation_date|default:"&nbsp;"}]]</td>
		</tr>
		<tr>
			<td>[[Subscription expiration date]]:</td>
			<td><input type="text" class="displayDate" style="z-index:99999;" name="expired_date[{$achievementInfo.id}]" value="[[{$achievementInfo.expired_date|default:"Never Expire"}]]"  id="expired_date_{$achievementInfo.id}"/></td>
		</tr>
		<tr>
			<td>[[Subscription price]]:</td>
			<td>{tr type="float"}{$achievementInfo.price}{/tr}</td>
		</tr>
	</table>
{/if}
{/foreach}
<br />
<a href="{$GLOBALS.site_url}/system/users/acl/?type=user&amp;role={$user_sid}">[[View user permissions]]</a>
<br/><br/>
	<div style="float: left">
		{if $countAchievements == 1}[[Remove Badge]]:{else}[[Remove Selected Badge(s)]]{/if}
		<input type='hidden' name='user_sid' value='{$user_sid}' />
		<input type='hidden' name='achievement_id' value='{$achievement_id}' />
		<input type='hidden' name='badge_to_change' value='0' />
		<span class="greenButtonEnd"><input type="submit" id="change_plan_send_button"  value="[[Remove]]" class="greenButton" onClick="javascript: reloadPage = false; return confirm('[[Are you sure you want to remove the selected badge(s)?]]');"/></span>
	</div>
	<div style='text-align:right;'><span class="greenButtonEnd"><input type="submit" id="save"  value="[[Save]]" class="greenButton" onClick="javascript: reloadPage = true;" /></span></div>
</form>
{if $changed}<script> var parentReload = true;</script>{/if}
{/if}

<script>

var dFormat = '{$GLOBALS.current_language_data.date_format}';
	
{literal}
dFormat = dFormat.replace('%Y', "yy");
dFormat = dFormat.replace('%m', "mm");
dFormat = dFormat.replace('%d', "dd");

$( function() {
	{/literal}
	{foreach from=$achievementsInfo item=achievementInfo}
	{literal}
	$("#expired_date_{/literal}{$achievementInfo.id}{literal}").datepicker({
		dateFormat: dFormat, 
		showOn: 'both',
		changeMonth: true,
		changeYear: true,
		minDate: new Date(1940, 1 - 1, 1),
		maxDate: '+10y +0m +0w',
		yearRange: '-99:+99',
		buttonImage: '{/literal}{image}icons/icon-calendar.png{literal}'
	});
	{/literal}
	{/foreach}
	{literal}
	$("#save").click(function() {
		$("#action").val('changeExpirationDate');
	});
	$("#change_plan_send_button").click(function() {
		$("#action").val('change');
	});
});
{/literal}
</script>
