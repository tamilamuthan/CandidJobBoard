{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-users/{$user_group.id|lower}/?restore=1">
		[[{$user_group.name} Profiles]]
	</a>
	&#187;
	[[Add a New {$user_group.name}]]
{/breadcrumbs}
<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Add {$user_group.name}]]</h1>
{include file="field_errors.tpl"}

<fieldset>
	<legend>[[Add a New {$user_group.name}]]</legend>
	<form id="editUserForm" method="post" enctype="multipart/form-data" action="{$GLOBALS.site_url}/add-user/{$user_group.id|lower}/" onsubmit="disableSubmitButton('submitAdd');">
		{set_token_field}
		<input type="hidden" name="action" value="add">
		<input type="hidden" name="user_group_id" value="{$user_group.id}">
		<table>
			{foreach from=$form_fields item=form_field}
				<tr {if $form_field.id == 'Location'}style="display: none;"{/if}>
					<td valign="top">[[{$form_field.caption}]]</td>
					<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
					<td>
						<div style="float: left;">
							{input property=$form_field.id}
						</div>
						{if in_array($form_field.type, array('multilist'))}
							<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="3">
					<div class="floatRight">
						<input type="submit" value="[[Add]]" class="grayButton" id="submitAdd" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>