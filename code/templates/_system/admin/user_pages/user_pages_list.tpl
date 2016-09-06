{breadcrumbs}[[Pages]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/>[[Pages]]</h1>
<p><a href="{$GLOBALS.site_url}/user-pages/?action=new" class="grayButton">[[Add a New Page]]</a></p>
<table>
	<thead>
		<tr>
			<th>
				[[URI]]
			</th>
			<th>[[Title]]</th>
			<th colspan="2" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$pages_list item=page name="foreach"}
			<tr class="{cycle values="oddrow,evenrow"}">
				<td><a href="{$GLOBALS.site_url}/..{$page.uri}" target="_blank">{$page.uri}</a></td>
				<td>[[{$page.title}]]</td>
				<td><a href="?action=edit_page&amp;uri={$page.uri|escape:'url'}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
				<td><a href="?action=delete_page&amp;uri={$page.uri|escape:'url'}" onclick="return confirm('[[Are you sure you want to delete this page?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
			</tr>
		{/foreach}
	</tbody>
</table>
