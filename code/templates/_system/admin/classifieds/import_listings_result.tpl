{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.id|lower}s/?restore=1">
		[[{$listingType.name} Postings]]
	</a>
	&#187;
	[[Import {$listingType.name}s]]
{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon" /> [[Import {$listingType.name}s]]</h1>
{if $imported_listings_count}
	<p class="message">[[$imported_listings_count listings were successfully imported.]]</p>
{/if}
{if !empty($nonExistentUsers)}
	{assign var="notImportedListingsCount" value=$nonExistentUsers|count}
	<p class="error">[[$notImportedListingsCount listings were not imported.]]</p>
	{foreach from=$nonExistentUsers item="username"}
		<p class="error">[[User '$username' not found.]]</p>
	{/foreach}
{/if}
