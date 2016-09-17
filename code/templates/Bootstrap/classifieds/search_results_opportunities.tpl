{assign var="site_name" value=$GLOBALS.settings.site_title}
{description}[[opportunities from $site_name]]{/description}

{capture name=search}
	{module name='classifieds' function='search_form' form_template='quick_search_opportunities.tpl' listing_type_id='Opportunity' browse_request_data=$browse_request_data searchId=$searchId}
{/capture}

{if $GLOBALS.user_page_uri == '/company/'}
	{assign var='refineSearch' value=false}
{/if}
{if $ERRORS}
	{include file="error.tpl"}
{else}
	{if $is_company_profile_page}
		{include file="search_results_opportunities_profile.tpl"}
	{else}
		<div class="search-header {if !$user_page_uri}hidden-xs-480{/if}"></div>
		<div class="quick-search__inner-pages {if !$user_page_uri}hidden-xs-480{/if}">
			{$smarty.capture.search}
		</div>
		<div class="container">
			<div class="details-body details-body__search {if ($refineSearch && $currentSearch) || ($refineSearch && $refineFields) || $user_page_uri}row{else}no-refine-search{/if}{if 'banner_right_side'|banner} with-banner{/if}">
				{if $smarty.request.not_found}
					<div class="col-xs-12">
						<div class="alert alert-info text-center">
							[[Sorry, that opportunity is no longer available. Here are some results that may be similar to the opportunity you were looking for.]]
						</div>
					</div>
				{/if}
				<div class="search-results__top clearfix">
					{assign var="opps_number" value=$listing_search.listings_number}
					{if $user_page_uri}
						{foreach from=$browse_navigation_elements item=element name="nav_elements"}
							<h1 class="search-results__title {if $user_page_uri}browse-by__title {else}col-sm-offset-3 col-xs-offset-0{/if}">
								{if $user_page_uri == '/categories/'}
									{assign var="category_name" value=$element.caption|escape}
									[[$opps_number $category_name opportunities]]
								{else}
									{assign var="location" value=$element.caption|escape}
									[[$opps_number opportunities found in $location]]
								{/if}
							</h1>
						{/foreach}
					{else}
						<h1 class="search-results__title {if $user_page_uri}browse-by__title {else}col-sm-offset-3 col-xs-offset-0{/if}">
							[[$opps_number opportunities found]]
						</h1>
					{/if}
					{if $listing_type_id != ''}
						<a class="btn create-job-alert btn__blue"
						   data-toggle="modal"
						   data-target="#apply-modal"
						   data-href='{$GLOBALS.site_url}/guest-alerts/create/?searchId={$searchId}'
						   data-title='[[Create Opportunity Alert]]'>
							[[Email me opportunities like this]]
						</a>
					{/if}
				</div>
				{if ($refineSearch && $currentSearch) || ($refineSearch && $refineFields)}
					<div id="ajax-refine-search" class="col-sm-3 col-xs-12 refine-search">
						<a class="toggle--refine-search visible-xs" role="button" data-toggle="collapse" href="#" aria-expanded="true">
							[[Refine Search]] [[{$refineField.caption}]]
						</a>
						<div class="refine-search__wrapper loading">
							<div class="quick-search__inner-pages visible-xs-480">
								{$smarty.capture.search}
							</div>
							{include file="search_results_refine_block.tpl"}
						</div>
					</div>
				{/if}
				<div class="search-results {if ($refineSearch && $currentSearch) || ($refineSearch && $refineFields)}col-xs-12 col-sm-9{/if}{if $user_page_uri} search-results__small{/if}">
					{if $listings}
						{include file="search_results_opportunities_listings.tpl"}
						<button type="button" class="load-more btn btn__white" data-page="2" data-backfilling="false" data-backfilling-page="1">
							[[Load more]]
						</button>
					{else}
						<div class="alert alert-danger no-listings-found hidden">
							[[Sorry, we don't currently have any opportunities for this search. Please try another search.]]
						</div>
						<button type="button" class="load-more btn btn__white" data-page="2" data-backfilling="false" data-backfilling-page="1">
							[[Load more]]
						</button>
					{/if}
				</div>
			</div>
			{if 'banner_right_side'|banner}
				<div class="banner banner--right banner--search">
					{'banner_right_side'|banner}
				</div>
			{/if}
		</div>
	{/if}
{/if}

{if $GLOBALS.user_page_uri == '/opportunities/' || $browse_request_data}
	{javascript}
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$GLOBALS.settings.google_api_key}&signed_in=true&libraries=places&callback=initService&language={$GLOBALS.current_language}" async defer></script>
	{/javascript}
{/if}

{javascript}
	<script>
		var listingPerPage = {$listing_search.listings_per_page};
		var listingNumber = '{$opps_number}';
		$(document).ready(function() {
			// refine search
			var ajaxUrl = "{$GLOBALS.site_url}/ajax/";
			var ajaxParams = {
				'action': 'get_refine_search_block',
				'listing_type[equal]': 'Opportunity',
				'searchId': '{$searchId}',
				'showRefineFields': {$listing_search.listings_number} > 0
			};

			$.get(ajaxUrl, ajaxParams, function (data) {
				if (data.length > 0) {
					$('.current-search').remove();
					$('#ajax-refine-search').find('.refine-search__wrapper .refine-search__block').remove();
					$('#ajax-refine-search').find('.refine-search__wrapper').append(data);
					$('.refine-search__wrapper').removeClass('loading');

					$('.refine-search__item-radius.active').removeClass('active');
					var miles = $('.form-group__input input[type="hidden"]').val();
					$('#refine-block-radius .dropdown-toggle').text(miles + ' [[{$GLOBALS.settings.radius_search_unit}]]');
				}
			});

			if (listingNumber != '' && listingNumber < listingPerPage) {
				$('.load-more').trigger('click');
			}
		});


		$('.load-more').click(function() {
			var self = $(this);
			self.addClass('loading');

			$.get('?searchId={$searchId}&action=search&page=' + self.data('page'), function(data) {
				var listings = $(data).find('.listing-item');
				self.removeClass('loading');
				if (listings.length) {
					$('.listing-item').last().after(listings);
					self.data('page', parseInt(self.data('page')) + 1);
				}
				if (listings.length !== listingPerPage) {
						self.hide();
				}
			});
		});
	</script>
{/javascript}