{if $profiles}
	<section class="main-sections main-sections__featured-companies">
		<div class="container container-fluid featured-companies">
			<h4 class="featured-companies__title text-center">[[Featured Companies]]</h4>
			<ul class="featured-companies__slider featured-companies__slider__js">
				{foreach from=$profiles item=profile name=profile_block}
					<li class="featured-company">
						<a href="{$GLOBALS.site_url}/company/{$profile.id}/{$profile.CompanyName|pretty_url}/" title="{$profile.CompanyName|escape}">
							<div class="panel panel-default featured-company__panel">
								<div class="panel-body featured-company__panel-body">
									{if $profile.Logo.thumb_file_url}
										<img class="featured-company__image" src="{$profile.Logo.thumb_file_url}" alt="{$profile.WebSite}" title="{$profile.CompanyName|truncate:23}"/>
									{else}
										<div class="company__no-image" title="{$profile.CompanyName}"></div>
									{/if}
								</div>
								<div class="panel-footer featured-company__panel-footer">
									<div class="featured-companies__name">
										<span>{$profile.CompanyName}</span>
									</div>
									<div class="featured-companies__jobs">
										{assign var="jobs_number" value=$profile.countListings}
										[[$jobs_number job(s)]]
									</div>
								</div>
							</div>
						</a>
					</li>
				{/foreach}
			</ul>
			<span class="featured-companies__slider--arrows featured-companies__slider--prev"></span>
			<span class="featured-companies__slider--arrows featured-companies__slider--next"></span>
		</div>
	</section>

	{javascript}
		<script type="text/javascript">
			var oneSlide = {
				auto: true,
				infiniteLoop: true,
				minSlides: 1,
				maxSlides: 1,
				slideWidth: 253,
				slideMargin: 10,
				moveSlides: 1,
				pager: false,
				useCSS: true,
				responsive: true,
				nextSelector: '.featured-companies__slider--next',
				prevSelector: '.featured-companies__slider--prev',
				nextText: '',
				prevText: ''
			};
			var twoSlides = {
				auto: true,
				infiniteLoop: true,
				minSlides: 1,
				maxSlides: 2,
				slideWidth: 253,
				slideMargin: 10,
				moveSlides: 1,
				pager: false,
				useCSS: true,
				responsive: true,
				nextSelector: '.featured-companies__slider--next',
				prevSelector: '.featured-companies__slider--prev',
				nextText: '',
				prevText: ''
			};
			var threeSlides = {
				auto: true,
				infiniteLoop: true,
				minSlides: 1,
				maxSlides: 3,
				slideWidth: 253,
				slideMargin: 10,
				moveSlides: 1,
				pager: false,
				useCSS: true,
				responsive: true,
				nextSelector: '.featured-companies__slider--next',
				prevSelector: '.featured-companies__slider--prev',
				nextText: '',
				prevText: ''
			};
			var slider;
			if ($(document).width() > 680 && $(document).width() <= 992) {
				slider = $('.featured-companies__slider__js').bxSlider(twoSlides);
			}
			else if ($(document).width() <= 680) {
				slider = $('.featured-companies__slider__js').bxSlider(oneSlide);
			}
			else {
				slider = $('.featured-companies__slider__js').bxSlider(threeSlides);
			}
			$(window).on('resize orientationchange', function() {
				if ($(document).width() > 680 && $(document).width() <= 992) {
					slider.destroySlider();
					slider.reloadSlider(twoSlides);
				}
				if($(document).width() <= 680) {
					slider.destroySlider();
					slider.reloadSlider(oneSlide);
				}
				if ($(document).width() > 992){
					slider.destroySlider();
					slider.reloadSlider(threeSlides);
				}
			});
		</script>
	{/javascript}
{/if}