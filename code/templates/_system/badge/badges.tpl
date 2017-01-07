<h1>[[Badges]]</h1>

<div id="badgesList" {if $GLOBALS.user_page_uri == "/employer-badges/" ||  $GLOBALS.user_page_uri == "/jobseeker-badges/"}class="productsList-nl"{/if}>
{if count($badges)>0}
    {foreach from=$badges item=badge key=id name=pr}
        <div class="badges {cycle values = 'evenrow,oddrow' advance=true}">
            <div class="badgeInfo">
                <h3>[[{$badge.name}]]</h3>
                <p>[[{$badge.short_description}]]</p>
                <a href="{$GLOBALS.site_url}/badge-details/?badge_sid={$badge.sid}">[[Badge details]] &#187;</a>
            </div>

            <div class="badgeLinks">
                <p class="badgesPrice">
					{capture assign="badgePrice"}{tr type="float"}{$badge.price}{/tr}{/capture}
                    {if $badge.pricing_type == 'volume_based'}
						[[Starting at]] <span class="strong">{currencyFormat amount=$badgePrice}</span>
                    {elseif $badge.period}
                        {if $badge.period_name == 'unlimited'}
                            [[Never Expire]]
                        {else}
							<span class="strong">{currencyFormat amount=$badgePrice}</span> [[per]] <span class="strong">{$badge.period} {if $badge.period > 1 }[[{$badge.period_name|capitalize}s]]{else}[[{$badge.period_name|capitalize}]]{/if}</span>
                        {/if}
                    {else}
						<span class="strong">{currencyFormat amount=$badgePrice}</span>
                    {/if}
                </p>
				<input type="button" value="[[Buy]]" class="button" onclick="location.href='{$GLOBALS.site_url}/badge-details/?badge_sid={$badge.sid}'" />
				{if $GLOBALS.settings.allow_to_post_before_checkout == 1 && ($badge.badge_type == 'post_listings' || $badge.badge_type == 'mixed_badge')}
					<form id="add-listing_{$badge.sid}" method="post" action="{$GLOBALS.site_url}/add-listing/?listing_type_id={$badge.listing_type_id}">
						<input type="hidden" name="badgeSID" value="{$badge.sid}" />
						<input type="hidden" name="listing_type_id" value="{$badge.listing_type_id}" />
						<input type="submit" name="proceed_to_posting" value="[[Proceed to Posting]]" class="button" />
					</form>
				{/if}
            </div>
        </div>
    {/foreach}
{else}
    <p>[[There are no Badges]]</p>
{/if}
</div>
