<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<meta name="keywords" content="{if $GLOBALS.settings.home_page_keywords}{$GLOBALS.settings.home_page_keywords|escape}{else}{$KEYWORDS|escape}{/if}">
	<meta name="description" content="{if $GLOBALS.settings.home_page_description}{$GLOBALS.settings.home_page_description|escape}{else}{$DESCRIPTION|escape}{/if}">
	<meta name="viewport" content="width=device-width, height=device-height,
                                   initial-scale=1.0, maximum-scale=1.0,
                                   target-densityDpi=device-dpi">
	<link rel="alternate" type="application/rss+xml" title="[[Jobs]]" href="{$GLOBALS.site_url}/rss/">

	<title>{if $GLOBALS.settings.home_page_title}{$GLOBALS.settings.home_page_title}{else}{$GLOBALS.settings.site_title}{/if}</title>

	<link href="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery-ui.css" rel="stylesheet">

	<!-- Bootstrap -->
	<link href="{$GLOBALS.site_url}/templates/Bootstrap/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<link href="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.bxslider.css" rel="stylesheet">
	<link href="{$GLOBALS.site_url}/templates/Bootstrap/assets/style/styles.css" rel="stylesheet">

	[[$HEAD]]
	<style type="text/css">{$GLOBALS.theme_settings.custom_css}</style>
	{$GLOBALS.theme_settings.custom_js}
