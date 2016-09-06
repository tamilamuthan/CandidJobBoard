{assign var="LocationValues" value=$value}
{foreach from=$form_fields item=form_field}
	{assign var="fixInstructionsForComplexField" value=true}
	<tr {if $form_field.hidden || true}style="display:none;"{/if} id="{$parentID}_{$form_field.id}">
		<td valign="top" width="20%">{tr}{$form_field.caption}{/tr|escape:'html'}</td>
		<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
		<td class="locationField">{input property=$form_field.id parent=$parentID}</td>
	</tr>
{/foreach}
{assign var="parentID" value=false scope=global}
{assign var="fixInstructionsForComplexField" value=false}
