{assign var="site_name" value=$GLOBALS.settings.site_title}
{description}[[jobs from $site_name]]{/description}

{capture name=search}
	{module name='classifieds' function='search_form' form_template='quick_search.tpl' listing_type_id='Job' browse_request_data=$browse_request_data searchId=$searchId}
{/capture}

{if $GLOBALS.user_page_uri == '/company/'}
	{assign var='refineSearch' value=false}
{/if}
{if $ERRORS}
	{include file="error.tpl"}
{else}
	{if $is_company_profile_page}
		{include file="search_results_profile.tpl"}
	{else}
		<div class="search-header hidden-xs-480"></div>
		<div class="quick-search__inner-pages {if !$user_page_uri}hidden-xs-480{/if}">
			{$smarty.capture.search}
		</div>
		<div class="container">
			<div class="details-body details-body__search {if ($refineSearch && $currentSearch) || ($refineSearch && $refineFields) || $user_page_uri}row{else}no-refine-search{/if}{if 'banner_right_side'|banner} with-banner{/if}">
				{if $smarty.request.not_found}
					<div class="col-xs-12">
						<div class="alert alert-info text-center">
							[[Sorry, that job is no longer available. Here are some results that may be similar to the job you were looking for.]]
						</div>
					</div>
				{/if}
				<div class="search-results__top search-results__listings clearfix">
					{assign var="jobs_number" value=$listing_search.listings_number}
					{if $user_page_uri}
						{foreach from=$browse_navigation_elements item=element name="nav_elements"}
							<h1 class="search-results__title {if $user_page_uri}browse-by__title {else}col-sm-offset-3 col-xs-offset-0{/if}">
								{if $user_page_uri == '/categories/'}
									{assign var="category_name" value=$element.caption|escape}
									[[$jobs_number $category_name jobs]]
								{else}
									{assign var="location" value=$element.caption|escape|capitalize}
									[[$jobs_number jobs found in $location]]
								{/if}
							</h1>
						{/foreach}
					{else}
						<h1 class="search-results__title {if $user_page_uri}browse-by__title {else}col-sm-offset-3 col-xs-offset-0{/if}">
							[[$jobs_number jobs found]]
						</h1>
					{/if}
					{if $listing_type_id != ''}
						<a class="btn create-job-alert btn__blue"
						   data-toggle="modal"
						   data-target="#apply-modal"
						   data-href='{$GLOBALS.site_url}/guest-alerts/create/?searchId={$searchId}'
						   data-title='[[Create Job Alert]]'>
							<?xml version="1.0" encoding="UTF-8" standalone="no"?>
							<svg style="border-radius: 2px" width="22px" height="18px" viewBox="0 0 22 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
								<!-- Generator: Sketch 3.4.4 (17249) - http://www.bohemiancoding.com/sketch -->
								<title>Rectangle 438 + Path 118</title>
								<desc>Created with Sketch.</desc>
								<defs></defs>
								<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
									<g id="search-results" sketch:type="MSArtboardGroup" transform="translate(-1069.000000, -337.000000)" stroke="#d40c05">
										<g id="Rectangle-2-Copy-+-Get-free-quot" sketch:type="MSLayerGroup" transform="translate(1051.000000, 326.000000)">
											<g id="Create-Job-Alert-+-Rectangle-438-+-Path-118" transform="translate(18.000000, 9.000000)" sketch:type="MSShapeGroup">
												<g id="Rectangle-438-+-Path-118" transform="translate(0.000000, 2.000000)">
													<rect id="Rectangle-438" fill-opacity="0.2" fill="#f8f8f8" x="0" y="0" width="22" height="18" rx="2"></rect>
													<path d="M1,1 L11,7.5 L21,1" id="Path-118"></path>
												</g>
											</g>
										</g>
									</g>
								</g>
							</svg>
							[[Email me jobs like this]]
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
						{include file="search_results_jobs_listings.tpl"}
						<button type="button" class="load-more btn btn__white" data-page="2" data-backfilling="{if count($listings) < $listing_search.listings_per_page && $GLOBALS.user_page_uri ne '/company/'}true{else}false{/if}" data-backfilling-page="1">
							[[Load more]]
						</button>
					{else}
						<div class="alert alert-danger no-listings-found hidden">
							[[Sorry, we don't currently have any jobs for this search. Please try another search.]]
						</div>
						<button type="button" class="load-more btn btn__white" data-page="2" data-backfilling="{if count($listings) < $listing_search.listings_per_page && $GLOBALS.user_page_uri ne '/company/'}true{else}false{/if}" data-backfilling-page="1">
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

{if $GLOBALS.user_page_uri == '/jobs/' || $browse_request_data}
	{javascript}
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$GLOBALS.settings.google_api_key}&signed_in=true&libraries=places&callback=initService&language={$GLOBALS.current_language}" async defer></script>
	{/javascript}
{/if}

{javascript}
	<script>
		var listingPerPage = {$listing_search.listings_per_page};
		var listingNumber = '{$jobs_number}';
		$(document).ready(function() {
			// refine search
			var ajaxUrl = "{$GLOBALS.site_url}/ajax/";
			var ajaxParams = {
				'action': 'get_refine_search_block',
				'listing_type[equal]': 'Job',
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
			if (self.data('backfilling')) {
				var page = self.data('backfilling-page');
				self.data('backfilling-page', parseInt(page) + 1);

				// request to listings providers
				var ajaxUrl = "{$GLOBALS.site_url}/ajax/";
				var ajaxParams = {
					'action' : 'request_for_listings',
					'searchId' : '{$searchId}',
					'page' : page
				};

				$.get(ajaxUrl, ajaxParams, function(data) {
					if (data.length > 0) {
						$('.no-listings-found').hide();
					} else {
						self.prop('disabled', true);
						$('.no-listings-found').removeClass('hidden');
					}
					self.before(data);
					if ($('.listing_item__backfilling').length < listingPerPage) {
						self.hide();
					}
					self.removeClass('loading');
				});
				return;
			}

			$.get('?searchId={$searchId}&action=search&page=' + self.data('page'), function(data) {
				var listings = $(data).find('.listing-item');
				self.removeClass('loading');
				if (listings.length) {
					$('.listing-item').last().after(listings);
					self.data('page', parseInt(self.data('page')) + 1);
				}
				if (listings.length !== listingPerPage) {
					if ('{$GLOBALS.user_page_uri ne '/company/'}') {
						self.data('backfilling', true);
						$('.load-more').click();
					} else {
						self.hide();
					}
				}
			});
		});
	</script>
{/javascript}