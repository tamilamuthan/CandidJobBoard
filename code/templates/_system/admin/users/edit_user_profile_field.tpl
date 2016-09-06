{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$user_group_sid}">[[{$user_group_info.name}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$user_group_sid}">[[Edit User Profile Fields]]</a> &#187; [[{$user_profile_field_info.caption}]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon" />[[Edit User Profile Field Info]]</h1>
{include file="field_errors.tpl"}

{if in_array($field_type, array('list', 'multilist'))}
    <p><a href="{$GLOBALS.site_url}/edit-user-profile-field/edit-list/?field_sid={$user_profile_field_sid}" class="grayButton">[[Edit List Values]]</a></p>
{elseif $field_type eq 'location'}
    <p><a href="{$GLOBALS.site_url}/edit-user-profile-field/edit-location-fields/?field_sid={$user_profile_field_sid}" class="grayButton">[[Edit Fields]]</a></p>
{/if}

<fieldset>
	<legend>[[User Profile Field Info]] - [[{$user_profile_field_info.caption}]]</legend>
	<table>
		<form id="fieldData" method="post" enctype="multipart/form-data">
			<input type="hidden" id="action" name="action" value="save_info">
			<input type="hidden" name="sid" value="{$user_profile_field_sid}">
			<input type="hidden" name="user_group_sid" value="{$user_group_sid}">
			{foreach from=$form_fields key=field_name item=form_field}
				{if $form_field.id == 'width' && $field_type == 'logo'}
					<tr><td colspan="3" style="font-weight: bold; padding-top:10px;">[[Company Info]]</td></tr>
				{elseif $form_field.id == 'second_width' && $field_type == 'logo'}
					<tr><td colspan="3" style="font-weight: bold; padding-top:10px;">[[Featured Companies]]</td></tr>
				{/if}
				<tr id="tr_{$field_name}">
					<td id="td_caption_{$field_name}">[[{$form_field.caption}]] </td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>
						{if $field_name eq 'choiceLimit'}
							{input property=$form_field.id}<br />
							<span class="commentSmall">[[Set empty or 0 for unlimited selection]]</span>
						{elseif $field_name == 'display_as' && ($field_type == 'list' || $field_type == 'multilist')}
							{input property=$form_field.id template="list_empty.tpl"}
						{else}
						{input property=$form_field.id}
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
                <td colspan="3">
                    <div class="floatRight">
                        <input type="button" id="apply" value="[[Apply]]" class="greenButton"/>
                        <input type="button" id="save" value="[[Save]]" class="greenButton" />
                    </div>
                </td>
            </tr>
		</form>
	</table>
</fieldset>
<div id="messageWindow" style="display: none;">
	<p>[[You are trying to edit the system field (id). If you change the default value of this field there would be a need to make appropriate changes in the settings, templates and PHP code. Otherwise the system will function unpredictably]]</p>
</div>
{capture name="change_anyway"}[[Change anyway]]{/capture}
{capture name="don_t_change"}[[Don't change]]{/capture}

<script type="text/javascript">

var userFieldId = "{$user_profile_field_info.id}";
$('#apply').click(function () {
		$('#action').attr('value', 'apply_info');
		saveFieldSettings();
});
$('#save').click(function () {
		saveFieldSettings();
});

function saveFieldSettings()
{
	if ((userFieldId == $("input[name='id']").val()) || (userFieldId == 'Location')) {
		$('#fieldData').submit();
	} else {
		showMessageWindow();
	}
}

function showMessageWindow()
{
	$("#messageWindow").dialog({
		width: 600,
		height: 200,
		buttons: {
			"{$smarty.capture.change_anyway|escape:"javascript"}": function () {
				$('#fieldData').submit();
			},
			"{$smarty.capture.don_t_change|escape:"javascript"}": function () {
				$("input[name='id']").val(userFieldId);
				$('#action').attr('value', 'save_info');
				$("#messageWindow").dialog('destroy');
			}
		}
	});
}

</script>