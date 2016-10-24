{if $listingTypeName == 'Job'}
	{title}[[Post a Job]]{/title}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Post a Job]]</h1>
{elseif $listingTypeName == 'Opportunity'}
	{title}[[Post an Opportunity]]{/title}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Post an Opportunity]]</h1>
{elseif $listingTypeName == 'Idea'}
	{title}[[Create New Idea]]{/title}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Create New Idea]]</h1>
{else}
	{title}[[Create New Resume]]{/title}
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Create New Resume]]</h1>
{/if}
<div class="static-pages content-text">
	{if $error eq 'LISTINGS_NUMBER_LIMIT_EXCEEDED'}
		[[You've reached the limit of number of listings allowed by your product]]
		{if $listingTypeName == 'Job'}
			<p><a href="{$GLOBALS.site_url}/employer-products/?postingProductsOnly=1">[[Please choose new product]]</a></p>
    	{elseif $listingTypeName == 'Opportunity'}
			<p><a href="{$GLOBALS.site_url}/investor-products/?postingProductsOnly=1">[[Please choose new product]]</a></p>
    	{elseif $listingTypeName == 'Idea'}
			<p><a href="{$GLOBALS.site_url}/entrepreneur-products/?postingProductsOnly=1">[[Please choose new product]]</a></p>
		{else}
			<p><a href="{$GLOBALS.site_url}/jobseeker-products/?postingProductsOnly=1">[[Please choose new product]]</a></p>
		{/if}
	{elseif $error eq 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING'}
		{if $GLOBALS.current_user.group.id == 'Employer'}
			{*TODO: Сделать через PHP *}
			<script>
				window.location.replace("{$GLOBALS.site_url}/employer-products/");
			</script>
    	{elseif $GLOBALS.current_user.group.id == 'Investor'}
			<script>
				window.location.replace("{$GLOBALS.site_url}/investor-products/");
			</script>
        {elseif $GLOBALS.current_user.group.id == 'Entrepreneur'}
			<script>
				window.location.replace("{$GLOBALS.site_url}/entrepreneur-products/");
			</script>
		{else}
			[[You do not have permissions to post {$listingTypeName}s. Please purchase a relevant product.]]
		{/if}
	{elseif $error eq 'NOT_ALLOW_TO_POST_LISTING'}
		<div class="alert alert-info">[[The product you've purchased does not allow to post listings. Please purchase another product]]</div>
	{elseif $error eq 'NOT_LOGGED_IN'}
		{module name="users" function="login"}
	{/if}
</div>