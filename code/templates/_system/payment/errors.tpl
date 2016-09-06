{foreach from=$ERRORS item="error_message" key="error"}
	{if $error eq "NOT_LOGGED_IN"}
		{module name="users" function="login"}
	{elseif $error == 'INVALID_GATEWAY'}
        <p class="error">[[Invalid gateway ID is specified]]</p>
	{else}
		<p class="error">[[{$error_message}]]</p>
	{/if}
{/foreach}
