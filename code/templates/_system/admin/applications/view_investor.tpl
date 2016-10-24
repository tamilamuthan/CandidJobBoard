{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.id|lower}s/">
		[[{$listingType.name} Postings]]
	</a>
	&#187;
	[[Applications]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/> [[Applications for]] {$jobInfo.Title}</h1>

<div class="clr"><br/></div>

<table width="60%">
	<thead>
	<tr>
		<th><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=date&amp;appJobId={$jobInfo.id}&amp;order={if		$orderBy == "date" 		&& $order == "asc"}desc{else}asc{/if}">[[Date applied]]</a></th>
		<th><a href="?user_sid={$user_sid}&amp;username={$username}&amp;orderBy=applicant&amp;appJobId={$jobInfo.id}&amp;order={if	$orderBy == "applicant" && $order == "asc"}desc{else}asc{/if}">Applicant’s Name</a></th>
	</tr>
	</thead>
	<tbody>
	{foreach item=app from=$applications name=applications}
		<tr class="{cycle values='evenrow,oddrow' advance=false}">
			<td>{$app.date|date:null:true}</td>
			<td>{$app.user.FullName}</td>
		</tr>
		<tr class="{cycle values='evenrow,oddrow' }">
			<td colspan="2">
				<div class="applicationCommentsHeader">[[Cover Letter ]]:</div>
				<div class="applicationComments">
					{$app.comments|escape:'html'}
					{if $app.idea}
						<br />- <a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$app.idea}">[[Attached Idea]]</a>
					{/if}
					{if $app.file}
						<br />- <a href="?appsID={$app.id}&amp;filename={$app.file|escape:"url"}">[[View Attached File]]</a>
					{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>
