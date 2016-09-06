<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	var progbar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";
	var parentReload = false;
	$(function() {
		$(".getUser").click(function() {
			$("#dialog").dialog('destroy');
			$("#dialog").attr({ title: "Loading"});
			$("#dialog").html(progbar).dialog({ width: 180});
			var link = $(this).attr("href");
			$.get(link, function(data) {
				$("#dialog").dialog('destroy');
				$("#dialog").attr({ title: "User Product Details"});
				$("#dialog").html(data).dialog({
					width: 560,
					close: function(event, ui) {
						$("#expired_date").datepicker( 'hide' );
						if (parentReload == true) {
							window.location = "?restore=1";
						}
					}
				});
			});
			return false;
		});

		$("tr[id^='users']").click(function() {
			var name = ($(this).attr('id'));
			if( !$(this).attr('style') ) {
				$("input[name='" + name + "']").attr('checked','checked');
				$(this).attr('style','background-color: #ffcc99');
			} else {
				$(this).removeAttr('style');
				$("input[name='" + name + "']").removeAttr('checked');
			}
		});
	});

	function login_as_user( name, pass ) {
		$.get('{$GLOBALS.site_url}/login-as-user/', { username: name, password: pass}, function (data) {
			var response = $.trim(data);
			if (response == "") {
				document.login.username.value = name;
				document.login.password.value = pass;
				document.getElementById('login').submit();
			}
			else {
				popUpMessageWindow(300, 100, '[[Error]]', data);
			}
		});
	}

	function isPopUp(button, textChooseAction, textChooseItem, textToDelete) {
		if (isActionEmpty(button, textChooseAction, textChooseItem)) {
			var action = $("#selectedAction_" + button).val();
			switch (action) {
				case "delete":
					if (confirm(textToDelete)) {
						submitForm("delete");
					}
					break;
				default:
					submitForm(action);
					break;
			}
		}
		$("#selectedAction_" + button).val('');
	}

	function viewListingBlock() {
        $("#product_select option").each(function () {
        	$("#block_"+this.value).css('display', 'none');
          });
	
        $("#product_select option:selected").each(function () {
           $("#block_"+this.value).css('display', 'block');
         });
	}
	</script>

{if $errors}
	{foreach from=$errors item="error"}
		<p class="error">[[$error]]</p>
	{/foreach}
{/if}
<div id="dialog" style="display: none"></div>
<form id="login" name="login" target="_blank"  action="{$GLOBALS.user_site_url}/login/" method="post">
    <input type="hidden" name="action" value="login" />
    <input type="hidden" name="as_user" />
    <input type="hidden" name="username" value="" />
    <input type="hidden" name="password" value="" />
</form>
<div class="clr"><br/></div>
<form method="post" name="users_form">
	<input type="hidden" name="action_name" id="action_name" value="" />
	{*<input type="hidden" name="product_to_change" id="product_to_change" value="" />*}
	<input type="hidden" name="number_of_listings" id="number_of_listings" value="" />
	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
					{include file="../pagination/sort.tpl"}
					</thead>
					{foreach from=$found_users item=user name=users_block}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><input type="checkbox" name="users[{$user.sid}]" value="1" id="checkbox_{$smarty.foreach.users_block.iteration}" /></td>
							{if $userGroupInfo.id == 'Employer'}
								<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user.sid}" title="Edit">{$user.CompanyName|escape:'html'}</a></td>
								<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user.sid}" title="Edit"><b>{$user.username}</b></a></td>
								<td>
									{$user|location}
								</td>
							{elseif $userGroupInfo.id == 'JobSeeker'}
								<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user.sid}" title="Edit"><b>{$user.FullName}</b></a></td>
								<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user.sid}" title="Edit"><b>{$user.username}</b></a></td>
							{/if}

							<td>{$user.registration_date|date:null:true}</td>
							<td>
								{if $user.active == "1"}
									[[Active]]
								{else}
									[[Not Active]]
								{/if}
							</td>
							<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/edit-user/?user_group={$userGroupInfo.id}&amp;user_sid={$user.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
							<td nowrap="nowrap" style="border-left: 0px;"><span class="greenButtonEnd"><input type="button" name="button" value="[[Login]]" class="greenButton" onclick="login_as_user('{$user.username}', '{$user.password}');" /></span></td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>