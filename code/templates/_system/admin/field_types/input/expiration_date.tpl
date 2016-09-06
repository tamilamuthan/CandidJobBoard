{if !isset($extraInfo.listing_duration)}
	{$extraInfo.listing_duration = (isset($contract['listing_duration'])) ? $contract['listing_duration'] : $productInfo['listing_duration']}
{/if}

{if $extraInfo.listing_duration}
	{if $listing['activation_date']}
		{$maxExpirationDate = strftime("{$GLOBALS.current_language_data.date_format}", strtotime("+{$extraInfo.listing_duration} day", strtotime($listing['activation_date'])))}
	{else}
		{$maxExpirationDate = strftime("{$GLOBALS.current_language_data.date_format}", strtotime("+{$extraInfo.listing_duration} day"))}
	{/if}
{/if}
<input type="text" id="{$id}" readonly value="{tr type="date"}{if $mysql_date}{$mysql_date}{else}{$value}{/if}{/tr}" class="input_date displayDate" name="{$id}"/>

<script type="text/javascript">
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	var maxExpirationDate = '{$maxExpirationDate}';
	var id = '{$id}';
	var listingDuration = '{$extraInfo.listing_duration}';
	var expired = '{$expired}';
	if (expired) {
		$("#" + id).datepicker('hide');
	} else {
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");
		var dp = $("#" + id).datepicker({
			dateFormat: dFormat,
			showOn: 'button',
			changeMonth: true,
			changeYear: true,
			minDate: '+1d',
			yearRange: '-99:+99',
			buttonImage: '{$GLOBALS.user_site_url}/system/ext/jquery/calendar.gif',
			buttonImageOnly: true
		});
		if (listingDuration) {
			dp.datepicker("option", "maxDate", maxExpirationDate);
		}
		if (dp.val() == '') {
			if (maxExpirationDate) {
				dp.datepicker('setDate', maxExpirationDate);
			} else {
				dp.datepicker('setDate', '+1y');
			}
		}
	}
</script>
