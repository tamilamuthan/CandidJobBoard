{breadcrumbs}
	[[{$listingsType.name}s]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[{$listingsType.name}s]]</h1>
<div class="right">
	<a href="{$GLOBALS.site_url}/import-listings/?listing_type_id={$listingsType.id}" class="grayButton">[[Import Resumes]]</a>
	<a href="{$GLOBALS.site_url}/export-listings/?listing_type_id={$listingsType.id}" class="grayButton">[[Export Resumes]]</a>
	<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingsType.id|lower}" class="grayButton">[[Add New {$listingsType.name}]]</a>
</div>

<div class="setting_button" id="mediumButton">
	[[Filter Resumes]]
	<div class="setting_icon">
		<div id="accordeonClosed"></div>
	</div>
</div>
<div class="setting_block" style="display: none" id="clearTable">
		<form method="post" name="search_form">
			<input type="hidden" name="action" value="search" />
			<input type="hidden" name="page" value="1" />
			<table  width="100%">
				<tr>
					<td>[[Keywords]]: </td>
					<td><input type="text" value="{if $idKeyword}{$idKeyword|escape:'html'}{/if}" name="idKeyword" id="idkeyword"></td>
				</tr>
				<tr>
					<td>
						[[Job Seeker Email]]:
					</td>
					<td>
						<input type="text" value="{if $companyName}{$companyName|escape:'html'}{/if}" name="company_name[like]" />
					</td>
				</tr>
				<tr>
					<td>[[Posting Date]]:</td>
					<td>{search property="PostingDate" template='list.date.tpl'}</td>
				</tr>
				<tr>
					<td>[[Status]]: </td>
					<td>
						{search property="active"}
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="floatRight">
							<input type="submit" value="[[Filter]]" class="greenButton" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>

<script>
	$(function() {
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