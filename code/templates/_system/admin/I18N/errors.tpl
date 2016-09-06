{foreach from=$errors item=error}
	{if is_array($error)}
		{if $error.0 eq 'TOO_LONG_TRANSLATION'}
			<p class="error">'[[{$error.1}]]' [[You have exceeded the limit of maximum allowed symbols for the field]]</p>
		{/if}
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}
