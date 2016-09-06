{breadcrumbs}<a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$listing_type_sid}">[[$listing_type_info.name Fields]]</a> &#187; [[Add Listing Field]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add New $listing_type_info.name Field]]</h1>
{include file="field_errors.tpl" errors=$errors}
<fieldset>
	<legend>[[Add New $listing_type_info.name Field]]</legend>
	<form method="post" action="">
	<input type="hidden" name="action" value="add" />
	<input type="hidden" name="listing_type_sid" value="{$listing_type_sid}" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
				<tr style="{if $form_field.id == 'id'}display: none;{/if}">
					<td>[[{$form_field.caption}]]</td>
					<td class="required">{if $form_field.is_required}*{/if}</td>
					<td>{input property=$form_field.id}</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="3">
                    <div class="floatRight"><input type="submit" value="[[Save]]" class="greenButton"/></div>
                </td>
			</tr>
		</table>
	</form>
</fieldset>
<script>
	$('#caption').change(function() {
		var prefix = 'id_{$listing_type_info.id}_';
		var val = prefix + $(this).val();
		val = val.replace(/[^\w]/g, '');
		if (val.length > 50) { // too long
			val = prefix;
		}
		if (val == prefix) { // too long or invalid chars
			var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			for (var i = 0; i < 7; i++)
				val += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		$('#id').val(val);
	}).change();
</script>