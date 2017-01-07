<script type="text/javascript">
	function windowMessage(message){
		$("#messageBox").dialog( 'destroy' ).html(message);
		$("#messageBox").dialog({
			width: 370,
			height: 170,
			title: '[[Error]]',
			buttons: {
				OK: function(){
					$(this).dialog('close');
				}
			}
			
		}).dialog( 'open' );
		
		return false;
	}

</script>

{breadcrumbs}
	<a href="{$GLOBALS.site_url}/badges/{$userGroup.id|lower}/">[[{$userGroup.name} Badges]]</a> &#187; [[Edit badge]]
{/breadcrumbs}
<h1><img src="{image}/icons/paperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Badge]]</h1>
<div id="messageBox" style="display: none;"></div>

<div class="addBadge">
{include file="../users/field_errors.tpl"}

<form method="post" action="{$GLOBALS.site_url}/edit-badge/" id="badgeForm" enctype="multipart/form-data" >
	<input type="hidden" id="action" name="action" value="save" />
	<input type="hidden" id="sid" name="sid" value="{$badge_info.sid}" />

	<div id="addBadge">
		{foreach from=$form_fields item=form_fields_info key=page_id}
			<table class="basetable" width="100%">
			      <tr>
			            <td>Photo</td>
                        <td></td>
			            <td>
                            <div class="productInputField"><input type="file" name="file_tmp" class="form-control"/></div>
			            </td>
			        </tr>
			{foreach from=$form_fields_info item=form_field}
					<tr {if $form_field.id == 'user_group_sid' || $form_field.id == 'listing_type_sid'}style="display:none;"{else}class="{cycle values = 'evenrow,oddrow'}"{/if}>
						<td>[[$form_field.caption]]</td>
						<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
						<td><div  class="productInputField">{input property=$form_field.id}</td>
					</tr>
			{/foreach}
			</table>
		{/foreach}
        <div class="clr"><br/></div>
		<div class="badge-buttons">
			<input id="apply" type="submit" class="grayButton" value="[[Apply]]" /> <input type="submit" class="grayButton" value="[[Save]]" id="saveBadge" />
		</div>
	</div>
</form>
</div>
<script type="text/javascript">
    $('#apply').click(
         function(){
             $('#action').attr('value', 'apply_badge');
             return validatePeriod();
         }
     );
</script>
