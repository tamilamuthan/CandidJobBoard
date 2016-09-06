{if $userInfo.Logo.file_url || $userInfo.CompanyDescription}
	<div class="sidebar col-xs-10 profile col-xs-offset-1 col-sm-offset-0 pull-right">
		<div class="sidebar__content">
			{if $userInfo.Logo.file_url}
				<div class="text-center profile__image">
					<img class="profile__img profile__img-company" src="{$userInfo.Logo.file_url}" alt="" />
				</div>
			{/if}
			<div class="profile__info">
				<div class="profile__info__description content-text">{$userInfo.CompanyDescription}</div>
			</div>
		</div>
		{if 'banner_right_side'|banner}
			<div class="banner banner--right">
				{'banner_right_side'|banner}
			</div>
		{/if}
	</div>
{else}
	{if 'banner_right_side'|banner}
		<div class="sidebar col-xs-10 profile col-xs-offset-1 col-sm-offset-0 pull-right with-banner">
			<div class="banner banner--right banner--company-profile">
				{'banner_right_side'|banner}
			</div>
		</div>
	{/if}
{/if}