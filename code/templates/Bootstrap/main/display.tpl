<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <meta name="keywords" content="{$KEYWORDS|escape}">
    <meta name="description" content="{$DESCRIPTION|escape}">
    <meta name="viewport" content="width=device-width, height=device-height,
                                   initial-scale=1.0, maximum-scale=1.0,
                                   target-densityDpi=device-dpi">
    <link rel="alternate" type="application/rss+xml" title="[[Jobs]]" href="{$GLOBALS.site_url}/rss/">

    <title>{if $TITLE}{tr}{$TITLE}{/tr|escape} | {/if}{$GLOBALS.settings.site_title}</title>

    <link href="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery-ui.css" rel="stylesheet">
    <link href="{$GLOBALS.site_url}/templates/Bootstrap/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{$GLOBALS.site_url}/templates/Bootstrap/assets/style/styles.css" rel="stylesheet">

    [[$HEAD]]
    <style type="text/css">{$GLOBALS.theme_settings.custom_css}</style>
    {$GLOBALS.theme_settings.custom_js}
</head>
<body>
    {include file="../menu/header.tpl"}
    {module name='flash_messages' function='display'}
    <div class="page-row page-row-expanded">
        <div class="display-item{if 'banner_right_side'|banner} with-banner{/if}">
            {$MAIN_CONTENT}
            <div id="apply-modal" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Modal Window</h4>
                        </div>
                        <div class="modal-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file="../menu/footer.tpl"}

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{$GLOBALS.site_url}/templates/Bootstrap/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery-ui.min.js"></script>

    <script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.form.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.validate.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/templates/Bootstrap/common_js/autoupload_functions.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/templates/Bootstrap/assets/third-party/jquery.selectbox-0.2.min.js"></script>
    <link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/templates/Bootstrap/common_js/multilist_functions.js"></script>
    <script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
    <script>
        document.addEventListener("touchstart", function() { }, false);

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

    <script language="javascript" type="text/javascript">

        // Set global javascript value for page
        window.SJB_GlobalSiteUrl = '{$GLOBALS.site_url}';
        window.SJB_UserSiteUrl   = '{$GLOBALS.user_site_url}';

        function windowMessage() {
            $("#messageBox").dialog( 'destroy' ).html('[[You already applied to this]]');
            $("#messageBox").dialog({
                bgiframe: true,
                modal: true,
                title: '[[Error]]',
                buttons: {
                    Ok: function() {
                        $(this).dialog('close');
                    }
                }
            });
        }
        function popUpWindow(url, widthWin, title, parentReload, userLoggedIn, callbackFunction) {
            reloadPage = false;
            $("#loading").show();
            {*$("#messageBox").dialog( 'destroy' ).html('{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}{$smarty.capture.displayJobProgressBar|escape:'quotes'}');*}
            $("#messageBox").dialog({
                autoOpen: false,
                width: widthWin,
                height: 'auto',
                modal: true,
                title: title,
                close: function(event, ui) {
                    if (callbackFunction) {
                        callbackFunction();
                    }
                    if (parentReload == true && !userLoggedIn && reloadPage == true) {
                        parent.document.location.reload();
                    }
                }
            }).hide();

            $.get(url, function(data){
                $("#messageBox").html(data).dialog("open").show();
                $("#loading").hide();
            });

            return false;
        }

        $("#apply-modal")
                .on('show.bs.modal', function(event){
                    var button = $(event.relatedTarget);
                    if (button.data('applied')) {
                        $(this).find('.modal-title').text('[[You already applied to this]]');
                        $(this).find('.modal-body').html('[[You already applied to this]]');
                    } else {
                        var titleData = button.data('title');
                        $(this).find('.modal-title').text(titleData);
                        $(this).find('.modal-body').load(button.data('href'), function() {
                            $(this).find('.form-control').focus().select()
                        });
                    }
                })
                .on('shown.bs.modal', function(){
                    $(this).find('.form-control').first().focus().select();
                });

        $('.toggle--refine-search').on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('collapsed');
            $('.refine-search__wrapper').toggleClass('show');
            $(document).mouseup(function (e) {
                var container = $(".refine-search");
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.toggle--refine-search').removeClass('collapsed');
                    $('.refine-search__wrapper').removeClass('show');
                }
            });
        });

        $('#apply-modal').on('click', '.email-frequency__btn-js', function(){
            $('.email-frequency__btn-js').each(function(){
                $(this).removeClass('active');
            });
            $(this).addClass('active');
        })
    </script>

    {js}
</body>
</html>
