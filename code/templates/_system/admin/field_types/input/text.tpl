{capture name="wysiwygName"}{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}{/capture}
{capture name="wysiwygClass"}inputText{if $complexField} complexField{/if}{/capture}
{assign var='wysiwygType' value='ckeditor'}
{if $id == 'instructions'}
    {assign var='wysiwygType' value='none'}
{/if}

{WYSIWYGEditor name=$smarty.capture.wysiwygName class=$smarty.capture.wysiwygClass width="100%" height="150" type=$wysiwygType value=$value conf="BasicAdmin"}