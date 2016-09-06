{foreach from=$errors item="error_message" key="error"}
	{if $error eq "INVALID_REQUEST"}
		<p class="error">[[{$error_message}]]</p>
	{elseif $error eq "INVALID_DATA"}
		<p class="error">[[{$error_message}]]</p>
	{elseif $error eq "PARAMETERS_MISSED"}
		<p class="error">[[The key parameters are not specified]]</p>
	{elseif $error eq "MYSQL_ERROR"}
		{$error_message}
	{elseif $error eq "NOT_LOGGED_IN"}
		<p class="error">[[No logged in user found]]</p>
	{elseif $error eq "NOT_OWNER"}
		<p class="error">[[You're not owner of this listing]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size shouldn't be larger than 5 MB.]]</p>
	{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
		<p class="error">[[Please choose Excel or csv file]]</p>
	{elseif $error eq 'DO_NOT_MATCH_SELECTED_FILE_FORMAT'}
		<p class="error">[[The file type do not match with selected file format]]</p>
	{else}
		<p class="error">[[{$error}]] [[{$error_message}]]</p>
	{/if}
{/foreach}