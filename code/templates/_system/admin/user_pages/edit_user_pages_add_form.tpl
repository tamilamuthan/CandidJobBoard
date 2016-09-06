
{if $IS_NEW == 1}
    {breadcrumbs}<a href="{$GLOBALS.site_url}/user-pages/">[[Pages]]</a> &#187; [[Add a New Page]]{/breadcrumbs}
    <h1><img src="{image}/icons/linedpaperplus32.png" border="0" alt="" class="titleicon"/>[[Add a New Page]]</h1>
{else}
    {breadcrumbs}<a href="{$GLOBALS.site_url}/user-pages/">[[Pages]]</a> &#187; [[Edit Page]]{/breadcrumbs}
    <h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Page]]</h1>
{/if}

{foreach from=$ERRORS key=ERROR item=ERROR_DATA}
	{if $ERROR == 'URI_NOT_SPECIFIED'}<p class="error">[[The page URL is not specified]]</p>{/if}
	{if $ERROR == 'ADD_ERROR'}<p class="error">[[Cannot add new Page]]</p>{/if}
	{if $ERROR == 'CHANGE_ERROR'}<p class="error">[[Cannot change data of the Page]]</p>{/if}
	{if $ERROR == 'PAGE_EXISTS'}<p class="error">[[Page with such URI is already exist]]</p>{/if}
	{if $ERROR == 'DELETE_PAGE'}<p class="error">[[Page URL is not defined]]</p>{/if}
	{if $ERROR == 'PAGE_ALREADY_EXISTS'}<p class="error">[[Page with such url already exists]]</p>{/if}
{/foreach}

<form name="form1" method="post">
	<input type="hidden" name="action" value="{$action}" />
    <input type="hidden" id="submit" name="submit" value="save_page" />
	<fieldset>
		<legend>{if $IS_NEW == 1}[[Add a New Page]]{else}[[Edit Page]]{/if}</legend>
		<table>
			<tr><td colspan="3"><input type="hidden" name="ID" value="{$user_page.ID}" /></td></tr>
			<tr>
				<td>[[Page Title]]</td>
				<td class="required"></td>
				<td><input type="text" name="title" value="{$user_page.title|escape}" /></td>
			</tr>
			<tr>
				<td valign="top">[[Page Content]]</td>
				<td class="required"></td>
				<td>
					{WYSIWYGEditor name='content' class='inputText' width="100%" height="150" type='ckeditor' value=$user_page.content conf="BasicAdmin"}
				</td>
			</tr>
			<tr>
				<td>[[URL]]</td>
				<td class="required">*</td>
				<td><input type="text" name="uri" value="{$user_page.uri}" /></td>
			</tr>
			<tr>
				<td valign="top">[[Meta Description]]</td>
				<td class="required"></td>
				<td><textarea name="description" cols="55" rows="4">{$user_page.description}</textarea></td>
			</tr>
			<tr>
				<td valign="top">[[Meta Keywords]]</td>
				<td class="required"></td>
				<td><input name="keywords" type="text" value="{$user_page.keywords|escape}">
				</td>
			</tr>
			<tr>
				<td colspan="3">
                    <div class="floatRight">
                        {if ! $IS_NEW}
                            <input type="submit" id="apply" value="[[Apply]]" class="grayButton" />
                        {/if}
                        <input type="submit" value="{if $IS_NEW == 1}[[Add]]{else}[[Save]]{/if}" class="grayButton" />
                    </div>
                </td>
			</tr>
		</table>
	</fieldset>
</form>

<script>
	$('#apply').click(
		function(){
			$('#submit').attr('value', 'apply_page');
		}
	);
</script>