<div class="quick-search__inner-pages">
	<div class="container container-fluid quick-search">
		<div class="quick-search__wrapper well">
			<form action="{$GLOBALS.site_url}/companies/" class="form-inline row">
				<input type="hidden" name="action" value="search" />
				<div class="form-group form-group__input {if !$GLOBALS.settings.search_by_location}full{/if}">
					{search property=CompanyName  template="string.like.tpl"}
				</div>
				{if $GLOBALS.settings.search_by_location}
					<div class="form-group form-group__input">
						{search property='GooglePlace' template='google_place.tpl'}
					</div>
				{/if}
				<div class="form-group form-group__btn">
					<button type="submit" class="quick-search__find btn btn__orange btn__bold">[[Find Companies]]</button>
				</div>
			</form>
		</div>
	</div>
</div>
