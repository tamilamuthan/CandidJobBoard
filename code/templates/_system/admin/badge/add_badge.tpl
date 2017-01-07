{javascript}
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
{/javascript}

{breadcrumbs}
	<a href="{$GLOBALS.site_url}/badges/{$userGroup.id|lower}/">[[{$userGroup.name} Badges]]</a> &#187; [[Add New Badge]]
{/breadcrumbs}
<h1>[[Add New Badge]]</h1>
<div id="messageBox" style="display: none;"></div>
<div class="addBadge">
{include file="../users/field_errors.tpl"}

<form method="post" action="{$GLOBALS.site_url}/add-badge/" id="badgeForm" enctype="multipart/form-data" >
	<input type="hidden" id="action" name="action" value="save" />

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
					<tr {if $form_field.id == 'user_group_sid' || $form_field.id == 'listing_type_sid'}style="display:none;"{else}class="{cycle values = 'evenrow,oddrow'} {if in_array($form_field.id, array('listing_duration', 'number_of_listings', 'featured'))}post-listing-field{/if}"{/if}>
						<td>[[$form_field.caption]]</td>
						<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
						<td><div class="productInputField">{input property=$form_field.id} {if $form_field.id == 'width' || $form_field.id == 'height'} [[pixels]]{/if}</div></td>
					</tr>
			{/foreach}
			</table>
		{/foreach}
        <div class="clr"><br/></div>
        <div class="product-buttons"><input type="submit" class="grayButton" value="[[Save]]" id="saveBadge" /></div>
	</div>
</form>
</div>
