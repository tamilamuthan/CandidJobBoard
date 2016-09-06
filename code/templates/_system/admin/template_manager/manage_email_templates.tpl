{breadcrumbs}
	[[Email Templates]]
{/breadcrumbs}

<h1><img src="{image}/icons/contactbook32.png" border="0" alt="" class="titleicon" />{if $etGroups.$group}[[{$etGroups.$group}]]{else}[[Email Templates]]{/if}</h1>

<div class="clr"><br/></div>

<table>
	<thead>
		<tr>
			<th>[[Template Name]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{assign var="counter" value=0}
		{foreach from=$templates item="template"}
			{assign var="counter" value=$counter+1}
			<tr class="{if $template@index is odd}oddrow{else}evenrow{/if}">
				<td>[[{$template.name}]]</td>
				<td align=center>
					<a href="{$GLOBALS.site_url}/edit-email-templates/{$template.sid}/" title="[[Edit]]" class="editbutton">[[Edit]]</a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>



