{foreach from=$value item=list_value name="multifor"}
    <span class="job-type__value">
		{if $display_list_values.$list_value}
            {tr}{$display_list_values.$list_value}{/tr|escape}
        {else}
            {tr}{$list_value}{/tr|escape}
        {/if}
	</span>
{/foreach}