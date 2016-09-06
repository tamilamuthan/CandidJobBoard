{breadcrumbs}<a href="{$GLOBALS.site_url}/edit-listing-type/?sid={$type_sid}">[[{$type_info.name} Fields]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$field_sid}">{$field_info.caption}</a> &#187; [[Edit Fields]]{/breadcrumbs}
<h1>[[Edit Fields]]</h1>
<table>
	<thead>
		<tr>
			<th>[[Caption]]</th>
			<th>[[Type]]</th>
			<th>[[Required]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$listing_fields item=listing_field}
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[{$listing_field.caption}]]</td>
				<td>[[{$listing_field.typeCaption}]]</td>
				<td>{display property='is_required' object_sid=$listing_field.sid}</td>
				<td><a href="?sid={$listing_field.sid}&field_sid={$field_sid}&action=edit" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
			</tr>
		{/foreach}
	</tbody>
</table>