<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>SmartJobBoard [[Admin Panel]]{if $TITLE} | {$TITLE|escape}{/if}</title>
	<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
    <link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/themes/green/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{image src="jquery-ui-1.8.custom.css"}" />
	<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery-ui.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery.multiselect.css" />
	<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.form.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.bgiframe.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>

	<script>
		var langSettings = {
			thousands_separator : '{$GLOBALS.current_language_data.thousands_separator}',
			decimal_separator : '{$GLOBALS.current_language_data.decimal_separator}',
			decimals : '{$GLOBALS.current_language_data.decimals}',
			currencySign: '{currencySign}',
			showCurrencySign: 1,
			currencySignLocation: '{$GLOBALS.current_language_data.currencySignLocation}',
			rightToLeft: {$GLOBALS.current_language_data.rightToLeft}
		};
	</script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/floatnumbers_functions.js"></script>
	{capture name="displayProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}
    <script language="JavaScript" type="text/javascript">

		// Set global javascript value for page
		window.SJB_GlobalSiteUrl = '{$GLOBALS.site_url}';
		window.SJB_AdminSiteUrl  = '{$GLOBALS.admin_site_url}';
		window.SJB_UserSiteUrl   = '{$GLOBALS.user_site_url}';

		currentSjbVersion = {
			major: "{$GLOBALS.version.major}",
			minor: "{$GLOBALS.version.minor}",
			build: "{$GLOBALS.version.build}"
		};

		{if not $isSaas}
            $(document).ready(function() {
                // check for availabled SJB updates
                $.getJSON(window.SJB_AdminSiteUrl + "/system/miscellaneous/update_check/", function(data) {
                    if (data.updateStatus == 'available' && !data.closed_by_user) {
                        $("#updateInfoBlock").show("slide", { direction: "up"}, 500);
                    }
                });

                $("#closeUpdateInfoBlock").click(function() {
                    $("#updateInfoBlock").hide("slide", { direction: "up"}, 500);
                    $.post(window.SJB_AdminSiteUrl + "/system/miscellaneous/update_check/", { action: "mark_as_closed"});
                });
            });
		{/if}

		$.extend($.ui.dialog.prototype.options, {
			modal: true
		});

		function popUpWindow(url, widthWin, heightWin, title, iframe, callbackFunction) {
			$("#messageBox").dialog('destroy').html('{$smarty.capture.displayProgressBar|escape:'javascript'}');
			$("#messageBox").dialog({
				width: widthWin,
				height: heightWin,
				modal: true,
				title: title,
				close: function(event, ui) {
					if (callbackFunction) {
						callbackFunction();
					}
				}
			}).dialog('open');
			if (iframe) {
				$("#messageBox").html('<iframe border="0" runat="server" width="100%" height="100%" frameborder="0" src="'+url+'"></iframe>');
			} else {
				$.get(url, function(data) {
					$("#messageBox").html(data);
				});
			}
			return false;
		}

		function popUpMessageWindow(widthWin, heightWin, title, message) {
			$("#messageBox").dialog("destroy" ).html(message);
			$("#messageBox").dialog({
				width: widthWin,
				height: heightWin,
				title: title
			}).dialog("open");
			return false;
		}
	</script>
</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" id="structure"  height="100%">
		<tr class="page-row page-row-expanded">
			<td id="left" valign="top" height="100%">
				<div id="leftHeader" style="text-align:left">
					<a href="{if $GLOBALS.settings.domain}http://{$GLOBALS.settings.domain}{$GLOBALS.base_url}{else}{$GLOBALS.user_site_url}{/if}" class="view-frontend" target="_blank" title="[[View your job board]]"></a>
					<a href="{$GLOBALS.admin_site_url}" id="logoLink"></a>
                    <span class="packageVersion">[[version]] {$GLOBALS.version.major}.{$GLOBALS.version.minor}.{$GLOBALS.version.build}</span>
				</div>
				<div class="clr"><br/></div>
				{module name="menu" function="show_left_menu"}
				<div class="clr"><br/></div>
			</td>
			<td valign="top" height="100%">
				<div id="messageBox"></div>

				<div id="topGray">
					<div id="updateInfoBlock">
						<a href="{$GLOBALS.site_url}/update-to-new-version/">[[New update available]]</a>
						<span id="closeUpdateInfoBlock">X</span>
					</div>
					<div id="breadCrumbs">
						{if $GLOBALS.user_page_uri !== "/"}<a href="{$GLOBALS.site_url}/">[[Dashboard]]</a> &#187;{/if} [[{$ADMIN_BREADCRUMBS}]]
					</div>
					<div id="topRight">
						<select id="my-account">
							<option style="display:none;">{$smarty.session.username}</option>
							{if $isSaas}
								<option value="upgrade" data-href="{$billingUrl}/l.php?user={$smarty.session.username|escape:'url'}&amp;pass={$smarty.session.password|whmcs_encode|base64_encode|escape:'url'}&amp;product={$smarty.session.whmcsProductId}">[[Upgrade]]</option>
							{/if}
							<option value="my_account" data-href="{$billingUrl}">[[My Account]]</option>
							<option value="logout" data-href="{$GLOBALS.site_url}/system/users/logout/">[[Log out]]</option>
						</select>
					</div>
				</div>
				
				<div class="InContent">
					<div style="margin: 0 215px 0 0">
						{module name='flash_messages' function='display'}
					</div>
					{$MAIN_CONTENT}
					<div class="clr"><br/></div>
				</div>
			
			</td>
		</tr>
		<tr class="page-row">
			<td colspan="2">
				<div id="footer">
					[[Copyright]] &copy;  {$smarty.now|date_format:"%Y"} [[Powered by]] <a href="http://www.smartjobboard.com/">SmartJobBoard</a>
				</div>
			</td>
		</tr>
	</table>
	<script>
		$('#my-account').change(function() {
			window.location.href = $(this).find(':selected').data('href');
		});
	</script>
	<script>
		{literal}
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		{/literal}
		ga('create', 'UA-71150631-1', 'auto');
		ga('send', 'pageview');
	</script>

	<!-- Chatra {literal} -->
		<script>
			ChatraID = '8BZ9fXKJPeSkTzHyh';
			(function(d, w, c) {
				var n = d.getElementsByTagName('script')[0],
						s = d.createElement('script');
				w[c] = w[c] || function()
						{ (w[c].q = w[c].q || []).push(arguments); }
				;
				s.async = true;
				s.src = (d.location.protocol === 'https:' ? 'https:': 'http:')
						+ '//call.chatra.io/chatra.js';
				n.parentNode.insertBefore(s, n);
			})(document, window, 'Chatra');
		</script>
	<!-- /Chatra {/literal} -->

	{if $isSaas}
		<script>
			window.intercomSettings = {
				app_id: '{$intercom_app}',
				user_id: '{$smarty.session.whmcsProductId}',
				domain: '{if $GLOBALS.settings.domain}{$GLOBALS.settings.domain}{else}{$smarty.server.HTTP_HOST}{/if}'
			};
			{literal}(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/hhrd1569';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})(){/literal}

			$(document).ready(function() {
				$('#generalTab input[name="logo"]').change(function() {
					Intercom('trackEvent', 'change-logo');
				});
			});
		</script>
	{/if}
</body>
</html>