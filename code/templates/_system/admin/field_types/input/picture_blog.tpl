{if $value.file_name ne null}
    <a href="{$GLOBALS.site_url}/blog/?action=delete_image&amp;id={$postId}">[[Delete]]</a>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <img src="{$value.file_url}" alt="" border="0" />
    <br/><br/>
{/if}
<input type="file" name="{$id}" />
