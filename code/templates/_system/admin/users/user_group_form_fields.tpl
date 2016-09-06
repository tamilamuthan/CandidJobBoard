<table>
	{foreach from=$form_fields key=field_id item=form_field}
		{if strpos($form_field.id, 'notif') === false
			&& $form_field.id != 'welcome_email'
		}
			<tr>
				<td valign="top" width="42%">[[{$form_field.caption}]]</td>
				<td>{if $form_field.is_required}<span class="color-red">*</span>{/if}</td>
				<td>{input property=$form_field.id}</td>
			</tr>
		{/if}
	{/foreach}
</table>

{* >>>> NOTIFICATIONS >>>> *}
<div id="mediumButton" class="setting_button">[[Notification Settings]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div  class="setting_block" style="display: none">
	<table>
		<tr>
			<td colspan="3" style='font-size:11px'>
				* [[These settings will be applied by default for newly registered users of this group]].<br/>
				* [[Select "None" if you want to disable sending of notification]].</td>
		</tr>

		{foreach from=$notifications item="notificationsByGroup" key="notificationGroupID"}
			<tr>
				<td colspan="3" style='font-size:16px;font-weight: bold;'>[[{$notificationGroups.$notificationGroupID}]]:</td>
			</tr>
			{foreach from=$notificationsByGroup key="notificationID" item="notification"}
				{assign var="form_field" value=$form_fields.$notificationID}
				{if $form_field.type == 'integer'}
					<tr>
						<td>&nbsp;</td>
						<td class="notifications">{input property=$form_field.id}</td>
						<td>[[Days before]]</td>
					</tr>
				{else}
					<tr>
						<td colspan="1">[[{$form_field.caption}]]{if $form_field.is_required}<span class="required">*</span>{/if}</td>
						<td colspan="2">
							{input property=$form_field.id template="list_none.tpl"}
							{if $user_group_info[$notificationID] && $user_group_info[$notificationID] != 'DoNotSend'}
								<a href="{$GLOBALS.site_url}/edit-email-templates/{$notificationGroupID}/{$user_group_info[$notificationID]}" target="_blank" title="[[Edit]] {$form_field.caption}" class="edit-email-template"></a>
							{/if}
						</td>
					</tr>
				{/if}

			{/foreach}

		{/foreach}
	</table>
</div>
{* <<<< NOTIFICATIONS <<<< *}


<script type="text/javascript">
	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
			if ($(this).css("display") == "block") {
				butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
			} else {
				butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
			}
		});
	});
</script>