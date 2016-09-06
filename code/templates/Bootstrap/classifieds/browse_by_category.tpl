{assign var=i value=1}
<ul class="list-unstyled browse-by__list">
	{foreach from=$browseItems key=id item=category}
		{if $category.count > 0 && $i <= $recordsNumToDisplay}
			{assign var=i value=$i+1}
			<li>
				<a href="{$GLOBALS.site_url}/categories/{$id}/{$category.caption|pretty_url}-jobs/">
					<span class="browse-by__item">[[{$category.caption|escape:'html'|truncate:28:"...":true}]]</span>
					<span class="count">({$category.count})</span>
				</a>
			</li>
		{/if}
	{foreachelse}
		<li class="browse-by__list-empty">[[Sorry, we don't currently have any jobs for this search. Please try another search.]]</li>
	{/foreach}
</ul>