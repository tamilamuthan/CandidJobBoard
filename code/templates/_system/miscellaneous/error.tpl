<div id="blank">
	{if $ERROR eq 'NOT_LOGIN'}
		{module name="users" function="login"}
	{elseif $ERROR eq 'ACCESS_DENIED' || $ERROR eq 'NOT_OWNER'}
		<p class="error">[[You don't have permissions to access this page.]]</p>
		<p><a href="javascript: history.back()">[[Back]]</a></p>
	{elseif $ERROR eq 'WRONG_INVOICE_ID_SPECIFIED'}
		<p class="error">[[There is no such invoice in the system]]</p>
	{elseif $ERROR eq 'INVOICE_ALREADY_PAID'}
		<p class="error">[[Invoice already paid]]</p>
	{elseif $ERROR eq 'NOT_VALID_PAYMENT_ID'}
		<p class="error">[[Invalid payment ID is specified]]</p>
	{/if}
</div>