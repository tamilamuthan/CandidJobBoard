{if $message_sent == false}
	{foreach key="key" item="value" from=$field_errors}
		{if $key == 'EMAIL'}
			<div class="alert alert-danger">[[Please enter valid email address]]</div>
		{elseif $key == 'NAME'}
			<div class="alert alert-danger">[[Please provide your full name.]]</div>
		{elseif $key == 'COMMENTS'}
			<div class="alert alert-danger">[[Please include your comments.]]</div>
		{/if}
	{/foreach}
	<div class="row static-pages content-text">
		<form method="post" action="" onsubmit="disableSubmitButton('submit-contact');" class="form col-xs-12 col-lg-11 col-md-11 col-sm-11">
			<input type="hidden" name="action" value="send_message" />
			<div class="form-group form-group__half margin">
				<label for="name" class="form-label">[[Full Name]]</label>
				<input type="text" class="form-control" name="name" value="{$name|escape}" />
			</div>
			<div class="form-group form-group__half margin pull-right">
				<label for="email" class="form-label">[[Email]]</label>
				<input type="text" name="email" class="form-control" value="{if $GLOBALS.current_user.logged_in}{$email|default:$GLOBALS.current_user.username|escape}{else}{$email|escape}{/if}" />
			</div>
			<div class="form-group">
				<label for="name" class="form-label">[[Comments]]:</label>
				<textarea cols="20" rows="10" class="form-control" name="comments">{$comments|escape}</textarea>
			</div>
			<div class="form-group form-group__btns text-center">
				<input class="btn btn__bold btn__orange" type="submit" value="[[Submit]]" id="submit-contact"/>
			</div>
		</form>
	</div>
{else}
	<div class="row static-pages content-text">
		<div class="alert alert-success" role="alert">[[Thank you very much for your message. We will respond to you as soon as possible.]]</div>
	</div>
{/if}