{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-feeds/">[[RSS/XML Feeds]]</a> &#187; [[Customize XML Feed]]{/breadcrumbs}
<h1><img src="{image}/icons/rss32.png" border="0" alt="" class="titleicon"/>[[Customize XML Feed]]</h1>
<p>[[Please use filtering criteria below to filter jobs that will appear in your feed.]]</p>

<form class="custom-feed__form">
	<table>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Keywords]]</td>
			<td><input type="text" name="keywords" value=""/></td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Categories]]</td>
			<td>
				<select name="categories" multiple="multiple">
					{foreach item='item' from=$categories}
						<option value="{$item.id}">{$item.caption|escape}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Job Type]]</td>
			<td>
				<select name="job_type">
					<option value=""></option>
					{foreach item='item' from=$job_types}
						<option value="{$item.id}">{$item.caption|escape}</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Location]]</td>
			<td>
				<input type="text" name="location" value=""/>
				<select name="radius">
					{foreach item='value' from=$radius.values}
						<option value="{$value}" {if $value == $radius.default}selected="selected"{/if}>[[within]] {$value} {$GLOBALS.settings.radius_search_unit}</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Featured Jobs]]</td>
			<td>
				<input type="checkbox" name="featured" value="1"/>
			</td>
		</tr>

		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Products]]</td>
			<td>
				<select name="products" multiple="multiple">
					{foreach item='item' from=$products}
						<option value="{$item.sid}">{$item.name|escape}</option>
					{/foreach}
				</select>
			</td>
		</tr>

		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Exclude Imported Jobs]]</td>
			<td>
				<input type="checkbox" name="exclude_imported" value="1" {if $feed.id == 'indeed'}disabled checked{/if}/>
			</td>
		</tr>

		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Jobs Limit]]</td>
			<td>
				<select name="limit">
					{foreach item='limit' from=$limits.values}
						<option value="{$limit}" {if $limits.default == $limit}selected="selected"{/if}>{$limit} [[jobs]]</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr class="custom-feed__empty-row">
			<td colspan="2">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h2>[[Custom Feed URL:]]</h2>
				<p class="custom-feed__url" data-url="{$GLOBALS.custom_domain_url}/feeds/{$feed.id}.xml">
					{$GLOBALS.custom_domain_url}/feeds/{$feed.id}.xml
				</p>
				<input type="text" class="custom-feed__text">
				<button type="button" class="custom-feed__copy grayButton">[[Copy Feed URL]]</button>
			</td>
		</tr>
	</table>
</form>

<script>
	$(document).ready(function() {
		$('.custom-feed__url').click(function() {
			var range = document.createRange();
			range.selectNodeContents(this);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		});
		$('.custom-feed__copy').click(function() {
			$('.custom-feed__text').val($.trim($('.custom-feed__url').text()));
			$('.custom-feed__text').select();
			document.execCommand('copy');
			$(this).focus();
		});
		$('.custom-feed__form :input').bind('keydown change', function() {
			setTimeout(function() {
				var urlElement = $('.custom-feed__url');
				var formData = $('.custom-feed__form').serializeArray();
				var data = {};
				$.each(formData, function() {
					if (this.name == 'limit' && this.value == '{$limits.default}') {
						return;
					}
					if (this.name == 'radius' && this.value == '{$radius.default}') {
						return;
					}
					if ($.trim(this.value) == '') {
						return;
					}
					if (!data[this.name]) {
						data[this.name] = this.value;
					} else {
						data[this.name] += ',' + this.value;
					}
				});
				var url = urlElement.data('url') + '?' + $.param(data);
				url = url.replace(/%2C/g, ',');
				urlElement.text(url);
			}, 0);
		});

		var options = {
			selectedList: 3,
			selectedText: "# {tr}selected{/tr|escape:'html'}",
			noneSelectedText: "{tr}Click to select{/tr|escape:'html'}",
			checkAllText: "{tr}Select all{/tr|escape:'html'}",
			uncheckAllText: "{tr}Deselect all{/tr|escape:'html'}",
			header: true,
			height: 'auto',
			minWidth: 209
		};
		$('select[name="categories"]').getCustomMultiList(options, 'categories');
		$('select[name="products"]').getCustomMultiList(options, 'products');
	});
</script>

<style>
	.custom-feed__text {
		background-color: transparent;
		position: fixed;
		border: none !important;
		outline: none;
		box-shadow: none;
		width: 1px !important;
		height: 1px !important;
		padding: 0;
	}
	.custom-feed__url {
		max-width: 500px;
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
	}
	.custom-feed__empty-row td,
	.custom-feed__empty-row tr {
		border-left: 1px solid white !important;
		border-right: 1px solid white !important;
	}
</style>