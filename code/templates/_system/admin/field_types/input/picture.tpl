{if $value.file_name ne null}
    {if $user_info}
        <a href="{$GLOBALS.site_url}/users/delete-uploaded-file/?user_sid={$user_info.user_sid}&amp;field_id={$id}">[[Remove]]</a>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <img src="{$value.file_url}" alt="" border="0" />
        <br/><br/>
    {else}
        <div id="file_{$id}">
            <a class="delete_file"
               form_token="{$form_token}"
               listing_id="{if $listing_id}{$listing_id}{else}{$listing.id}{/if}"
               field_id="{$id}"
               file_id="{$value.file_id}"
               data-type="picture"
               href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listing.id}&amp;field_id={$id}&amp;form_token={$form_token}">[[Remove]]</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <img src="{$value.file_url}" alt="" border="0" />
            <br/><br/>
        </div>
    {/if}
{/if}
<input type="file" name="{$id}" />
