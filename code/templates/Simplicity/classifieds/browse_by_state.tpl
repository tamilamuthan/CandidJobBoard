{assign var=i value=1}
<ul class="list-unstyled browse-by__list">
	{foreach from=$browseItems key=itemName item=count}
		{if $count > 0 && $i <= $recordsNumToDisplay}
			{assign var=i value=$i+1}
			<li>
				<a href="{$GLOBALS.site_url}/states/jobs-in-{$itemName|pretty_url:false|escape:'url'}/">
					<span class="browse-by__item">[[{$itemName|escape|truncate:28:"...":true}]]</span>
					<span class="count">({$count})</span>
				</a>
			</li>
		{/if}
	{foreachelse}
		<li class="browse-by__list-empty">[[Sorry, we don't currently have any jobs for this search. Please try another search.]]</li>
	{/foreach}
</ul>
