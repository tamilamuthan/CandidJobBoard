{foreach from=$field_errors item=error key=field_caption}
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
            [[Please enter valid]] {$field_caption}
        {elseif $error eq 'NOT_VALID'}
            [[Please enter valid]] {$field_caption}
        {elseif $error eq 'NOT_SUPPORTED_VIDEO_FORMAT'}
            '{$field_caption}' [[this file is not in a supported video file format]]
        {elseif $error eq 'NOT_CONVERT_VIDEO'}
            '{$field_caption}' [[Could not convert video file]]
        {elseif $error eq 'MAX_FILE_SIZE_EXCEEDED'}
            '{$field_caption}' [[filesize exceeds the quota]]
        {elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
        	'{$field_caption}' [[File size exceeds system limit]]
        {elseif $error eq 'UPLOAD_ERR_FORM_SIZE'}
        	'{$field_caption}' [[File size exceeds system limit]]
        {elseif $error eq 'UPLOAD_ERR_PARTIAL'}
        	'{$field_caption}' [[There was an error during file upload]]
        {elseif $error eq 'UPLOAD_ERR_NO_FILE'}
        	'{$field_caption}' [[file not specified]]
        {elseif $error eq 'HAS_BAD_WORDS'}
        	'{$field_caption}' [[has bad words]]
		{elseif $error eq 'WRONG_DATE_FORMAT'}
			'{$field_caption}' [[The date format is incorrect]]
        {elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
			'{$field_caption}': [[Please enter valid YouTube link]]
		{elseif $error eq 'CURRENCY_SIGN_IS_EMPTY'}
			{capture name="currencySignIsEmpty"}[[Select currency sign for '$field_caption' field]]{/capture}
			{eval var=$smarty.capture.currencySignIsEmpty}
        {elseif $error eq 'UNABLE_TO_ADD_BLOG_POST'}
            [[Unable to add blog post]]
        {else}
       		{$error}
        {/if}
    </p>
{/foreach}