{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-invoices/">[[Orders]]</a>&nbsp;&#187;&nbsp;[[View Order]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[View Order]]</h1>
{include file='errors.tpl'}
<fieldset>
	<legend>&nbsp;[[View Order]]</legend>
	<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="save" id="action">
	<input type="hidden" name="sid" value="{$invoice_sid}"/>
		<table>
			<tr>
				<td>[[Customer]]:</td>
				{if $user}
					<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user.sid}">{if $user.CompanyName}{$user.CompanyName|escape}{else}{$user.FullName|escape}{/if}</a></td>
				{else}
					<td><span class="invoice-washy">[[User deleted]]</span></td>
				{/if}
				<td>[[Order Status]]:</td>
				<td>{display property="status"}</td>
			</tr>
			<tr>
				<td>[[Order Date]]:</td>
				<td>{display property="date"}</td>
				<td>[[Payment Method]]:</td>
				<td>{display property="payment_method"}</td>
			</tr>
			{input property='items' template="items_complex.tpl"}
			<tr>
				<td colspan="3" style="text-align:right;">
					[[Sub Total]]
				</td>
				<td>
					{$GLOBALS.settings.listing_currency}{display property='sub_total'}
				</td>
			</tr>
			<tr id="tax_info" {if !$include_tax}style = "display:none"{/if}>
				<td colspan="3" style="text-align: right;">
					[[Tax]]
				</td>
				<td>
					{currencyFormat amount=$tax.tax_amount}
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;">
					[[Total]]
				</td>
				<td>
					{$GLOBALS.settings.listing_currency}{display property='total'}
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="floatRight">
						{if $user}
							<a class="grayButton" href="{$GLOBALS.site_url}/view-invoice/?sid={$invoice_sid}&amp;action=download_pdf_version">[[Download PDF Version]]</a>
							<a class="grayButton" href="{$GLOBALS.site_url}/print-invoice/?sid={$invoice_sid}&amp;action=print" target="_blank">[[Print Order]]</a>
						{/if}
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<br/><br/>
{include file="transactions_by_invoice.tpl"}
