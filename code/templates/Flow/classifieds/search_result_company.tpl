<div class="container {if 'banner_right_side'|banner}with-banner__companies{/if}">
	<div class="row details-body details-body__search">
		{if $ERRORS}
			{include file="error.tpl"}
		{else}
			<div class="search-results__top search-results__top-company clearfix">
				<h1 class="title__primary title__centered">
					[[$companies_number Companies]]
				</h1>
			</div>
			<div class="search-results search-results__companies featured-companies text-center clearfix">
				{foreach from=$found_users_sids item=user_sid name=users_block}
					{display property='State.Code' object_sid=$user_sid parent=Location assign='State'}
					{display property='City' object_sid=$user_sid parent=Location assign='City'}
					<div class="featured-company" aria-hidden="false">
						{display property='CompanyName' object_sid=$user_sid assign=CompanyName}
						<a href="{$GLOBALS.site_url}/company/{$user_sid}/{$CompanyName|unescape|pretty_url}/" title="{$CompanyName}">
							<div class="panel panel-default featured-company__panel">
								<div class="panel-body featured-company__panel-body text-center">
									{if {display property='Logo' object_sid=$user_sid}}
										{display property='Logo' object_sid=$user_sid}
									{else}
										<div class="company__no-image"></div>
									{/if}
								</div>
								<div class="panel-footer featured-company__panel-footer">
									<div class="featured-companies__name">
										<span>{display property='CompanyName' object_sid=$user_sid}</span>
									</div>
									<div class="featured-companies__jobs">
										{display property='countListings' object_sid=$user_sid assign="jobs_number"}
										[[$jobs_number job(s)]]
									</div>
								</div>
							</div>
						</a>
					</div>
				{/foreach}
			</div>
			<button type="button" class="load-more load-more__companies btn btn__white {if $companies_number > {$companies_per_page}}show{else}hidden{/if}" data-page="2">
				[[Load more]]
			</button>
		{/if}
	</div>
	{if 'banner_right_side'|banner}
		<div class="banner banner--right banner--companies">
			{'banner_right_side'|banner}
		</div>
	{/if}
</div>

{javascript}
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$GLOBALS.settings.google_api_key}&signed_in=true&libraries=places&callback=initService&language={$GLOBALS.current_language}" async defer></script>
	<script>
		var listingPerPage = {$companies_per_page};
		$('.load-more').click(function() {
			var self = $(this);
			self.addClass('loading');
			$.get('?searchId={$searchId|escape}&action=search&page=' + self.data('page'), function(data) {
				self.removeClass('loading');
				var listings = $(data).find('.featured-company');
				if (listings.length) {
					$('.featured-company').last().after(listings);
					self.data('page', parseInt(self.data('page')) + 1);
				}
				if (listings.length !== listingPerPage) {
					self.removeClass('show').addClass('hidden');
				}
			});
		});
	</script>
{/javascript}