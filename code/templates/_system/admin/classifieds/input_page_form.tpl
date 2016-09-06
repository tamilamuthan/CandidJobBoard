<script type="text/javascript">
    $(function() {
        var action = '{$action}';
        if (action == 'new') {
            $('#save').attr('value', '[[Add]]');
        }
    });
</script>

{breadcrumbs}[[$listingTypeInfo.name Fields]]{/breadcrumbs}

{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
      {assign var="field_caption" value=$field_caption|tr}
		<p class="error">[[Please enter '$field_caption']]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'{$field_caption}' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'{$field_caption}' [[is not a float value]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">[[Please enter valid]] [[{$field_caption}]]</p>
	{elseif $error eq 'CAN_NOT_EQUAL_NULL'}
		<p class="error">'{$field_caption}' [[can not equal "0"]]</p>
	{/if}
{/foreach}

{if $action == 'edit'}
    <h1>[[{$pageInfo.page_name}]] [[Fields]]</h1>
    <a href="{$GLOBALS.site_url}/add-listing-type-field/?listing_type_sid={$listingTypeInfo.sid}" class="grayButton">[[Add New $listingTypeInfo.name Field]]</a>

    <form method="post" action="" name="fields_items_form" id="fields_items_form">
        <input type="hidden" name="field_action" id="field_action" value="save_order" />
        <input type="hidden" name="page_sid" id="page_sid" value="{$pageSID}" />
        <div class="clr"><br/></div>

        <table id="fields_table">
            <thead>
                <tr>
                    <th>[[Caption]]</th>
                    <th>[[Type]]</th>
                    <th>[[Required]]</th>
                    <th colspan="4" width="20%" class="actions">[[Actions]]</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$fieldsOnPage item=fieldOnPage name=fieldList}
                    <tr {if $fieldOnPage.type == 'location'}style="display: none;"{else}class="{cycle values = 'evenrow,oddrow' advance=true}"{/if}>
                        <td class="{if $fieldOnPage.type == 'location'} posting-field-td{/if}">
							{if $fieldOnPage.type == 'location'}
								<div class="field-holder">
									[[{$fieldOnPage.caption}]]
									<input type="hidden" name="item_order[{$fieldOnPage.sid}]" value="1">
								</div>
								{foreach from=$fieldOnPage.fields item=field}
									<div class="field">
                                        [[{$field.caption}]]
									</div>
								{/foreach}
                            {else}
								[[{$fieldOnPage.caption}]]
								<input type="hidden" name="item_order[{$fieldOnPage.sid}]" value="1">
                            {/if}
                        </td>
                        <td {if $fieldOnPage.type == 'location'} class="posting-field-td" {/if}>
                        	{if $fieldOnPage.type == 'location'}
                        		<div>
                        			[[{$fieldOnPage.typeCaption}]]
                        		</div>
                        		{foreach from=$fieldOnPage.fields item=field}
									<div class="field">
			                            [[{$field.typeCaption}]]
									</div>
								{/foreach}
                        	{else}
                        		[[{$fieldOnPage.typeCaption}]]
                        	{/if}
                        </td>
                        <td {if $fieldOnPage.type == 'location'} class="posting-field-td" {/if}>
                        	{if $fieldOnPage.type == 'location'}
								<div>
                        			&nbsp;
                        		</div>
								{foreach from=$fieldOnPage.fields item=field}
									<div class="field">
			                            {if $field.is_required}[[Yes]]{else}[[No]]{/if}
									</div>
								{/foreach}
                        	{else}
                        		{if $fieldOnPage.is_required}[[Yes]]{else}[[No]]{/if}
                        	{/if}
                        </td>
                        <td  align="center" valign="top" nowrap="nowrap">
                            <a href="{$GLOBALS.site_url}/edit-listing-type-field/?sid={$fieldOnPage.sid}&amp;listing_type_sid={$listingTypeInfo.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a>
                        </td>
                        <td  align="center" valign="top">
                            {if not $fieldOnPage.is_reserved}
                                <a href="{$GLOBALS.site_url}/delete-listing-type-field/?sid={$fieldOnPage.sid}" onclick='return confirm("[[Remove the field?]]")' title="[[Remove]]" class="deletebutton">[[Remove]]</a></td>
                            {/if}
                        <td  align="center" valign="top">
                            {if $smarty.foreach.fieldList.iteration < $smarty.foreach.fieldList.total}
                                <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/?field_action=move_down&field_sid={$fieldOnPage.sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
                            {/if}
                        </td>
                        <td  align="center" valign="top">
                            {if $smarty.foreach.fieldList.iteration > 1}
                                <a href="{$GLOBALS.site_url}/posting-pages/{$listingTypeInfo.id|lower}/edit/{$pageSID}/?field_action=move_up&field_sid={$fieldOnPage.sid}"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
                            {/if}
                        </td>
                    </tr>
                {/foreach}

            </tbody>
        </table>
    </form>
	<div class="clr"><br/></div>
	<script type="text/javascript" src="{$GLOBALS.site_url}/../system/ext/jquery/jquery.tablednd.js"></script>
	<script>
		$('#apply').click(
			function(){
				$('#action').attr('value', 'apply_info');
			}
		);
	</script>
{/if}