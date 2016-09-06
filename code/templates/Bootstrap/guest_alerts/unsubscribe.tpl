{if !empty($error)}
	<div class="alert alert-danger">
		[[{$error}]]
	</div>
{else}
	<div class="alert alert-success">
		[[Your email $email was successfully unsubscribed from the Email Alert]]
	</div>
{/if}
