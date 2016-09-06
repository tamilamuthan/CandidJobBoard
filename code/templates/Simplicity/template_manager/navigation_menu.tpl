<ul class="nav navbar-nav navbar-left">
    {foreach from=$menuItems item='menuItem'}
        <li class="navbar__item {if $url == $menuItem.url}active{/if}{if $smarty.request.listing_type_id == 'Job' && $menuItem.url == '/add-listing/?listing_type_id=Job'}active{/if}">
            <a class="navbar__link" href="{$menuItem.fixed_url|escape}">[[{$menuItem.name}]]</a>
        </li>
    {/foreach}
</ul>
