<article class="media well listing-item listing-item__jobs {if $listing.featured}listing-item__featured{/if} {if not $listing.user.Logo.file_url}listing-item__no-logo{/if}">
    {if $listing.user.Logo.file_url}
        <div class="media-left listing-item__logo">
            <a href="{$GLOBALS.site_url}{$listing|listing_url}">
                <img class="media-object profile__img-company" src="{$listing.user.Logo.file_url}" alt="{$listing.user.CompanyName|escape:'html'}">
            </a>
        </div>
    {/if}
    <div class="media-body">
        <div class="media-heading listing-item__title">
            <a href="{$GLOBALS.site_url}{$listing|listing_url}" class="link">{$listing.Title|escape}</a>
        </div>
        <div class="listing-item__info clearfix">
            <span class="listing-item__info--item listing-item__info--item-company">
                {$listing.user.CompanyName|escape:'html'}
            </span>
            {if $listing|location}
                <span class="listing-item__info--item listing-item__info--item-location">
                    {$listing|location}
                </span>
            {/if}
        </div>
        <div class="listing-item__desc hidden-sm hidden-xs">
            {$listing.JobDescription|strip_tags:false}
        </div>
    </div>
    <div class="media-right text-right">
        <div class="listing-item__date">
            {$listing.activation_date|date}
        </div>
        {foreach from=$listing.EmploymentType item=list_value name="multifor"}
            {if $smarty.foreach.multifor.first && $list_value}
                <span class="listing-item__employment-type">{tr}{$list_value}{/tr}</span>
            {/if}
        {/foreach}
    </div>
    <div class="listing-item__desc visible-sm visible-xs">
        {$listing.JobDescription|strip_tags:false}
    </div>
</article>