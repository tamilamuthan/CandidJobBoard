{breadcrumbs}[[Navigation Menu]]{/breadcrumbs}



<style>
	.sortable-handle {
		cursor: pointer;
		transform: rotate(90deg);
	}
	.prototype {
		display: none;
	}
</style>

<form method="post" action="">
	<div style="display: inline-block">
		<h1>
			<img src="{image}/icons/wand32.png" border="0" alt="" class="titleicon"/>
			[[Navigation Menu]]
			<button type="button" class="grayButton add-menu-item right">[[Add Menu Item]]</button>
		</h1>

		<table>
			<thead>
			<tr>
				<th></th>
				<th>[[Name]]</th>
				<th colspan="3">[[Link]]</th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$menuItems item='menuItem'}
				{assign var='selected' value=false}
				<tr>
					<td class="sortable-handle">...</td>
					<td><input type="text" name="menu_item[]" value="{$menuItem.name|escape}" /></td>
					<td>
						<select class="page-selector">
							{foreach from=$system_pages item='page' key='uri'}
								<option value="{$uri|escape}" {if $menuItem.url == $uri}selected="selected" {assign var='selected' value=true}{/if}>{$page}</option>
							{/foreach}
							{foreach from=$pages item='page'}
								<option value="{$page.uri|escape}" {if $menuItem.url == $page.uri}selected="selected" {assign var='selected' value=true}{/if}>{$page.title}</option>
							{/foreach}
							<option value="" {if $selected == false}selected="selected{/if}">[[External Page]]</option>
						</select>
					</td>
					<td style="min-width: 260px;">
						<div class="external-link">
							<input type="text" name="link[]" value="{$menuItem.url|escape}" />
						</div>
					</td>
					<td><button type="button" class="deletebutton">[[Delete]]</button></td>
				</tr>
			{/foreach}
			<tr class="prototype">
				<td class="sortable-handle">...</td>
				<td><input type="text" name="menu_item[]" value=""></td>
				<td>
					<select class="page-selector">
						{foreach from=$system_pages item='page' key='uri'}
							<option value="{$uri|escape}">{$page}</option>
						{/foreach}
						{foreach from=$pages item='page'}
							<option value="{$page.uri|escape}">{$page.title}</option>
						{/foreach}
						<option value="" selected="selected">[[External Page]]</option>
					</select>
				</td>
				<td style="min-width: 260px;">
					<div class="external-link" style="display: none;">
						<input type="text" name="link[]" value="http://" />
					</div>
				</td>
				<td><button type="button" class="deletebutton">[[Delete]]</button></td>
			</tr>
			</tbody>
		</table>
		<p>
			<button class="grayButton right">[[Save]]</button>
		</p>
	</div>
</form>
<script>
	$(document).ready(function() {
		$('.deletebutton').live('click', function() {
			$(this).closest('tr').remove();
		});
		var init = false;
		$('.page-selector').live('change', function() {
			var row = $(this).closest('tr');
			row.find('.external-link').toggle($(this).val() == '');
			if (init) {
				row.find('.external-link').find('input').val($(this).val() == '' ? 'http://' : $(this).val());
				if ($(this).val() != '') {
					row.find('[name="menu_item[]"]').val($(this).find('option:selected').text());
				} else {
					row.find('[name="menu_item[]"]').val('');
				}
			}
		}).change();
		init = true;

		$('tbody').sortable({
			helper: function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			},
			handle: '.sortable-handle'
		});
		$('.sortable-handle').disableSelection();

		$('.add-menu-item').click(function() {
			$('.prototype').clone().insertBefore($('.prototype')).removeClass('prototype');
		});
	});
</script>