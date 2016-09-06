{breadcrumbs}[[Languages]]{/breadcrumbs}
<h1><img src="{image}/icons/exchange32.png" border="0" alt="" class="titleicon"/>[[Languages]]</h1>

{if $errors}
	{include file="errors.tpl" errors=$errors}
{/if}

<table>
	<thead>
		<tr>
			<th>[[Language]]</th>
			<th>[[Active]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
	{foreach from=$langs item=lang}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>
				{$lang.caption}
			</td>
			<td>
				{if $lang.activeFrontend}
					[[Yes]]
				{else}
					[[No]]
				{/if}
			</td>
			<td>
				<a href="{$GLOBALS.site_url}/manage-phrases/?language={$lang.id}&action=search_phrases" class="grayButton">[[Manage Phrases]]</a>
				{if $lang.activeFrontend}
					{if not $lang.is_default}
						<a href="{$GLOBALS.site_url}/manage-languages/?language={$lang.id}&action=deactivate" class="deletebutton">[[Deactivate]]</a>
					{/if}
				{else}
					<a href="{$GLOBALS.site_url}/manage-languages/?language={$lang.id}&action=activate" class="grayButton">[[Activate]]</a>
				{/if}
			</td>
		</tr>
	{/foreach}
</table>