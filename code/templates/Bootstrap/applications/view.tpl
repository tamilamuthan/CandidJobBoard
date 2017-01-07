{literal}
<style>
.editbutton,
.deletebutton {
    cursor: pointer;
    font-family: Arial, sans-serif;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 10px;
    text-decoration: none;
    display: inline-block;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
.editbutton {
    background: #f9fdff;
    background: -moz-linear-gradient(top,  #f9fdff 0%, #bbd4e5 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f9fdff), color-stop(100%,#bbd4e5));
    background: -webkit-linear-gradient(top,  #f9fdff 0%,#bbd4e5 100%);
    background: -o-linear-gradient(top,  #f9fdff 0%,#bbd4e5 100%);
    background: -ms-linear-gradient(top,  #f9fdff 0%,#bbd4e5 100%);
    background: linear-gradient(to bottom,  #f9fdff 0%,#bbd4e5 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9fdff', endColorstr='#bbd4e5',GradientType=0 );
    -moz-box-shadow: inset 0 1px 0 0 #fff;
    -webkit-box-shadow: inset 0 1px 0 0 #fff;
    box-shadow: inset 0 1px 0 0 #fff;
    border: 1px solid #b5b3b5;
    color: #777;
    text-shadow: 1px 1px 0 #fff;
}
.editbutton:hover {
    background: #bbd4e5;
    background: -moz-linear-gradient(top,  #bbd4e5 0%, #f9fdff 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bbd4e5), color-stop(100%,#f9fdff));
    background: -webkit-linear-gradient(top,  #bbd4e5 0%,#f9fdff 100%);
    background: -o-linear-gradient(top,  #bbd4e5 0%,#f9fdff 100%);
    background: -ms-linear-gradient(top,  #bbd4e5 0%,#f9fdff 100%);
    background: linear-gradient(to bottom,  #bbd4e5 0%,#f9fdff 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bbd4e5', endColorstr='#f9fdff',GradientType=0 );
}
.deletebutton {
    -moz-box-shadow: inset 0 1px 0 0 #f7c5c0;
    -webkit-box-shadow: inset 0 1px 0 0 #f7c5c0;
    box-shadow: inset 0 1px 0 0 #f7c5c0;
    background: -webkit-gradient( linear, left top, left bottom, color-stop(0.05, #f0a5a1), color-stop(1, #e67e7e) );
    background: -moz-linear-gradient( center top, #f0a5a1 5%, #e67e7e 100% );
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f0a5a1', endColorstr='#e67e7e');
    background-color: #f0a5a1;
    border: 1px solid #b8372e;
    color: #fff;
    text-shadow: 1px 1px 0 #9c3830;
}
.deletebutton:hover {
    background: -webkit-gradient( linear, left top, left bottom, color-stop(0.05, #e67e7e), color-stop(1, #f0a5a1) );
    background: -moz-linear-gradient( center top, #e67e7e 5%, #f0a5a1 100% );
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#e67e7e', endColorstr='#f0a5a1');
    background-color: #e67e7e;
}
</style>
{/literal}

{title}[[Applicants]]{/title}
<h1 class="my-account-title">[[My Account]]</h1>
<div class="my-account-list">
    <ul class="nav nav-pills">
        <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/job/">[[Job Postings]]</a></li>
        <li class="presentation active"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Applications]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questions]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Company Profile]]</a></li>
    </ul>
</div>
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
			{if $error_code == 'NO_SUCH_FILE'} <p class="alert alert-danger">[[No such file found in the system]]</p>
			{elseif $error_code == 'NO_SUCH_APPS'} <p class="alert alert-danger">[[No such application with this ID]]</p>
			{elseif $error_code == 'APPLICATIONS_NOT_FOUND'}
				{if $current_filter}
					<p class="alert alert-danger">[[There are no applications for "$listing_title"]]</p>
				{else}
					<p class="alert alert-danger">[[You have no applications so far.]]</p>
				{/if}
			{/if}
	{/foreach}
{/if}
<div class="details-body__left applicants">

    <form method="post" name="applicationFilter" action="" id="applicationFilter" class="clearfix">
        <input type="hidden" name="orderBy" value="{$orderBy|escape:'html'}" />
        <input type="hidden" name="order" value="{$order}" />
        <input type="hidden" name="appsPerPage" value="{$appsPerPage}" />
        <div class="col-xs-12 col-sm-3">
            <h3 class="title__primary title__primary-sall">[[ {$cnt_pending+$cnt_approved+$cnt_rejected} ]] [[Applications]]</h3>
        </div>
        <div class="col-xs-12 col-sm-9 app-job-filter" style="border:0px solid red;">
           {if $can_use_questionnaire}
            <select name="score" class="form-control" style="display:inline; width: 20%; float: right">
                <option value="">[[Any Score]]</option>
                <option value="passed" {if $score == 'passed'} selected="selected"{/if}>[[Passed]]</option>
                <option value="not_passed" {if $score == 'not_passed'} selected="selected"{/if}>[[Not passed]]</option>
            </select>
            {/if}
            <select name="appJobId" class="form-control" style="display:inline; width: 50%; float:right">
                <option value="">[[All Jobs]]</option>
                {foreach from=$appJobs item=appJob}
                    <option value="{$appJob.id}"{if $appJob.id == $current_filter} selected="selected"{/if}>{$appJob.title}</option>
                {/foreach}
            </select>
        </div>
        <input type="submit" value="[[Filter]]" class="btn btn-default hidden filter-button" />
    </form>

    <div id="applicants-list" style="margin-top: 20px;">
        {if $can_use_app_management}
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#pending">Pending ([[$cnt_pending]])</a></li>
            <li><a data-toggle="tab" href="#rejected">Rejected ([[$cnt_rejected]])</a></li>
            <li><a data-toggle="tab"  href="#approved">Approved ([[$cnt_approved]])</a></li>
        </ul>

        <div class="tab-content">
            <div id="pending" class="tab-pane fade in active">
                <h3>&nbsp;</h3>
		        {include file='view_tab.tpl' tab="Pending"}
            </div>
            <div id="rejected" class="tab-pane fade">
                <h3>&nbsp;</h3>
		        {include file='view_tab.tpl' tab="Rejected"}
            </div>
            <div id="approved" class="tab-pane fade">
                <h3>&nbsp;</h3>
		        {include file='view_tab.tpl' tab="Approved"}
            </div>
        </div>
        {else}
		     {include file='view_default.tpl'}
        {/if}
        <button type="button" class="load-more btn btn__white {if $applications|@count < $appsPerPage}hidden{/if}" data-page="2">
            [[Load more]]
        </button>
    </div>
</div>
{javascript}
	<script>
		function showCoverLetter(id) {
			message('[[Cover letter]]', $("#coverLetter_" + id).html());
		}

        $('.form-control').change(function () {
            $('.filter-button').trigger('click');
        });

        var listingPerPage = {$appsPerPage};
        $('.load-more').click(function() {
            var self = $(this);
            self.addClass('loading');
            $.get('?appJobId={$current_filter}&action=search&page=' + self.data('page'), function(data) {
                self.removeClass('loading');
                var listings = $(data).find('.listing-item');
                if (listings.length) {
                    $('.listing-item').last().after(listings);
                    self.data('page', parseInt(self.data('page')) + 1);
                }

                if ($(data).find('.listing-item').length < listingPerPage) {
                   self.hide();
                }
            });
        });

        $(document).ready(function() {
            $('.nav-pills').scrollLeft($('.nav-pills').width() / 2);
        
        });
	</script>
{/javascript}
