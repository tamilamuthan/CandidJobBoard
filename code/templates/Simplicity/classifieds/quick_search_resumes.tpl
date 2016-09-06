<div class="container container-fluid quick-search">
    <div class="quick-search__wrapper well">
        <form action="{$GLOBALS.site_url}/resumes/" class="form-inline row">
            <input type="hidden" name="action" value="search" />
            <input type="hidden" name="listing_type[equal]" value="Resume" />
            <div class="form-group form-group__input">
                {search property=keywords}
            </div>
            {if $GLOBALS.settings.search_by_location}
                <div class="form-group form-group__input">
                    {search property='GooglePlace' template='google_place.tpl'}
                </div>
            {/if}
            <div class="form-group form-group__btn">
                <button type="submit" class="quick-search__find btn btn__orange btn__bold">[[Find Resumes]]</button>
            </div>
        </form>
    </div>
</div>