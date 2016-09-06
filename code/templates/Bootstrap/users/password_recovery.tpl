{foreach from=$errors key=error_code item=error_message}
	{if $error_code == 'WRONG_EMAIL'}
		<p class="alert alert-danger">[[Please enter valid email address]]</p>
	{/if}
{/foreach}
<form method="post" action="" class="form form__modal password-recovery">
	<h1 class="title__primary title__primary-small title__centered title__bordered">[[Forgot Your Password?]]</h1>
	<div class="form-group text-center password-recovery__description">
		[[Please enter your email address and we'll send you a link to reset your password right away!]]
	</div>
	<div class="form-group">
		<input type="email" name="email" value="{$email|escape}" class="form-control" placeholder="[[Email]]"/>
	</div>
	<div class="form-group form-group__btns text-center">
		<input type="submit" name="submit" value="[[Reset my password]]" class="btn btn__orange btn__bold" />
	</div>
</form>
