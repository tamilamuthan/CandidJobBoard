{include file="error.tpl"}
{if empty($listings)}
	<div class="container container--small  ">
		{include file="../miscellaneous/404.tpl"}
	</div>
{elseif $listing_type == 'Resume'}
	{include file="search_results_resumes.tpl"}
{else}
	{include file="search_results_jobs.tpl"}
{/if}

{assign var="site_name" value=$GLOBALS.settings.site_title}
{*must be after search results tags*}
{foreach from=$browse_navigation_elements item=element name="nav_elements"}
	{if $user_page_uri == '/categories/'}
		{assign var="category_name" value=$element.caption|capitalize:true}
		{title}[[$category_name jobs]]{/title}
		{description}[[$category_name jobs from $site_name]]{/description}
	{else}
		{assign var="location" value=$element.caption|capitalize:true}
		{title}[[Jobs in $location]]{/title}
		{description}[[Jobs in $location from $site_name]]{/description}
	{/if}
{/foreach}