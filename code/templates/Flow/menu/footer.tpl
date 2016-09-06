<div class="page-row">
	{if 'banner_bottom'|banner}
		<div class="banner banner--bottom {if $GLOBALS.user_page_uri == '/job/' || $GLOBALS.user_page_uri == '/job-preview/'}banner--job-details{/if}">
			{'banner_bottom'|banner}
		</div>
	{/if}

	<footer class="footer">
		<div class="container">
			{assign var="current_year" value=$smarty.now|date_format:"%Y"}
			[[{$GLOBALS.theme_settings.footer}]]
		</div>
	</footer>
	{if $GLOBALS.settings.google_TrackingID}
	<script>
		{literal}
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		{/literal}
		ga('create', '{$GLOBALS.settings.google_TrackingID}', 'auto');
		ga('send', 'pageview');
	</script>
	{/if}
</div>