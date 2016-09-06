{if $errors == null}
	{if $listing.type.id eq 'Resume'}
		<div class="checkout-message text-center">
			<h1 class="title__primary title__primary-small title__centered title__bordered">
				[[You have successfully posted your resume.]]
			</h1>
			<p class="paragraph">
				[[You have successfully posted your resume.]] <br/>
			</p>
			<p>
				<a href="{$GLOBALS.site_url}/my-resume-details/{$listing.id}/" class="link">[[Preview your resume.]]</a>
			</p>
			<p>
				<a href="{$GLOBALS.site_url}/edit-resume/?listing_id={$listing.id}" class="link">
					[[Edit your resume in "My Account" section]]
				</a>
			</p>
		</div>
	{/if}
{else}
	{foreach from=$errors key=error item=error_message}
		{if $error == 'PARAMETERS_MISSED'}
			<p class="error">[[The key parameters are not specified]]</p>
		{elseif $error == 'WRONG_PARAMETERS_SPECIFIED'}
			<p class="error">[[Wrong parameters are specified]]</p>
		{elseif $error == 'NOT_OWNER'}
			<p class="error">[[You are not owner of this listing]]</p>
		{elseif $error == 'NOT_LOGGED_IN'}
			{module name="users" function="login"}
		{/if}
	{/foreach}
{/if}