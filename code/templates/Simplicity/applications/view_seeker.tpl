{title}[[My Applications]]{/title}
{capture assign='trListingTypeName'}Resumes{/capture}
<h1 class="my-account-title">[[My Account]]</h1>
<div class="my-account-list">
    <ul class="nav nav-pills">
        <li class="presentation"> <a href="{$GLOBALS.site_url}/my-listings/resume">[[My Resumes]]</a></li>
        <li class="presentation active"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[My Applications]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Account Settings]]</a></li>
    </ul>
</div>
<div class="col-xs-12 details-body__left applicants">
    {if $applications}
        <div class="col-xs-12">
            <h3 class="title__primary title__primary-small">[[My Applications]]</h3><br/>
        </div>
        <div id="applicants-list">
            {foreach item=application from=$applications name=applications}
                <article class="media well listing-item {if $listing.type.id eq 'Job'}listing-item__jobs{elseif $listing.type.id eq 'Resume'}listing-item__resumes{/if}">
                    {if $application.resumeInfo.Photo.file_url}
                        <div class="media-left listing-item__logo">
                            <a href="{$GLOBALS.site_url}{$listing|listing_url}">
                                <img class="media-object" src="{$application.resumeInfo.Photo.file_url}" />
                            </a>
                        </div>
                    {/if}
                    <div class="media-body">
                        <div class="media-heading listing-item__title">
                            <a href="{$GLOBALS.site_url}{$application.job|listing_url}">{$application.job.Title}</a>
                        </div>
                        <div class="listing-item__date visible-xs-480">[[Applied]]: {$application.date|date}</div>
                        <div class="listings-application-info listing-item__info clearfix">
                            <span class="listing-item__info--item listing-item__info--item-company">
                                {$application.company.CompanyName}
                            </span>
                            {if $application.job|location}
                                <span class="listing-item__info--item listing-item__info--item-location">
                                    {$application.job|location}
                                </span>
                            {/if}
                        </div>
                    </div>
                    <div class="media-right text-right hidden-xs-480">
                        <div class="listing-item__date">[[Applied]]: {$application.date|date}</div>
                    </div>
                </article>
            {/foreach}
        </div>
    {else}
        <div id="applicants-list">
            <div class="alert alert-danger">
                [[You haven't applied to any job yet.]]
            </div>
        </div>
    {/if}
</div>
{javascript}
    <script>
        function modifyNote(noteId, url) {
            $.get(url, function(data) {
                $("#formNote_" + noteId).html(data);
                $("#trNote_" + noteId).css("display", "table-row").css("border-bottom","1px solid #B2B2B2");
                $("#trAppl_" + noteId).css("border-bottom","0px");
                $("#tdCheckbox_" + noteId).attr("rowspan", "2");
            });
        }

        function showCoverLetter(id) {
            message('[[Cover letter]]', $("#coverLetter_" + id).text());
        }

        $(document).ready(function() {
            $('.nav-pills').scrollLeft($('.nav-pills').width() / 2);
        });

    </script>
{/javascript}