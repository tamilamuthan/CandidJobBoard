{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		{assign var="field_caption" value=$field_caption|tr}
		<p class="error">[[Please enter '$field_caption']]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_CONFIRMED'}
		<p class="error">[[Passwords did not match]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an integer value]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not a float value]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">[[Please enter valid]] [[{$field_caption}]]</p>
	{elseif $error eq 'MAX_FILE_SIZE_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[File size shouldn't be larger than 5 MB.]]</p>
	{elseif $error eq 'NO_SUCH_FILE'}
		<p class="error">'[[No such file found in the system]]</p>
	{elseif $error eq 'NOT_STRING_ID_VALUE'}
		<p class="error">[[Use at least one A-Z letter value in the '$field_caption' field]]</p>
	{elseif $error eq 'WRONG_DATE_FORMAT'}
		<p class="error">'[[{$field_caption}]]' [[The date format is incorrect]]</p>
	{elseif $error eq 'NOT_PLUS_VALUE'}
		<p class="error"> '[[{$field_caption}]]' [[The number you have entered is negative. Please enter a positive number]]</p>
	{elseif $error eq 'INVALID_EMAIL_TEMPLATE_SID_WAS_SPECIFIED'}
		<p class="error"> '[[Invalid Email Id has been specified]]</p>
	{elseif $error eq 'WRONG_GROUP'}
		<p class="error"> '[[Wrong Group has been specified]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error"> '[[{$field_caption}]]' [[File size shouldn't be larger than 5 MB.]]</p>
	{elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
		<p class="error">'[[{$field_caption}]]': [[Please enter valid YouTube link]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}