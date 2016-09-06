<input type="hidden" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" value=""/>
<select multiple="multiple" style="display: none;" class="form-control fieldType{$id} {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][]{else}{$id}[]{/if}">
	{foreach from=$list_values item=list_value}
		<option  value="{$list_value.id}" {foreach from=$value item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >{tr mode="raw"}{$list_value.caption}{/tr|escape:"html"}</option>
	{/foreach}
</select>
{javascript}
<script type="text/javascript">
	$(document).ready(function() {
		var limit = {if !empty($choiceLimit)}{$choiceLimit}{else}null{/if};
		var name = "{if $complexField}{$complexField}[{$id}][{$complexStep}][]{else}{$id}[]{/if}";
		var fieldId = "{$id}";
		var options = {
			selectedList: 5,
			selectedText: "# {tr}selected{/tr|escape}",
			noneSelectedText: "{tr}Click to select{/tr|escape}",
			checkAllText: "",
			uncheckAllText: "",
			header: true,
			height: 'auto'
		};
		$("select[name='" + name + "']").getCustomMultiList(options, fieldId, limit);
	});
</script>
{/javascript}