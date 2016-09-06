<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
{breadcrumbs}[[Blog]]{/breadcrumbs}
<h1><img src="{image}icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Blog Posts]]</h1>
{include file='../classifieds/field_errors.tpl'}
<p style="text-align: right">
    <a href="{$GLOBALS.site_url}/blog/?action=add" class="grayButton">[[Add Blog Post]]</a>
</p>

<form method="post" action="{$GLOBALS.site_url}/blog/" name="resultsForm">
    <input type="hidden" name="action" id="action_name" value="">
    <div class="box" id="displayResults">
        <div class="box-header">
            {include file="../pagination/pagination.tpl" layout="header"}
        </div>
        <div class="innerpadding">
            <div id="displayResultsTable">
                <table width="100%">
                    <thead>
                    {include file="../pagination/sort.tpl"}
                    </thead>
                    <tbody>
                    {foreach from=$posts item=item name=blog_posts}
                        <tr class="{cycle values = 'evenrow,oddrow'}">
                            <td><input type="checkbox" name="posts[{$item.sid}]" value="1" id="checkbox_{$smarty.foreach.blog_posts.iteration}"></td>
                            <td><a href="{$GLOBALS.site_url}/blog/?action=edit&id={$item.sid}" title="{$item.title|escape}">{$item.title}</a></td>
                            <td>{$item.date|date}</td>
                            <td>{if $item.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>
                            <td><a href="{$GLOBALS.site_url}/blog/?action=delete&posts[{$item.sid}]=1" onclick="return confirm('[[Are you sure you want to delete the selected post?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
                            <td><a href="{$GLOBALS.user_site_url}/blog/{$item.sid}/{$item.title|pretty_url}/" title="[[View]]" class="editbutton" target="_blank">[[View]]</a></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer">
            {include file="../pagination/pagination.tpl" layout="footer"}
        </div>
    </div>
</form>
<script>
    $('#apply').click(
        function(){
            $('#submit').attr('value', 'apply');
        }
    );
</script>