<h3 class="title__primary title__primary-small title__centered title__bordered">
	[[Credit Card Information]]
</h3>
<form method="post" action="{$form_submit_url}" class="form">
	<input type="hidden" id="action" name="action" value="BUTTON_PRESSED"/>{$hiddenFields}
	{if $errors}
		{foreach from=$errors item=error}
			<p class="alert alert-danger">[[$error]]</p>
		{/foreach}
	{/if}
	{*<h1>[[Order Information]]</h1>*}
	{*<p>[[Description]]: [[{$invoiceInfo.description}]]</p>*}
	<p>
		{foreach from=$creditCards item=card}
			<img src="{$GLOBALS.site_url}/templates/Simplicity/assets/images/creditcards/{$card}.gif" />
		{/foreach}
	</p>
	<div class="form-group">
		<label for="" class="form-label">[[Card Number]] *:</label>
		<input type="text" class="form-control" id="card_number" name="card_number" value="{$formFields.card_number}" maxLength="16"/>
		<p class="help-block">([[enter number without spaces or dashes]])</p>
	</div>
	<div class="row">
		<div class="col-sm-7 col-xs-12">
			<div class="row">
				<div class="col-sm-6 col-xs12">
					<div class="form-group">
						<label for="" class="form-label">[[Expiration month]] *</label>
						<select class="form-control" id="exp_date_mm" name="exp_date_mm">
							{foreach from=$monthList item="month"}
								<option value="{$month}" {if $formFields.exp_date_mm == $month}selected="selected"{/if}>{$month}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
						<label for="" class="form-label">[[Expiration year]] *</label>
						<select class="form-control" id="exp_date_yy" name="exp_date_yy">
							{foreach from=$yearList item="yearListItem"}
								<option value="{$yearListItem}" {if $formFields.exp_date_yy == $yearListItem}selected="selected"{/if}>{$yearListItem}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-5 col-xs-12">
			<div class="form-group">
				<label for="csc_value" class="form-label">[[Security code]]</label>
				<input type="text" class="form-control" id="csc_value" name="csc_value" value="{$formFields.csc_value}" maxLength="20"/>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="" class="form-label">[[First Name]] *</label>
		<input type="text" class="form-control" id="first_name" name="first_name" value="{$formFields.first_name}" maxLength="50"/>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[Last Name]] *</label>
		<input type="text" class="form-control" id="last_name" name="last_name" value="{$formFields.last_name}" maxLength="50"/>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[Billing Address]] *</label>
		<input type="text" class="form-control" id="address" name="address" value="{$formFields.address}" maxLength="60"/>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[Zip Code]] *</label>
		<input type="text" class="form-control" id="zip" name="zip" value="{$formFields.zip}" maxLength="60"/>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[Country]] *</label>
		<select class="form-control" id="country" name="country">
			<option value="">[[Select Country]]</option>
			{foreach from=$CountryList key="Country" item="countryCode"}
				<option value="{$countryCode}" {if $formFields.country == $countryCode}selected="selected"{/if}>{$Country}</option>
			{/foreach}
		</select>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[City]] *</label>
		<input type="text" class="form-control" id="city" name="city" value="{$formFields.city}" maxLength="40"/>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[State/Region]] *</label>
		{if in_array($selCountry, array("US", "GB", "AU", "CA"))}
			<select class="form-control" id="state" name="state">
				<option value="">[[Select State/Region]]</option>
			</select>
		{else}
			<input type="text" class="form-control" id="state" name="state" value="{$formFields.state}" maxLength="40"/>
		{/if}
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[Email]]</label>
		<input type="text" class="form-control" id="email" name="email" value="{$formFields.email}" maxLength="255"/>
	</div>
	<div class="form-group">
		<label for="" class="form-label">[[Phone Number]]</label>
		<input type="text" class="form-control" id="phone" name="phone" value="{$formFields.phone}" maxLength="25"/>
	</div>
	<div class="form-group form-group__btns text-center">
		{capture name="trPayNow"}[[Place Order]]{/capture}
		<input class="btn btn__orange btn__bold" type="submit" value="{$smarty.capture.trPayNow|escape}" />
	</div>
</form>

{javascript}
	<script type="text/javascript">
		$(function() {
			$("#country").change(function() {
				var pickedCountryCode = $(this).val();
				if ($.inArray(pickedCountryCode, ["US", "AU", "CA", "GB"]) != -1) {
					var url = "{$GLOBALS.site_url}/paypal-pro-fill-payment-card";
					var state = $('<select id="state" name="state" class="form-control"><option value="">[[Select State/Region]]</option></select>');
					$("#state").replaceWith(state);
					$.ajax({
						url: url,
						type: "POST",
						data: {
							"countryCode": pickedCountryCode
						},
						success: function(data) {
							var states = $.parseJSON(data);
							$.each(states, function(index, value) {
								if ($.isPlainObject(value)) {
									var optgroup = $("<optgroup>", {
										label: index
									});
									$.each(value, function(index, value) {
										$("<option>", {
											value: value,
											text: index
										}).appendTo(optgroup);
									});
									optgroup.appendTo($("#state"));
								} else {
									$("<option>", {
										value: value,
										text: index
									}).appendTo($("#state"));
								}
							});
						}
					});
				} else {
					$("#state").replaceWith($('<input name="state" class="form-control" id="state">'));
				}
			}).change();
		})
	</script>
{/javascript}