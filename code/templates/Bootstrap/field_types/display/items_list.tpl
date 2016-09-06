{if $value == -1}
	{$qty}&nbsp;&nbsp;&#120;&nbsp;{$custom_item|unescape:"html"|paymentTranslate}
{else}
	{foreach from=$products item=list_value}
		{if $list_value.sid == $value}
			{tr}{$list_value.name}{/tr}
		{/if}
	{/foreach}
{/if}
