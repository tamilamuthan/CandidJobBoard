{foreach from=$messagesArray key=type item=messages}
	{foreach from=$messages item=message}
		{if is_array($message)}
			{assign var='messageId' value=$message.messageId}
		{else}
			{assign var='messageId' value=$message}
		{/if}
		
		{capture assign='messageValue'}
			{* FIELDS *}
			{if $messageId eq 'EMPTY_VALUE'}
				{assign var="field_caption" value=$message.fieldCaption|tr}
				[[Please enter '$field_caption']]
			{else}
				[[$messageId]]
			{/if}
		{/capture}
		
		<p class="{$type} alert alert-danger">{$messageValue|escape:'html'}</p>
	{/foreach}
{/foreach}
