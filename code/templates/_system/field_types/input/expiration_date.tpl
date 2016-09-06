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
<input readonly type="text" id="{$id}" value="{if $mysql_date}{$mysql_date|escape|date_format:"%m.%d.%Y"}{elseif !$value}{$maxExpirationDate}{else}{$value|date_format:"%m.%d.%Y"}{/if}" class="form-control input-date" name="{$id}"/>
<img class="ui-datepicker-trigger" src="{$GLOBALS.user_site_url}/templates/Bootstrap/assets/images/icon-calendar.svg" alt="..." title="...">
{javascript}
	<script type="text/javascript">
		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		var maxExpirationDate = '{$maxExpirationDate}';
		var id = '{$id}';

		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yyyy");

		$("#" + id).datepicker({
			language: '{$GLOBALS.current_language}',
			autoclose: true,
			todayHighlight: true,
			format: dFormat,
			startDate: '+1d',
			endDate: maxExpirationDate
		});

		$('.ui-datepicker-trigger').on('click', function () {
			$(this).closest('.form-group').find('.form-control').focus();
		});
	</script>
{/javascript}