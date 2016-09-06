{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$selected_listing_type_id|lower}s/?restore=1">
		[[{$selected_listing_type_id} Postings]]
	</a>
	&#187;
	[[Export {$selected_listing_type_id}s]]
{/breadcrumbs}
<h1><img src="{image}/icons/boxupload32.png" border="0" alt="" class="titleicon" /> [[Export {$selected_listing_type_id}s]]</h1>

<form method="post">
	<table class="basetable">
		<input type="hidden" name="action" value="export" />
		<thead>
			<tr>
				<th colspan="6">[[Export Filter]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="evenrow" style="display: none;"><td>[[Listing Type]]: </td><td colspan="5">{search property="listing_type" template="list_with_reload.tpl"}</td></tr>
			<tr class="oddrow"><td>[[Activation Date]]:</td><td colspan="5">{search property="activation_date"}</td></tr>
			<tr class="evenrow"><td>[[Expiration Date]]:</td>	<td colspan="5">{search property="expiration_date"}</td></tr>
			<tr class="oddrow"><td>[[Username]]: </td><td colspan="5">{search property="username"}</td></tr>
			<tr class="evenrow"><td>[[Featured]]: </td><td colspan="5">{search property="featured"}</td></tr>
		</tbody>
		<tr id="clearTable"><td colspan="6">&nbsp;</td></tr>
		<thead>
			<tr>
				<th colspan="6">[[Listing Properties To Export]]</th>
			</tr>
		</thead>
		<tbody class="listings-properties">
			<tr class="oddrow">
				{assign var='i' value=1}
				{foreach from=$properties.system item=property_id name=system_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property_id}]" value="1" /> {$property_id}</td>
					{if $i % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
					{assign var='i' value=$i+1}
				{/foreach}
				{foreach from=$properties.common item=property name=common_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property.id}]" value="1" /> [[{$property.caption}]]</td>
					{if $i % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
					{assign var='i' value=$i+1}
				{/foreach}
				{foreach from=$properties.extra item=property name=extra_properties}
					<td colspan="2"><input type="checkbox" name="export_properties[{$property.id}]" value="1" /> [[{$property.caption}]]</td>
					{if $i % 3 == 0}
						</tr><tr class="{cycle values="evenrow,oddrow"}">
					{/if}
					{assign var='i' value=$i+1}
				{/foreach}
			</tr>
				<tr class="{cycle values="evenrow,oddrow"}"><td colspan="6"><a href="#" onClick="check_all_system();return false;">[[Select]]</a> / <a href="#" onClick="uncheck_all_system();return false;">[[Deselect All]]</a></td>
			</tr>
		</tbody>
		<tbody>
			<tr id="clearTable">
				<td colspan="6">
                    <div class="clr"><br/></div>
                    <div class="floatRight"><input type="submit" value="[[Export]]" class="grayButton" /></div>
                </td>
			</tr>
		</tbody>
	</table>
</form>
<br/>
<script language="Javascript">
	$(function() {

		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");

		$("#activation_date_notless, #activation_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});
		$("#expiration_date_notless, #expiration_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});

	});

	function check_all_system() 	{ set_checkbox_to(true); }
	function uncheck_all_system() 	{ set_checkbox_to(false); }

	function set_checkbox_to(flag)
	{
		$('.listings-properties :checkbox').each(function() {
			this.checked = flag;
		});
	}

</script>
