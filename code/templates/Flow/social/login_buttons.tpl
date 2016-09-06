<div>
	<span>[[Connect with social network]]:</span>
	{foreach from=$aSocPlugins item="plugin"}
		<a href="{$GLOBALS.site_url}/social/?network={$plugin.id}{if $user_group_id}&amp;user_group_id={$user_group_id}{/if}" title="[[Connect using $plugin.name]]"></a>
	{/foreach}
</div>
