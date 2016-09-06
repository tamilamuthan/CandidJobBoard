<div class="container container-fluid quick-search">
	<div class="quick-search__wrapper well">
		<form action="{$GLOBALS.site_url}/jobs/" class="form-inline row">
			<input type="hidden" name="listing_type[equal]" value="Job" />
			{if $searchId}
				<input type="hidden" name="searchId" value="{$searchId|escape}" />
			{/if}
			<input type="hidden" name="action" value="search" />
			<div class="form-group form-group__input {if !$GLOBALS.settings.search_by_location}full{/if}">
				{search property='keywords'}
			</div>
			{if $GLOBALS.settings.search_by_location}
				<div class="form-group form-group__input">
					{search property='GooglePlace' template='google_place.tpl'}
				</div>
			{/if}
			{foreach from=$browse_request_data item='browse_item'}
				{if $browse_item@first}
					{foreach from=$browse_item item='criteria'}
						{if is_array($criteria)}
							<input type="hidden" name="{$browse_item@key}[{$criteria@key}][]" value="{$criteria[0]|escape}">
						{else}
							<input type="hidden" name="{$browse_item@key}[{$criteria@key}]" value="{$criteria|escape}">
						{/if}
					{/foreach}
				{/if}
			{/foreach}
			<div class="form-group form-group__btn">
				<button type="submit" class="quick-search__find btn btn__orange btn__bold ">[[Find Jobs]]</button>
			</div>
		</form>
	</div>
</div>