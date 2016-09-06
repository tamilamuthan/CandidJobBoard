{breadcrumbs}[[RSS/XML Feeds]]{/breadcrumbs}
<h1><img src="{image}/icons/rss32.png" border="0" alt="" class="titleicon" />[[RSS/XML Feeds]]</h1>
<p>[[XML/RSS feeds allow you to broadcast job postings from your job board to other websites (e.g. Indeed, SimplyHired and etc.)]]</p>
{include file="field_errors.tpl"}
{foreach from=$errors item=error}
	<p class="error">[[{$error.message}]]</p>
{/foreach}
<table>
	<tbody>
		{foreach from=$feeds item=feed}
			<tr>
				<td>
					<h4>[[{$feed.name}]]</h4>
					<p style="font-style: italic;">
						<a href="{$GLOBALS.custom_domain_url}/feeds/{$feed.id}.xml" target="_blank" title="[[Link to this XML feed]]">{$GLOBALS.custom_domain_url}/feeds/{$feed.id}.xml</a>
					</p>
					<p>
						[[{$feed.description}]]
					</p>
				</td>
				<td>
					<a href="{$GLOBALS.site_url}/listing-feeds/?action=edit&amp;id={$feed.sid}" class="grayButton">[[Customize Feed]]</a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
