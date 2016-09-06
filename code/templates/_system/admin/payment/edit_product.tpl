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
	<a href="{$GLOBALS.site_url}/products/{$userGroup.id|lower}/">[[{$userGroup.name} Products]]</a> &#187; [[Edit product]]
{/breadcrumbs}
<h1><img src="{image}/icons/paperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Product]]</h1>
<div id="messageBox" style="display: none;"></div>

<div class="addProduct">
{include file="../users/field_errors.tpl"}

<form method="post" action="{$GLOBALS.site_url}/edit-product/" id="productForm">
	<input type="hidden" id="action" name="action" value="save" />
	<input type="hidden" id="sid" name="sid" value="{$product_info.sid}" />

	<div id="addProduct">
		{foreach from=$form_fields item=form_fields_info key=page_id}
			<table class="basetable" width="100%">
			{foreach from=$form_fields_info item=form_field}
				{if $form_field.id == 'availability_from'}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[$form_field.caption]]</td>
						<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
						<td><div  class="productInputField">[[from]] {input property=$form_field.id} [[to]] {input property=availability_to}</div>{if $form_field.comment}<br/><small>[[{$form_field.comment}]]</small>{/if}</td>
					</tr>
				{elseif $form_field.id == 'availability_to'}
				{* *}
				{elseif $form_field.id == 'expiration_period'}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td colspan="2">[[Product expires in]]</td><td> {input property=$form_field.id} [[days after purchase]]{if $form_field.comment}<br/><small>[[{$form_field.comment}]]</small>{/if}</td>
					</tr>
				{elseif $form_field.id == 'period'}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[$form_field.caption]]</td>
						<td class="productInputReq">{foreach from=$form_fields_info item=formFieldReq}{if $formFieldReq.id == 'period_name' && $formFieldReq.is_required}*{/if}{/foreach}</td>
						<td><div  class="productInputField">{input property=$form_field.id} {input property=period_name template="list_period.tpl"}</div></td>
					</tr>
				{elseif $form_field.id == 'period_name'}
				{else}
					<tr {if $form_field.id == 'user_group_sid' || $form_field.id == 'listing_type_sid'}style="display:none;"{else}class="{cycle values = 'evenrow,oddrow'} {if in_array($form_field.id, array('listing_duration', 'number_of_listings', 'featured'))}post-listing-field{/if}"{/if}>
						<td>[[$form_field.caption]]</td>
						<td class="productInputReq">{if $form_field.is_required}*{/if}</td>
						<td><div  class="productInputField">{if $form_field.id == 'price'}{currencySign} {/if}{input property=$form_field.id} {if $form_field.id == 'listing_duration'}[[days]]{/if} {if $form_field.id == 'width' || $form_field.id == 'height'} [[pixels]]{/if}{if $form_field.comment}<br/><small>[[{$form_field.comment}]]</small>{/if}</td>
					</tr>
				{/if}
			{/foreach}
			</table>
		{/foreach}
        <div class="clr"><br/></div>
		<div class="product-buttons">
			<input id="apply" type="submit" class="grayButton" value="[[Apply]]" /> <input type="submit" class="grayButton" value="[[Save]]" id="saveProduct" />
		</div>
	</div>
</form>
</div>
<div id="periodMessage" style="display: none">[[If you want to set up and unlimited period please leave the 'Period' field blank and select 'Unlimited']]</div>
<script type="text/javascript">
	$('[name="post_job"], [name="post_resume"]').change(function() {
		$('.post-listing-field').toggle(this.checked);
		if ($('[name="post_resume"]').length) {
			$('[name="number_of_listings"]')
					.val(1)
					.closest('.post-listing-field').hide();
		}
	}).change();

    $('#apply').click(
         function(){
             $('#action').attr('value', 'apply_product');
             return validatePeriod();
         }
     );
	$(function() {
		$("#saveProduct").click(function(){
			return validatePeriod();
		});
	});

	function validatePeriod() 
	{
		var period_name = $("#period_name").val();
		if (period_name == 'unlimited') {
			$("#period").attr('disabled', false);
			$("#period").val('');
		}
		return true;
	}
</script>