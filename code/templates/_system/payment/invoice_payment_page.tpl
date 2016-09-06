{foreach from=$checkPaymentErrors key=error item=value}
	{if $error == 'NOT_OWNER'}
		<p class="alert alert-danger">[[You're not the owner of this payment]]</p>
	{elseif $error == 'NOT_LOGGED_IN'}
		{module name="users" function="login"}
	{elseif $error == 'WRONG_INVOICE_PARAMETERS'}
		<p class="alert alert-danger">[[Invoice contains wrong parameters. Check all items listed in it.]]</p>
	{elseif $error == 'PROMOTION_TOO_MANY_USES'}
		<p class="alert alert-danger">[[Discount code that was applied to this invoice is expired. Invoice cannot be paid for. Please generate a new invoice by purchasing product(s) again.]]</p>
	{/if}
	{foreachelse}
	<div class="payment-proceed--preloader"></div>
	<div class="row hidden">
		<div class="col-sm-pull-3 col-sm-9 col-xs-12">
			<div class="form-group">
				<label>[[How would you like to pay]]</label>
				<select name="" id="payment-gateway__selector" class="form-control">
					{foreach from=$gateways item="gateway" key="gatewayID" name="gateways"}
						{capture name="trGatewayCaption"}[[{$gateway.caption}]]{/capture}
						<option value="{$gatewayID}">{$smarty.capture.trGatewayCaption|escape:'html'}</option>
					{/foreach}
				</select>
			</div>
			{foreach from=$gateways item="gateway" key="gatewayID" name="gateways"}
				<div class="payment-gateway__contents payment-gateway__contents-{$gatewayID}">
					<form action="{$gateway.url}" method="post" id="form_{$gatewayID}" onsubmit="disableSubmitButton('submit_{$gatewayID}');">
						{$gateway.hidden_fields}
						{if $selectedGateway == $gatewayID}
							{javascript}
								<script type="text/javascript">
									$(document).ready(function(){
										$("#form_" + "{$gatewayID}").submit();
									});
								</script>
							{/javascript}
						{/if}
						<div class="form-group">
							<input type="submit" value="[[Place Order]]" class="btn btn-default" id="submit_{$gatewayID}"/>
						</div>
					</form>
				</div>
			{/foreach}
		</div>
	</div>
{/foreach}