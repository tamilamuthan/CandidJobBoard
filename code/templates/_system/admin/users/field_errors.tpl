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
{elseif $error eq 'RESERVED_ID_VALUE'}
	<p class="error">[["$field_caption" current value is reserved for system]]</p>
{elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
	<p class="error">[[Please enter valid email address]]</p>
{elseif $error eq 'Administrator Current Password is Required'}
	<p class="error">'[[{$field_caption}]]' [[Administrator Current Password is Required]]</p>
{elseif $field_caption eq 'QTY_FIELDS_IS_EMPTY'}
	<p class="error">[[All the Qty fields in Volume Based Pricing should be filled]]</p>
{elseif $field_caption eq 'QTY_FIELDS_RANGE_ERROR'}
	<p class="error">[[The Qty fields should be filled From: min To: max but not vice versa]]</p>
{elseif $error eq 'NOT_STRING_ID_VALUE'}
	<p class="error">[[Use at least one A-Z letter value in the '$field_caption' field]]</p>
{elseif $error eq 'NOT_PLUS_VALUE'}
	<p class="error"> '{$field_caption}' [[The number you have entered is negative. Please enter a positive number]]</p>	
{elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
	<p class="error">[[Please enter valid YouTube link]]</p>
{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
	<p class="error"> '[[{$field_caption}]]' [[File size shouldn't be larger than 5 MB.]]</p>
{elseif $error eq 'NOT_ACCEPTABLE_FILE_FORMAT'}
	<p class="error">[[Not supported file format]]</p>
{else}
	<p class="error">[[{$error}]]</p>
{/if}
{/foreach}