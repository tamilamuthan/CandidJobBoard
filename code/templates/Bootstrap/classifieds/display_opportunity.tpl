{title} {$listing.Title} {/title}
{keywords} {$listing.Title} {/keywords}
{description} {$listing.JobDescription|strip_tags|truncate:165} {/description}
{head}
	{module name="miscellaneous" function="opengraph_meta" listing_id=$listing.id}
{/head}

{* todo: Показывать алерт если юзер пришел из чекаута *}
{if $smarty.request.isBoughtNow}
	<div class="alert alert-bought-now text-center content-text">
		[[You have successfully posted your opportunity.]] <br/>
		<a href="{$GLOBALS.site_url}/my-listings/opportunity/" class="link">[[View your opportunity stats in "My Account" section]]
		<a href="#" class="alert__close"></a>
	</div>
{/if}

<div class="listing-results">
	<div class="details-header">
		<div class="container">
			<div class="results text-left">
				{if $url == "/my-opportunity-details/{$listing.id}/"}
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
			<h1 class="details-header__title ">{$listing.Title|escape}</h1>
			<ul class="listing-item__info clearfix inline-block">
				<li class="listing-item__info--item listing-item__info--item-company">
					{$listing.user.CompanyName|escape}
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
				{display property='OpportunityType' assign='OpportunityType'}
				{if $OpportunityType}
					<span class="job-type__value">{$OpportunityType}</span>
				{/if}
				{display property='OpportunityCategory' template="multilist_job_category.tpl"}
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row details-body">
			<div class="pull-left details-body__left">
				<h3 class="details-body__title">[[{$form_fields.JobDescription.caption}]]</h3>
				<div class="details-body__content content-text">{display property='JobDescription'}</div>
				{foreach from=$form_fields item=list_value}
					{if !$list_value.is_reserved}
						{if {display property=$list_value.id}}
							<h3 class="details-body__title">{$list_value.caption}</h3>
							<div class="details-body__content content-text">{display property=$list_value.id}</div>
						{/if}
					{/if}
				{/foreach}
			</div>
			<div class="sidebar sidebar-job profile col-xs-10 col-xs-offset-1 col-sm-offset-0">
				<div class="sidebar__content">
					{if $listing.user.Logo.file_url}
						<div class="text-center profile__image">
							<a href="{if $listing.user.isJobg8}{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.CompanyName|pretty_url}/{else}{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|pretty_url}/{/if}">
								<img class="profile__img profile__img-company" src="{$listing.user.Logo.file_url}" alt="" />
							</a>
						</div>
					{/if}
					<div class="profile__info">
						{assign var="company_name" value=$listing.user.CompanyName|escape}
						<div class="text-center profile__info__name">[[About $company_name]]</div>
						<div class="profile__info__description content-text">{$listing.user.CompanyDescription}</div>
                        <div>
                            <a class="btn__profile" href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|pretty_url}/">[[Company Profile]]</a>
                        </div>
					</div>
				</div>
				{if 'banner_right_side'|banner}
					<div class="banner banner--right">
						{'banner_right_side'|banner}
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>
<div class="details-footer  {if $GLOBALS.user_page_uri == '/opportunity-preview/'}opportunity-preview{/if}">
	<div class="container">
		{if $GLOBALS.user_page_uri == '/opportunity-preview/'}
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
		{else}
			{if !$isApplied}
				{if isset($listing.ApplicationSettings.add_parameter) && $listing.ApplicationSettings.add_parameter == 2}
					{capture assign='applyBtn_onClick'}
						{if $listing.user.isJobg8 && $listing.jobType == 'APPLICATION'}
							{$GLOBALS.site_url}/apply-now-external/?listing_id={$listing.id}
						{else}
							{$listing.ApplicationSettings.value}
						{/if}
					{/capture}
				{else}
					{capture assign='url'}
						{$GLOBALS.site_url}/apply-now/?listing_id={$listing.id}&ajaxRelocate=1
					{/capture}
					{capture assign='modalTitle'}
						{assign var="job_title" value=$listing.Title|escape}
						{assign var="company_name" value=$listing.user.CompanyName|escape}
						[[Apply to $job_title at $company_name]]
					{/capture}
				{/if}
			{/if}
 		{/if}

		<a class="btn details-footer__btn-apply btn__orange btn__bold"
				href="{$applyBtn_onClick}"
				data-toggle="modal"
				data-target="#apply-modal"
		   		data-href="{$url}"
		   		data-applied='{if $isApplied}applied{/if}'
				data-title="{$modalTitle}">
			[[Apply Now]]
		</a>

		<div class="social-share pull-right">
			<span class="social-share__title">
				[[Share this opportunity]]:
			</span>
			{if !$myListing}
				<div class="social-share__icons">
					<span class='st_facebook_large' displayText='Facebook'></span>
					<span class='st_twitter_large' displayText='Tweet'></span>
					<span class='st_googleplus_large' displayText='Google +'></span>
					<span class='st_linkedin_large' displayText='LinkedIn'></span>
					<span class='st_pinterest_large' displayText='Pinterest'></span>
					<span class='st_email_large' displayText='Email'></span>
				</div>
			{/if}
		</div>
	</div>
</div>

{literal}
	<script type="text/javascript">var switchTo5x=true;</script>
	<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
	<script type="text/javascript">stLight.options({publisher: "3f1014ed-afda-46f1-956a-a51d42078320", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
{/literal}
{javascript}
	<script type="text/javascript">
		dockDetailsFooter();
		$(window).on('resize orientationchange', function(){
			dockDetailsFooter();
		});

		function dockDetailsFooter() {
			$(".details-footer").affix({
				offset: {
					bottom: function () {
						return (this.bottom = $('.footer').outerHeight(true))
					}
				}
			});
		}
		$('.details-footer__btn-apply').on('click', function(e) {
			if ($(this).attr('href') != '') {
				e.preventDefault();
				e.stopPropagation();
				window.open($(this).attr('href'));
			}
		});

		$('.alert__close').on('click', function(e) {
			e.preventDefault();
			$(this).closest('.alert').hide();
		});
	</script>
{/javascript}