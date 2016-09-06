{breadcrumbs}
    <a href="{$GLOBALS.site_url}/blog/">[[Blog]]</a> &#187; [[Add Blog Post]]
{/breadcrumbs}

<h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add Blog Post]]</h1>

{include file='../classifieds/field_errors.tpl'}
<fieldset class="wide-fieldset">
    <legend>[[Add Blog Post]]</legend>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add" />
        <input type="hidden" name="form_submit" value="add_blog_post" />
        <table>
            {foreach from=$form_fields item=form_field}
                <tr>
                    <td width="20%;">[[{$form_field.caption}]]</td>
                    <td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
                    <td>{input property=$form_field.id}</td>
                </tr>
            {/foreach}
            <tr>
                <td colspan="3" align="right"><div class="floatRight"><input type="submit" name="form_submit" value="[[Add]]" class="grayButton"/></div></td>
            </tr>
        </table>
    </form>
</fieldset>
