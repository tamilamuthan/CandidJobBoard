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

			{* EMAILS *}
			{elseif $messageId eq 'ERROR_SEND_ACTIVATION_EMAIL'}
				[[Failed to send activation email]]
			
			{* EXPORT *}
			{elseif $messageId eq "CANT_CREATE_EXPORT_FILES"}
				[[Cannot create export files]]
			{elseif $messageId eq "EMPTY_EXPORT_PROPERTIES"}
				[[There are no selected properties. Select at least one property to export.]]
			{elseif $messageId eq 'EMPTY_EXPORT_DATA'}
				[[There is no data to export. Change your search criteria.]]
			{elseif $messageId eq 'ERROR_ADD_BANNER_GROUP'}
				[[System error while adding new banner group]]
			{else}
				[[$messageId]]
			{/if}
		{/capture}
		
		<p class="{$type}">{$messageValue|escape}</p>
	{/foreach}
{/foreach}
