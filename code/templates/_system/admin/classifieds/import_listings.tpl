{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.id|lower}s/?restore=1">
		[[{$listingType.name} Postings]]
	</a>
	&#187;
	[[Import {$listingType.name}s]]
{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon" /> [[Import {$listingType.name}s]]</h1>
{include file="error.tpl"}
<form method="post"  enctype="multipart/form-data" onsubmit="disableSubmitButton('submitImport');">
	<input type="hidden" name="listing_type_id" value="{$listingType.id}">
	<table>
		<thead>
		 	<tr>
				<th colspan="2">[[System Import Values]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[Product:]]</td>
				<td>
					<select name="product_sid">
						{foreach from=$products item=product}
							<option value="{$product.sid}">[[{$product.name}]]</option>
						{/foreach}
					</select>
				</td>
		    </tr>
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[Active Status:]]</td>
				<td><input type="checkbox" name="active" value="1" /></td>
		    </tr>
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[Activation Date:]]</td>
				<td><input type="text" name="activation_date" value="" id="activation_date_import" /></td>
			</tr>
		    <tr id="clearTable">
				<td colspan="2">&nbsp;</td>
			</tr>
			</tbody>
			<thead>
			    <tr>
					<th colspan="2">[[Data Import]]</th>
				</tr>
			</thead>
			<tbody>
			{cycle values="oddrow,evenrow" reset=true print=false}
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[File:]]</td>
				<td><input type="file" name="import_file" value="" /></td>
			</tr>
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[File Type:]]</td>
				<td>
					<select name="file_type">
						<option value="csv">CSV</option>
						<option value="xls">Excel</option>
					</select>
				</td>
			</tr>
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[Fields Delimiter:]]<br /><small>([[for CSV-file only]])</small></td>
				<td>
					<select name="csv_delimiter" >
						<option value="semicolon">[[Semicolon]]</option>
						<option value="comma">[[Comma]]</option>
						<option value="tab">[[Tabulator]]</option>
					</select>
				</td>
			</tr>
			<tr class="{cycle values="oddrow,evenrow"}">
				<td>[[Encoding:]]<br /><small>([[for CSV-file only]])</small></td>
				<td>
					<select name="encodingFromCharset" >
						<option value="UTF-8">[[Default]]</option>
						{foreach from=$charSets item=charSet}
							<option value="{$charSet}">{$charSet}</option>
						{/foreach}
					</select>
					<div class="commentSmall">[[Select appropriate encoding for your language  in case you have problems with import of certain symbols]]</div>
				</td>
			</tr>
		    <tr id="clearTable">
				<td colspan="2">
                    <div class="clr"><br/></div>
					<div class="floatRight">
						<input type="submit" name="action" value="[[Import]]" id="submitImport" class="greenButton" />
					</div>
                </td>
			</tr>
		</tbody>
	</table>
</form>

<script>
$(function () {

	var dFormat = '{$GLOBALS.current_language_data.date_format}';

	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	
	$("#activation_date_import").datepicker({
		dateFormat: dFormat,
		showOn: 'both',
		yearRange: '-99:+99',
		buttonImage: '{image}icons/icon-calendar.png'
	});

});
</script>
