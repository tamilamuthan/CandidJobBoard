{title} [[Order]] #{$invoice_sid}{/title}
{keywords} [[Print Order]] {/keywords}
{description} [[Print Order]] {/description}
<div id="view-invoice">
	{if $errors}
		{foreach from=$errors key=error item=error_message}
			<p class="error">
				{if $error eq 'WRONG_INVOICE_ID_SPECIFIED'}
					[[There is no such order in the system]]
					{elseif $error eq 'NOT_OWNER'}
					[[You're not owner of this order]]
				{/if}
			</p>
		{/foreach}
		{else}
		{if $paymentError}
			<p class="error">[[Order is not verified]]</p>
		{/if}
		<div class="printPage">
			{display property="status" assign=status}
			{display property="payment_method" assign=payment_method}

			<div id="invoice-logo">
				<img src="{$GLOBALS.user_site_url}/templates/{$GLOBALS.settings.TEMPLATE_USER_THEME}/assets/images/{$theme_settings.logo|escape:'url'}" border="0" />
			</div>
			<div id="invoice-info">
				<strong>[[Order]]</strong><br/>
				[[Date]]:&nbsp;{display property="date"}<br/>
				[[Order]]&nbsp;&#35;:&nbsp;{$invoice_sid}<br/>
				[[Order Status]]:&nbsp;{$status}{if $payment_method}&nbsp;({display property="payment_method"}){/if}<br/>
			</div>
			<div class="clr"></div>
			<div id="invoice-billto">
				<strong>[[Bill To]]</strong>
				<br/>{if $user.CompanyName}{$user.CompanyName}{else}{$user.username}{/if}
				<br/>{$user.GoolgePlace}
			</div>
			<div id="invoice-sendto">
				<strong>[[Send Payment To]]</strong>
				<br/>{$GLOBALS.settings.send_payment_to}
			</div>
			<div class="clr"></div>
			{display property="items" template="items_complex.tpl"}
		</div>
		<fieldset id="invoice-buttons">
			<input type=button value="[[Print]]" onClick="getElementById('invoice-buttons').style.display='none'; window.print();" class="standart-button">
		</fieldset>
	{/if}
</div>