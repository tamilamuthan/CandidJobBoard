{foreach from=$errors key=error_code item=error_message}
    {if $error_code == 'EMPTY_USERNAME'}
		<p class="alert alert-danger">[[Wrong verification key is specified]]</p>
    {elseif $error_code == 'EMPTY_VERIFICATION_KEY'}
		<p class="alert alert-danger">[[Wrong verification key is specified]]</p>
    {elseif $error_code == 'WRONG_VERIFICATION_KEY'}
		<p class="alert alert-danger">[[Wrong verification key is specified]]</p>
	{elseif $error_code == 'NOT_CONFIRMED'}
		<p class="alert alert-danger">[[Passwords did not match]]</p>
	{/if}
{/foreach}

<form method="post" action="" class="form form__modal password-recovery">
	<input type="hidden" name="username" value="{$username}" />
	<input type="hidden" name="verification_key" value="{$verification_key}" />
	<h3 class="title__primary title__primary-small title__centered title__bordered">[[Change Password]]</h3>
	<div class="form-group">
		<input type="password" name="password" class="form-control" placeholder="[[Password]]">
	</div>
	<div class="form-group">
		<input type="password" name="confirm_password" class="form-control" placeholder="[[Confirm Password]]">
	</div>
	<div class="form-group form-group__btns text-center">
		<input type="submit" name="submit" value="[[Change Password]]" class="btn btn__orange btn__bold">
	</div>
</form>