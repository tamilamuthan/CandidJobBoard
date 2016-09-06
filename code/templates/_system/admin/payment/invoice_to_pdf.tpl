{literal}
	<style type="text/css">
		*{font-size: 14px;}
		h2 {font-size: 18px;}
		div.clr {clear: both;}
		td.invoice-border-top {border-top: 1px solid #d9d9d9;}
		table#invoice-table {
			border-collapse: collapse;
			padding: 5px;
		}
		table#invoice-table td {border: 1px solid #d9d9d9;}
		tr.invoice-table-head {
			background-color: #ededed;
			font-weight: bold;
		}
		tr.invoice-amount-total {
			background-color: #f5f5f5;
			font-weight: bold;
			text-align: right;
		}
		td.align_right {text-align: right;}
	</style>
{/literal}
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="47%" valign="top"><img src="{$GLOBALS.user_site_url}/templates/{$GLOBALS.settings.TEMPLATE_USER_THEME}/assets/images/{$theme_settings.logo|escape:'url'}" border="0" /><br/></td>
		<td width="6%" rowspan="2">&nbsp;</td>
		<td width="47%" valign="top" class="align_right">
			<h2>[[Order]]</h2>
			<br/>[[Date]]: {display property="date"}
			<br/>[[Order]] &#35;{$invoice_sid}
			{display property="payment_method" assign = payment_method}
			<br/>[[Order Status]]: {display property="status"}{if $payment_method}&nbsp;({display property="payment_method"}){/if}
			<br/>
		</td>
	</tr>
	<tr>
		<td valign="top" class="invoice-border-top">
			<br/><h2>[[Bill To]]</h2>
			<br/>{if $user.CompanyName}{$user.CompanyName}{else}{$user.FullName}{/if}
			<br/>{$user|location}
		</td>
		<td valign="top" class="invoice-border-top">
			<br/><h2>[[Send Payment To]]</h2>
			<br/>{$GLOBALS.settings.send_payment_to}
		</td>
	</tr>
</table>
<div class="clr"></div>
{display property="items" template="items_complex.tpl"}