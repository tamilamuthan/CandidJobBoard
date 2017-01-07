{title} {$listing.Title} {/title}
{keywords} {$listing.Title} {/keywords}
{description} {$listing.Skills|strip_tags|truncate:165} {/description}

{* todo: Показывать алерт если юзер пришел из чекаута *}
{if $smarty.request.isBoughtNow}
	<div class="alert alert-bought-now text-center content-text">
		[[You have successfully posted your resume.]] <br/>
		<a href="{$GLOBALS.site_url}/my-listings/resume/" class="link">[[Edit your resume in "My Account" section]]</a>
		<a href="#" class="alert__close"></a>
	</div>
{/if}

{if $errors}
	<div>
		{foreach from=$errors key=error_code item=error_message}
			<p class="alert alert-danger">
				{if $error_code == 'NO_SUCH_FILE'} [[No such file found in the system]]{/if}
			</p>
		{/foreach}
	</div>
{else}
	<div class="details-header">
		<div class="container">
			<div class="results text-left">
				{if $url == "/my-resume-details/{$listing.id}/"}
					<a href="{$GLOBALS.site_url}/edit-{$listing.type.id}/?listing_id={$listing.id}"
					   class="btn__back">
						[[Back]]
					</a>
                    {javascript}
                        <script type="text/javascript">
                            if (window.history && window.history.pushState) {
                                window.history.pushState('forward', null, '');
                                $(window).on('popstate', function() {
                                    window.location.href = '{$GLOBALS.site_url}/edit-{$listing.type.id}/?listing_id={$listing.id}';
                                });
                            }
                        </script>
                    {/javascript}
				{else}
					<a href="javascript:history.go(-1)"
					   class="btn__back">
						[[Back]]
					</a>
				{/if}
			</div>
			<h1 class="details-header__title ">{$listing.user.FullName}</h1>
			<ul class="listing-item__info clearfix inline-block">
				<li class="listing-item__info--item listing-item__info--item-company">
					{$listing.Title|escape}
				</li>
				{if $listing|location}
					<li class="listing-item__info--item listing-item__info--item-location">
						{$listing|location}
					</li>
				{/if}
				<li class="listing-item__info--item listing-item__info--item-date">
					{$listing.activation_date|date}
				</li>
			</ul>
			<div class="job-type">
				{display property='EmploymentType' assign='EmploymentType'}
				{if $EmploymentType}
					<span class="job-type__value">{$EmploymentType}</span>
				{/if}
				{display property='JobCategory' template="multilist_job_category.tpl"}
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row details-body details-body__resume">
			<div class="sidebar profile col-xs-10 col-xs-offset-1 col-sm-offset-0">
				<div class="sidebar__content">
					{if $listing.Photo.file_url}
						<div class="job-seeker__image">
							<div class="text-center profile__image">
								<img class="profile__img" src="{$listing.Photo.file_url}" alt="{$listing.user.FullName|escape}">
							</div>
						</div>
					{/if}
					<ul class="profile__info-list">
						{if $listing.Phone}
							<li class="profile__info-list__item profile__info-list__item-phone">
								<a href="tel:{$listing.Phone}">{$listing.Phone}</a>
							</li>
						{/if}
						<li class="profile__info-list__item profile__info-list__item-email">
							<a href="mailto:{$listing.user.username}">{$listing.user.username}</a>
						</li>
						{if $listing.Resume.file_url}
							<li class="profile__info-list__item profile__info-list__item-resume">
								<a href="?filename={$listing.Resume.saved_file_name|escape:'url'}">[[My Resume]]</a>
							</li>
						{/if}
					    <li class="profile__info-list__item profile__info-list__item-resume">
							{$listing|badges}
                       </li>
					</ul>
				</div>
				{if 'banner_right_side'|banner}
					<div class="banner banner--right">
						{'banner_right_side'|banner}
					</div>
				{/if}
			</div>
			<div class="pull-left details-body__left">
				{if $listing.Skills}
					<h3 class="details-body__title">[[{$form_fields.Skills.caption}]]</h3>
					<div class="details-body__content content-text">{display property='Skills'}</div>
				{/if}
				{if $listing.WorkExperience}
					<h3 class="details-body__title">[[{$form_fields.WorkExperience.caption}]]</h3>
					<div class="details-body__content content-text">{display property='WorkExperience'}</div>
				{/if}
				{if $listing.Education}
					<h3 class="details-body__title">[[{$form_fields.Education.caption}]]</h3>
					<div class="details-body__content content-text">{display property='Education'}</div>
				{/if}
				{foreach from=$form_fields item=list_value}
					{if !$list_value.is_reserved}
						{if !$list_value.id != 'Location' && {display property=$list_value.id}}
							<h3 class="details-body__title">[[{$list_value.caption}]]</h3>
							<div class="details-body__content content-text">{display property=$list_value.id}</div>
						{/if}
					{/if}
				{/foreach}
			</div>
			{if $GLOBALS.user_page_uri == '/resume-preview/'}
				<div class="form-group job-preview__btns col-xs-12">
					<form action="{$referer}" method="post">
						<input type="hidden" name="from-preview" value="1" />
						<input type="submit" name="edit_temp_listing" value="[[Edit]]" class="btn btn__orange btn__bold" id="listing-preview" />
						{if $contract_id == 0 && !$checkouted}
							<input type="hidden" name="proceed_to_checkout" />
							<input type="submit" name="action_add" value="[[Post]]" class="btn btn__orange btn__bold" />
						{else}
							<input type="submit" name="action_add" value="[[Post]]" class="btn btn__orange btn__bold" />
						{/if}
					</form>
				</div>
			{/if}
		</div>
	</div>
{/if}
{javascript}
	<script type="text/javascript">
		$('.alert__close').on('click', function(e) {
			e.preventDefault();
			$(this).closest('.alert').hide();
		});
	</script>
{/javascript}
