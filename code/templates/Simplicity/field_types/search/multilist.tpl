<div>
	<select multiple="multiple" id="{$id}" class="form-control" name="{$id}[multi_like][]">
		{foreach from=$list_values item=list_value}
			<option value="{$list_value.id}" {foreach from=$value.multi_like item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} {foreach from=$value.multi_like_and item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach}>
				{tr mode="raw"}{$list_value.caption}{/tr|escape:"html"}
			</option>
		{/foreach}
	</select>
</div>
{javascript}
	<script type="text/javascript">
		$(document).ready(function() {
			var name = "{$id}[multi_like][]";
			var options = {
				selectedList: 3,
				selectedText: "# {tr}selected{/tr|escape:'html'}",
				noneSelectedText: "{tr}Click to select{/tr|escape:'html'}",
				checkAllText: "{tr}Select all{/tr|escape:'html'}",
				uncheckAllText: "{tr}Deselect all{/tr|escape:'html'}",
				header: true,
				height: 'auto',
				minWidth: 316
			};
			$("select[name='" + name + "']").getCustomMultiList(options, "{$id}", null);
		});
	</script>
{/javascript}
