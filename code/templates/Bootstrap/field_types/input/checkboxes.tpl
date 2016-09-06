<div>
	<input type="hidden" name="{$id}" value=""/>
	{foreach from=$list_values item=list_value}
		<input class="checkBox{$id}" type="checkbox" name="{$id}[]" {foreach from=$value item=value_id}{if $list_value.id == $value_id}checked="checked"{/if}{/foreach} value="{$list_value.id}" /><span>&nbsp;{tr}{$list_value.caption}{/tr|escape:'html'}</span><br/>
	{/foreach}
</div>
{if $comment}
	<span class="small">[[{$comment}]].</span>
{/if}
{javascript}
	<script type="text/javascript">
		$(document).ready(function() {
			var limit{$id} = {if !empty($choiceLimit)}{$choiceLimit}{else}null{/if};
			if (limit{$id}) {
				$(".checkBox{$id}").bind("change", function() {
					if($(this).siblings(':checked').length >= limit{$id}) {
						this.checked = false;
					}
				});
			}
		});
	</script>
{/javascript}