<div class="BreadCrumbs">
    <p><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a> &#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a> &#187; [[Edit Questionnaire]]</p>
</div>

<h1>[[Edit Questionnaire]]</h1>
<a href="{$GLOBALS.site_url}/edit-questions/{$sid}">[[Edit Questions]]</a><br/><br/>

{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'{$field_caption}' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'{$field_caption}' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'{$field_caption}' [[is not an float value]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">'{$field_caption}' [[is not valid]]</p>
	{elseif $error eq 'CAN_NOT_EQUAL_NULL'}
		<p class="error">'{$field_caption}' [[can not equal "0"]]</p>
	{/if}
{/foreach}
{if $edit}
	<p class="message">[[Your changes were successfully saved]]</p>
{/if}

<form method="post" action="" class="form" style="margin-left:0">
    {if $action == 'edit'}
        <input type="hidden" name="submit" value="edit" />
    {else}
        <input type="hidden" name="submit" value="add" />
    {/if}
    {foreach from=$form_fields item=form_field}
        {if $form_field.id == 'email_text_more'}
            <div id="email_text_more_set" class="form-group" {if $request.send_auto_reply_more != 1}style="display:none"{/if}>
                <label class="form-label">[[$form_field.caption]] <span style="color:red">&nbsp;{if $form_field.is_required}*{/if}</span></label>
                {input property=$form_field.id}
            </div>
        {elseif $form_field.id == 'email_text_less'}
            <div id="email_text_less_set" class="form-group" {if $request.send_auto_reply_less != 1}style="display:none"{/if}>
                <label class="form-label">[[$form_field.caption]] <span style="color:red">&nbsp;{if $form_field.is_required}*{/if}</span></label>
                {input property=$form_field.id}
            </div>
        {elseif $form_field.id == "send_auto_reply_more"}
            <p><span class="strong">[[Send Auto-Reply email to candidates whose score is]]</span></p>
            <div class="form-group">
                <label class="form-label" style="display:inline">[[$form_field.caption]] <span style="color:red">&nbsp;{if $form_field.is_required}*{/if}</span></label>
                {input property=$form_field.id}
            </div>
        {elseif $form_field.id == "send_auto_reply_less"}
            <div class="form-group">
                <label class="form-label" style="display:inline">[[$form_field.caption]] <span style="color:red">&nbsp;{if $form_field.is_required}*{/if}</span></label>
                {input property=$form_field.id}
            </div>
        {else}
            <div class="form-group">
                <label class="form-label">[[$form_field.caption]] <span class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</span></label>
                {input property=$form_field.id}
            </div>
        {/if}
    {/foreach}
    <div class="form-group">
        {if $action == 'edit'}
            <input type="submit" name="action_add" value="[[Edit]]" class="btn btn__orange btn__bold" />
        {else}
            <input type="submit" name="action_add" value="[[Add]]" class="btn btn__orange btn__bold" />
        {/if}
    </div>
</form>

{javascript}
<script type="text/javascript">
	$("document").ready(function() {
	    doBinding();
	});

    function doBinding() {
        $("#send_auto_reply_more").bind("click", function() {
            $("#email_text_more_set").css('display', this.checked ? 'block' : 'none');
        })

        $("#send_auto_reply_less").bind("click", function() {
   	        $("#email_text_less_set").css('display', this.checked ? 'block' : 'none');
        })
    }
</script>
{/javascript}
