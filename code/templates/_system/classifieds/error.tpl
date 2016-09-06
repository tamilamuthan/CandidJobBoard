{foreach from=$ERRORS item="error_message" key="error"}
	{if $error eq "INVALID_REQUEST"}
		<p class="error">{$error_message}</p>
	{elseif $error eq "INVALID_DATA"}
		<p class="error">{$error_message}</p>
	{elseif $error eq "PARAMETERS_MISSED"}
		[[The key parameters are not specified]]
	{elseif $error eq "MYSQL_ERROR"}
		{$error_message}
	{elseif $error eq "NOT_LOGGED_IN"}
		{module name="users" function="login"}
	{elseif $error == 'DEFAULT_VALUE_NOT_SET'}
		<p class="error">Default value for {$error_message} is not set</p>
	{elseif $error eq 'COMMENT_HAS_BAD_WORDS'}
		<p class="error">[[Your comment has bad words]]</p>
	{/if}
{/foreach}