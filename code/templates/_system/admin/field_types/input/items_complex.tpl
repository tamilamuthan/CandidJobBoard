
{assign var="complexField" value='items' scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
	<tr>
		<td colspan="4"></td>
	</tr>
	<tr>
		<th colspan="3">[[Item]]</th>
		<th>[[Price]]</th>
	</tr>

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<tr id='complexFields_{$complexField}'>
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == 'products'}
					<td colspan="3">
						{display property=$form_field.id complexParent=$complexField complexStep=$complexElementKey template="items_list.tpl"}
						<br /><br />
						{display property='custom_item' complexParent=$complexField complexStep=$complexElementKey}
					</td>
				{elseif $form_field.id != 'custom_item' && $form_field.id != 'custom_info' && $form_field.id != 'qty' && $form_field.id != 'price'}
					<td>{$GLOBALS.settings.listing_currency}{display property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}</td>
				{/if}
			{/foreach}
		</tr>
	{/foreach}

{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
