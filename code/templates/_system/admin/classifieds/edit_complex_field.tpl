{breadcrumbs}
  <a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[{$type_info.name} Fields]]</a>
  &#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">[[{$field_info.caption}]]</a>
  &#187; <a href='{$GLOBALS.site_url}/edit-listing-field/edit-fields/?field_sid={$field_sid}'>[[Edit Fields]]</a>
  &#187; [[Edit Listing Field]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Listing Field]]</h1>

{include file="field_errors.tpl"}
<fieldset>
	<legend>[[Listing Field Info]]</legend>
	<form method="post" action="">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="sid" value="{$sid}" />
		<input type="hidden" name="field_sid" value="{$field_sid}" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
				<tr {if in_array($field_name, array('id', 'type', 'instructions'))}style="display: none;"{/if}>
					<td>
						{if $form_field.id == 'default_value'}
							<div id='defaultCaption' style='display: block;'>[[{$form_field.caption}]]</div>
						{else}
							[[{$form_field.caption}]]
						{/if}
					</td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>
						{input property=$form_field.id}
					</td>
				</tr>
				{if $form_field.comment}<tr><td style='font-size:11px;' colspan="2">{$form_field.comment}</td></tr>{/if}
				{if $form_field.id == 'signs_num'}
					<tr>
						<td></td>
						<td class="small">[[This setting will be overlapped <br />by the language setting 'Decimals' <br />in the beta version. <br />It will be fixed in the release]].</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
				<td colspan="3">
					<input type="hidden" name="old_listing_field_id" value="{$listing_field_info.id}" />
					<input type="hidden" name="apply" value="no" />
                    <div class="floatRight">
                        {if $sid}
                            <input type="submit" name="submit_form" id="apply" value="[[Apply]]" class="greenButton"/>
                        {/if}
                        <input type="submit" name="submit_form" value="[[Save]]" class="greenButton" />
                    </div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script>
    $('#apply').click(
        function(){
            $("input[name='apply']").attr('value', 'yes');
        }
    );
</script>