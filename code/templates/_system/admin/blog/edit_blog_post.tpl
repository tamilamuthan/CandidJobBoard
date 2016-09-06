{breadcrumbs}
    <a href="{$GLOBALS.site_url}/blog/">[[Blog]]</a>
    &#187; [[Edit Blog Post]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Blog Post]]</h1>
{include file='../classifieds/field_errors.tpl'}
<fieldset class="wide-fieldset">
    <legend>&nbsp;[[Edit Blog Post]]</legend>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{$postId}" />
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" id="submit" name="form_submit" value="save_blog_post"/>
        <table>
            {foreach from=$form_fields item=form_field}
                {if $form_field.id == 'image'}
                    <tr>
                        <td valign="top" width="20%">[[{$form_field.caption}]]</td>
                        <td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
                        <td valign="top">{input property=$form_field.id template="picture_blog.tpl"}</td>
                    </tr>
                {else}
                    <tr>
                        <td valign="top" width="20%">[[{$form_field.caption}]]</td>
                        <td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
                        <td valign="top">{input property=$form_field.id}</td>
                    </tr>
                {/if}
            {/foreach}
            <tr>
                <td colspan="3" align="right">
                    <div class="floatRight"><input type="submit" name="form_submit" value="[[Update]]" class="greenButton"/></div>
                </td>
            </tr>
        </table>
    </form>
</fieldset>