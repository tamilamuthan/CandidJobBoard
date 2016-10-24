{assign var="company_name" value=$userInfo.CompanyName}
{title}[[Opportunities at $company_name]]{/title}
{description}[[Opportunities at $company_name on $site_name]]{/description}
<div class="details-header">
    <div class="container">
        <div class="results">
            <a href="javascript:history.go(-1)"
               class="btn__back">
                [[Back]]
            </a>
        </div>
        <h1 class="details-header__title">{$userInfo.CompanyName}</h1>
        <ul class="listing-item__info">
            {if $userInfo|location}
                <li class="listing-item__info--item listing-item__info--item-location">
                    {$userInfo|location}
                </li>
            {/if}
            {if $userInfo.WebSite}
                <li class="listing-item__info--item listing-item__info--item-website">
                    <a href="{$userInfo.WebSite}" target="_blank">
                        {$userInfo.WebSite}
                    </a>
                </li>
            {/if}
        </ul>
    </div>
</div>
{javascript}
    <script>
        $(document).on('ready', function() {
            var website = $('.listing-item__info--item-website a');
            var href = website.attr('href');
            if (href && !href.match(/^http([s]?):\/\/.*/)) {
                website.attr('href', 'http://' + href);
            }
        });
    </script>
{/javascript}