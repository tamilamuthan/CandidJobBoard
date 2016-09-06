{if $listings}
	<section class="main-sections listing__featured">
		<div class="container container-fluid listing">
			{if $listings}
				<h4 class="listing__title {if 'banner_right_side'|banner}with-banner{/if}">
					[[Featured Jobs]]
				</h4>
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
			{/if}
		</div>
	</section>
{/if}