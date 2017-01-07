        {foreach item=application from=$applications name=applications}
            <article class="media well listing-item {if $listing.type.id eq 'Job'}listing-item__jobs{elseif $listing.type.id eq 'Resume'}listing-item__resumes{/if}">
                {if $application.resumeInfo.Photo.file_url}
                    <div class="media-left listing-item__logo listing-item__resumes">
                        <div class="job-seeker__image">
                            <a class="link profile__image" href="{$GLOBALS.site_url}{$application.resumeInfo|listing_url}">
                                <img class="media-object profile__img" src="{$application.resumeInfo.Photo.file_url}" />
                            </a>
                        </div>
                    </div>
                {/if}
                <div class="media-body">
                    <div class="media-heading listing-item__title">
                        <span class="app-track-link">
                            {if $application.resume}
                                {if $application.resumeInfo}
                                    <a href="{$GLOBALS.site_url}{$application.resumeInfo|listing_url}">
                                        {$application.username|escape}
                                    </a>
                                {else}
                                    [[Not Available Anymore]]
                                {/if}
                            {else}
                                <a href="?appsID={$application.id}&amp;filename={$application.file|escape:"url"}">{$application.username|escape}</a>
                            {/if}
                        </span> <br />
                    </div>
                    <div class="listing-item__info clearfix">
                        <span class="listing-item__info--item listing-item__info--item-company">
                            {$application.job.Title}
                        </span>
                    </div>
                    <div class="listings-application-info clearfix">
                        <div class="listing-item__date visible-xs-480">{$application.date|date}</div>
                        <a class="listings-application-info--item link" href="mailto:{$application.email}">{$application.email}</a>
                        {if $application.file}
                            <a class="listings-application-info--item link" href="?appsID={$application.id}&amp;filename={$application.file|escape:"url"}">[[Resume file]]</a>
                        {/if}
                        {if $application.resumeInfo.Resume.file_name}
                            <a class="listings-application-info--item link" href="{$GLOBALS.site_url}{$application.resumeInfo|listing_url}?filename={$application.resumeInfo.Resume.saved_file_name|escape:'url'}">[[Resume file]]</a>
                        {/if}
                        {if $application.comments}
                            <span class="listings-application-info--item">
                                <a class="link" onclick="showCoverLetter('{$application.id}')" href="#">[[Cover letter]]</a>
                                <div id="coverLetter_{$application.id}" style="display: none">
                                    {$application.comments|escape}
                                </div>
                            </span>
                        {/if}
                    </div>
                </div>
                <div class="media-right text-right hidden-xs-480">
                    <div class="listing-item__date">{$application.date|date}</div>
                </div>
            </article>
        {/foreach}
