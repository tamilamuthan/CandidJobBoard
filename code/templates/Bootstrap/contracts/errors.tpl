{foreach from=$ERRORS item="error_message" key="error"}
{if $error eq "NOT_LOGGED_IN"}
	{module name="users" function="login"}
{/if}
{/foreach}