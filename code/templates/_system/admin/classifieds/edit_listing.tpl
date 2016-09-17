{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/?restore=1">
		{if $listingType.id == 'Job'}[[{$listingType.name} Postings]]{else}[[Resumes]]{/if}
	</a>
	&#187; [[Edit {$listingType.name}]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit {$listingType.name}]]</h1>

{if $GLOBALS.is_ajax}
	<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/themes/green/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	<script language="javascript" type="text/javascript">
		var url = "{$GLOBALS.site_url}/edit-listing/";
		$("#editListingForm").submit(function() {
			var options = {
				target: "#messageBox",
				url:  url,
				success: function(data) {
					$("#messageBox").html(data).dialog('width': '200');
				}
			};
			$(this).ajaxSubmit(options);
			return false;
		});
	</script>
{/if}

{include file='field_errors.tpl'}
<p>[[Fields marked with an asterisk (<span class="required">*</span>) are mandatory]]</p>

<fieldset class="wide-fieldset">
	<legend>[[Edit {$listingType.name}]]</legend>
	<form method="post" enctype="multipart/form-data" action="" {if isset($form_fields.ApplicationSettings)}onsubmit="return validateForm('editListingForm');"{/if} id='editListingForm'>
		<input type="hidden" id="action" name="action" value="save_info"/>
		<input type="hidden" name="listing_id" value="{$listing.id}"/>
		{set_token_field}
		<table>
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == 'status'}
				{elseif !isset($form_fields.Resume) && $form_field.id =='ApplicationSettings'}
				<tr>
					<td class="caption-td">[[$form_field.caption]]</td>
					<td class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
					<td>{input property=$form_field.id template='applicationSettings.tpl'}</td>
				</tr>
				{elseif !isset($form_fields.Resume) && $form_field.id == 'expiration_date'}
					<tr>
						<td class="caption-td">[[$form_field.caption]]</td>
						<td class="required">{if $form_field.is_required}*{/if} </td>
						<td> {input property=$form_field.id template='expiration_date.tpl'}</td>
					</tr>
				{elseif $form_field.type == 'location'}
					{input property=$form_field.id}
                {elseif in_array($listingType.id, array('Opportunity','Idea')) && in_array($form_field.id, array('JobCategory','EmploymentType'))}
                    <input type="hidden" id="{$form_field.id}" name="action" value="0"/>
				{else}
					<tr>
						<td class="caption-td">[[{$form_field.caption}]]</td>
						<td class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
						<td><div style="float: left;">{input property=$form_field.id}</div>
						 {if in_array($form_field.type, array('multilist'))}
							<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
						 {/if}
						</td>
					</tr>
				{/if}
			{/foreach}

			<tr>
				<td colspan="3">
					<div class="floatRight">
						<input type="submit" id="apply" value="[[Apply]]" class="greenButton"/>
						<input type="submit" value="[[Save]]" class="greenButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">
	$('#apply').click(function() {
		$('#action').attr('value', 'apply_info');
	});
</script>
