{foreach from=$field_errors item=error key=field_caption}
	<div class="alert alert-danger">
        {if $error eq 'EMPTY_VALUE'}
            {assign var="field_caption" value=$field_caption|tr}
            [[Please enter '$field_caption']]
        {elseif $error eq 'NOT_ACCEPTABLE_FILE_FORMAT'}
            {$field_caption}: [[File format is not supported]]
        {elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
            [[Please enter valid email address]]
        {elseif $error eq 'NOT_UNIQUE_VALUE'}
            [[This email address is already in use.]]
        {elseif $error eq 'NOT_CONFIRMED'}
            [[Passwords did not match]]
        {elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
            '{$field_caption}' [[length is exceeded]]
        {elseif $error eq 'NOT_INT_VALUE'}
            '{$field_caption}' [[is not an integer value]]
        {elseif $error eq 'NOT_FLOAT_VALUE'}
            '{$field_caption}' [[is not a float value]]
        {elseif $error eq 'NOT_VALID_ID_VALUE'}
            [[Please enter valid]] {$field_caption}
        {elseif $error eq 'NOT_VALID'}
            [[Please enter valid]] {$field_caption}
        {elseif $error eq 'MAX_FILE_SIZE_EXCEEDED'}
            '{$field_caption}' [[File size shouldn't be larger than 5 MB.]]
        {elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
        	'{$field_caption}' [[File size shouldn't be larger than 5 MB.]]
        {elseif $error eq 'UPLOAD_ERR_PARTIAL'}
        	'{$field_caption}' [[There was an error during file upload]]
        {elseif $error eq 'UPLOAD_ERR_NO_FILE'}
        	'{$field_caption}' [[file not specified]]
		{elseif $error eq 'WRONG_DATE_FORMAT'}
			'{$field_caption}' [[The date format is incorrect]]
        {elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
			'{$field_caption}': [[Please enter valid YouTube link]]
        {elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
            [[Image format is not supported]]
        {else}
       		[[{$error}]]
        {/if}
    </div>
{/foreach}