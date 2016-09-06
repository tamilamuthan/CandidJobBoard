{breadcrumbs}[[Job Alerts]]{/breadcrumbs}
<h1><img src="{image}users-online.png" border="0" alt="" class="titleicon" />[[Job Alerts]]</h1>
<div class="right">
	<form method="post" action="{$GLOBALS.site_url}/guest-alerts/export/">
		<input type="hidden" name="sorting_field" value="{$paginationInfo.sortingField}"/>
		<input type="hidden" name="sorting_order" value="{$paginationInfo.sortingOrder}"/>
		<input type="submit" name="export" value="[[Export Job Alerts]]" class="grayButton" /> &nbsp;
		<select name="type" style="width:60px;">
			<option value="csv">CSV</option>
			<option value="xls">XLS</option>
		</select>
	</form>
</div>

<div class="setting_button" id="mediumButton">
	<strong>[[Filter Job Alerts]]</strong>
	<div class="setting_icon"><div id="accordeonClosed"></div></div>
</div>

<div class="setting_block" style="display: none"  id="clearTable">
	<form method="get" name="search_form">
		<table  width="100%">
			<tr><td>[[Email]]:</td><td>{search property="email" template="string.like.tpl"}</td></tr>
			<tr><td>[[Frequency]]:</td><td>{search property="email_frequency"}</td></tr>
			<tr><td>[[Signed Up]]:</td><td>{search property="signed_up"}</td></tr>
			<tr><td>[[Status]]:</td><td>{search property="status"}</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div class="floatRight">
						<input type="hidden" name="action" value="search" />
						<input type="hidden" name="page" value="1" />
						<input type="submit" value="[[Search]]" class="grayButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
	$( function () {
		var dFormat = '{$GLOBALS.current_language_data.date_format}';
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");
		
		$("#subscription_date_notless, #subscription_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});
		
		$(".setting_button").click(function(){
			var butt = $(this);
			$(this).next(".setting_block").slideToggle("normal", function(){
				if ($(this).css("display") == "block") {
					butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
				} else {
					butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
				}
			});
		});
	});
</script>

