{if $listings}
	<section class="main-sections main-sections__listing__latest listing__latest">
		<div class="container container-fluid listing">
			<h4 class="listing__title {if 'banner_right_side'|banner}with-banner{/if}">[[Latest Jobs]]</h4>
			<div class="listing-item__list {if 'banner_right_side'|banner}with-banner{/if}">
				{foreach from=$listings item=listing}
					{include file="listing_item.tpl" listing=$listing}
				{/foreach}
			</div>
			{if 'banner_right_side'|banner}
				<div class="banner banner--right">
					{'banner_right_side'|banner}
				</div>
			{/if}
		</div>
	</section>
	<div class="view-all text-center {if 'banner_right_side'|banner}with-banner{/if}">
		<a href="{$GLOBALS.site_url}/jobs/" class="btn view-all__btn btn__white">[[View all jobs]]</a>
	</div>
{/if}
