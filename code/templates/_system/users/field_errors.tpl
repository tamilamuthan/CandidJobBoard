{foreach from=$errors item=error key=field_caption}
	<p class="error">
		{if $error eq 'EMPTY_VALUE'}
			{assign var="field_caption" value=$field_caption|tr}
			[[Please enter '$field_caption']]
		{elseif $error eq 'NOT_UNIQUE_VALUE'}
			'{$field_caption}' [[this value is already used in the system]]
		{elseif $error eq 'NOT_CONFIRMED'}
			[[Passwords did not match]]
		{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
			'{$field_caption}' [[length is exceeded]]
		{elseif $error eq 'NOT_INT_VALUE'}
			'{$field_caption}' [[is not an integer value]]
		{elseif $error eq 'NOT_FLOAT_VALUE'}
			'{$field_caption}' [[is not an float value]]
		{elseif $error eq 'NOT_VALID_ID_VALUE'}
			[[You can use only alphanumeric characters for]] '{$field_caption}'
		{elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
			{$field_caption}: [[Image format is not supported]]
		{elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
			[[Please enter valid email address]]
		{elseif $error eq 'HAS_BAD_WORDS'}
			'{$field_caption}' [[has bad words]]
		{elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
			[[Please enter valid YouTube link]]
		{elseif $error eq 'NOT_VALID'}
			[[Please enter valid]] [[{$field_caption}]]
		{else}
			[[{$error}]]
		{/if}
	</p>
{/foreach}


