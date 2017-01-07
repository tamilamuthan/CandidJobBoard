{if $GLOBALS.is_ajax}
	<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/themes/green/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	    
	<script language="javascript">
	
	var url = "{$GLOBALS.site_url}/edit-user/";
	

	$("#editUserForm").submit(function() {
		var options = {
			target: "#messageBox",
			url:  url,
			succes: function(data) {
				$("#messageBox").html(data).dialog({ width: 200});
			}
		};
		$(this).ajaxSubmit(options);
		return false;
	});
	</script>
{/if}

{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">
		[[{$user_group_info.name} Profiles]]
	</a>
	&#187;
	[[Edit {$user_group_info.name}]]
{/breadcrumbs}
<h1><img src="{image}/icons/users32.png" border="0" alt="" class="titleicon"/> [[Edit {$user_group_info.name}]]</h1>

<p>
	{*{foreach from=$listingTypes item=listingType}*}
		{*<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingType.id}&username={$user_info.username}&edit_user=1" class="grayButton">[[Add New {$listingType.name}]]</a>*}
	{*{/foreach}*}
    {*<a href="{$GLOBALS.site_url}/system/applications/view/?user_sid={$user_info.sid}" class="grayButton">[[Manage Applications]]</a>*}
	{if $user_group_info.id == 'Employer'}
		<a href="{$GLOBALS.site_url}/manage-jobs/?company_name[like]={$user_info.username|escape:'url'}" class="grayButton">[[View {$user_group_info.name} Jobs]]</a>
	{/if}
	<a href="{$GLOBALS.site_url}/user-products/?user_sid={$user_info.sid}" class="grayButton">[[{$user_group_info.name} Products]]</a>
      
   	<a href="{$GLOBALS.site_url}/user-badges/?user_sid={$user_info.sid}" class="grayButton">[[{$user_group_info.name} Badges]]</a>

    {*<a href="{$GLOBALS.site_url}/system/users/acl/?type=user&amp;role={$user_info.sid}" class="grayButton">[[View Permissions]]</a>*}
</p>
{include file='field_errors.tpl'}
<br/>
<fieldset>
	<legend>[[User Info]]</legend>
	<form method="post" enctype="multipart/form-data" id="editUserForm">
		{set_token_field}
		<input type="hidden" id="action_name" name="action_name" value="save_info" />
		<table>
			{foreach from=$form_fields item=form_field}
				<tr {if $form_field.id == 'Location'}style="display: none;"{/if}>
					<td valign="top">[[{$form_field.caption}]]</td>
					<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
					<td>
						<div style="float: left;">{input property=$form_field.id}</div>
						{if in_array($form_field.type, array('multilist'))}
							<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
				<td valign="top">IP</td>
				<td valign="top"></td>
				<td>{$user_info.ip}</td>
			</tr>
			<tr>
				<td colspan="3">
                    <div class="floatRight">
                        <input type="hidden" name="user_sid" value="{$user_info.sid}" />
                        <input type="submit" id="apply" value="[[Apply]]" class="grayButton" />
                        <input type="submit" value="[[Save]]" class="grayButton" />
                    </div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script>
	$('#apply').click(
		function() {
			$('#action_name').attr('value', 'apply_info');
		}
	);
</script>