</head>
<body>
	{include file="../menu/header.tpl"}
	<div class="main-banner">
		<div class="main-banner__wrapper">
			<div class="container text-center">
				{module name="classifieds" function="count_listings"}
			</div>
		</div>
	</div>
	<div class="quick-search__frontpage">
		{$MAIN_CONTENT}
	</div>
	{module name="users" function="featured_profiles" items_count="20"}
	<section class="main-sections main-sections__middle-banner middle-banner">
		<div class="container container-fluid text-center middle-banner__wrapper">
			<div class="middle-banner__block--wrapper">
				[[{$GLOBALS.theme_settings.secondary_banner_text}]]
			</div>
		</div>
	</section>
	{module name="classifieds" function="featured_listings" items_count="4" listing_type="Job"}
	{module name="classifieds" function="latest_listings" items_count="4" listing_type="Job"}
	{if $GLOBALS.theme_settings.jobs_by_category || $GLOBALS.theme_settings.jobs_by_city || $GLOBALS.theme_settings.jobs_by_state || $GLOBALS.theme_settings.jobs_by_country}
		{assign var="isFirstBrowse" value=true}
		<section class="main-sections main-sections__jobs-by jobs-by">
			<div class="jobs-by__wrapper">
				<div class="container container-fluid">
					<ul class="nav nav-pills" role="tablist">
						{if $GLOBALS.theme_settings.jobs_by_category}
							<li role="presentation" {if $isFirstBrowse}class="active"{/if}><a href="#jobs-by__category" aria-controls="jobs-by__category" role="tab" data-toggle="pill">[[Jobs by Category]]</a></li>
							{assign var="isFirstBrowse" value=false}
						{/if}
						{if $GLOBALS.theme_settings.jobs_by_city}
							<li role="presentation" {if $isFirstBrowse}class="active"{/if}><a href="#jobs-by__city" aria-controls="jobs-by__city" role="tab" data-toggle="pill">[[Jobs by City]]</a></li>
							{assign var="isFirstBrowse" value=false}
						{/if}
						{if $GLOBALS.theme_settings.jobs_by_state}
							<li role="presentation" {if $isFirstBrowse}class="active"{/if}><a href="#jobs-by__state" aria-controls="jobs-by__state" role="tab" data-toggle="pill">[[Jobs by State]]</a></li>
							{assign var="isFirstBrowse" value=false}
						{/if}
						{if $GLOBALS.theme_settings.jobs_by_country}
							<li role="presentation" {if $isFirstBrowse}class="active"{/if}><a href="#jobs-by__country" aria-controls="jobs-by__country" role="tab" data-toggle="pill">[[Jobs by Country]]</a></li>
							{assign var="isFirstBrowse" value=false}
						{/if}
					</ul>
					{assign var="isFirstBrowse" value=true}
					<div class="tab-content">
						{if $GLOBALS.theme_settings.jobs_by_category}
							<div role="tabpanel" class="tab-pane fade {if $isFirstBrowse}in active{/if}" id="jobs-by__category">
								{module name="classifieds" function="browse" columns=3 browseUrl="/categories/" browse_template="browse_by_category.tpl"}
							</div>
							{assign var="isFirstBrowse" value=false}
						{/if}
						{if $GLOBALS.theme_settings.jobs_by_city}
							<div role="tabpanel" class="tab-pane fade {if $isFirstBrowse}in active{/if}" id="jobs-by__city">
								{module name="classifieds" function="browse" columns=3 browseUrl="/cities/" browse_template="browse_by_city.tpl"}
							</div>
							{assign var="isFirstBrowse" value=false}
						{/if}
						{if $GLOBALS.theme_settings.jobs_by_state}
							<div role="tabpanel" class="tab-pane fade {if $isFirstBrowse}in active{/if}" id="jobs-by__state">
								{module name="classifieds" function="browse" columns=3 browseUrl="/states/" browse_template="browse_by_state.tpl"}
							</div>
							{assign var="isFirstBrowse" value=false}
						{/if}
						{if $GLOBALS.theme_settings.jobs_by_country}
							<div role="tabpanel" class="tab-pane fade {if $isFirstBrowse}in active{/if}" id="jobs-by__country">
								{module name="classifieds" function="browse" columns=3 browseUrl="/countries/" browse_template="browse_by_country.tpl"}
							</div>
							{assign var="isFirstBrowse" value=false}
						{/if}
					</div>
				</div>
			</div>
		</section>
	{/if}
	<section class="main-sections main-sections__alert alert">
		<div class="container container-fluid">
			<div class="alert__block subscribe__description">
				[[{$GLOBALS.theme_settings.bottom_section_html}]]
			</div>
			<div class="alert__block alert__block-form">
				<form action="{$GLOBALS.site_url}/guest-alerts/create/" method="post" id="create-alert" class="well alert__form">
					<input type="hidden" name="action" value="save" />
					<div class="alert__messages">
					</div>
					<div class="form-group alert__form__input">
						<input type="email" class="form-control" name="email" value="" placeholder="[[Your email]]">
					</div>
					<div class="form-group alert__form__input">
						<select class="form-control" name="email_frequency">
							<option value="daily">[[Daily]]</option>
							<option value="weekly">[[Weekly]]</option>
							<option value="monthly">[[Monthly]]</option>
						</select>
					</div>
					<div class="form-group alert__form__input text-center">
						<input type="submit" name="save" value="[[Create alert]]" class="btn__submit-modal btn btn__orange btn__bold" onclick="return createAlert();">
					</div>
				</form>
			</div>
		</div>
	</section>
	{include file="../menu/footer.tpl"}

	<script src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.min.js"></script>
	<script src="{$GLOBALS.site_url}/templates/Bootstrap/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.bxslider.min.js"></script>

	<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.form.min.js"></script>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$GLOBALS.settings.google_api_key}&signed_in=true&libraries=places&callback=initService&language={$GLOBALS.current_language}" async defer></script>
	{javascript}
		<script language="javascript" type="text/javascript">
			document.addEventListener("touchstart", function() { }, false);

			function createAlert() {
				var options = {
					target: '.alert__messages',
					url:  $('#create-alert').attr('action'),
					success: function(data) {
						if (data) {
							$('#create-alert').find('.form-control[name="email"]').text('').val('');
							$('#create-alert').find('.btn').blur();
						}
						$('.alert__messages').find('#create-alert').remove();
					}
				};
				$('#create-alert').ajaxSubmit(options);
				return false;
			}

			$(document).ready(function() {
				$('.nav-pills li').on('click', function() {
					var current = $('.nav-pills').scrollLeft();
					var left = $(this).position().left;

					if ( $( this ).is(':first-child') ) {
						$('.nav-pills').scrollLeft(0);
					} else {
						$('.nav-pills').animate({
							scrollLeft: current + left - 15
						}, 300);
					}
				});
			});
		</script>
	{/javascript}
	{js}
</body>
</html>