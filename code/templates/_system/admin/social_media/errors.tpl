{foreach from=$field_errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		{assign var="field_caption" value=$field_caption|tr}
		<p class="error">[[Please enter '$field_caption']]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'{$field_caption}' [[this value is already used in the system]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'{$field_caption}' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'{$field_caption}' [[is not an integer value]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'{$field_caption}' [[is not a float value]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">[[Please enter valid]] {$field_caption}</p>
	{elseif $error eq 'NOT_VALID'}
		<p class="error">[[Please enter valid]] {$field_caption}</p>
	{else}
		<p class="error">{$error}</p>
	{/if}
{/foreach}
