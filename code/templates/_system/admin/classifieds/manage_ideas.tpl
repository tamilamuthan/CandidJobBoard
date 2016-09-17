{breadcrumbs}
	[[Idea Postings]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[Idea Postings]]</h1>
<div class="right">
	<a href="{$GLOBALS.site_url}/import-listings/?listing_type_id={$listingsType.id}" class="grayButton">[[Import Ideas]]</a>
	<a href="{$GLOBALS.site_url}/export-listings/?listing_type_id={$listingsType.id}" class="grayButton">[[Export Ideas]]</a>
	<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingsType.id|lower}" class="grayButton" style="margin-left: 15px !important;">[[Add New Idea Listing]]</a>
</div>

<div class="setting_button" id="mediumButton">
	[[Filter Ideas]]
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
				<td>[[Posting Date]]:</td>
				<td>{search property="PostingDate" template='list.date.tpl'}</td>
			</tr>
			<tr>
				<td>
					[[Entrepreneur]]:
				</td>
				<td>
					<input type="text" value="{if $companyName}{$companyName|escape:'html'}{/if}" name="company_name[like]" placeholder="[[Entrepreneur name or email]]" />
				</td>
			</tr>
			<tr>
				<td>[[Posted with]]: </td>
				<td>{search property="product_info_sid" template="list.like.tpl"}</td>
			</tr>
			<tr>
				<td>[[Status]]: </td>
				<td>
					{search property="active"}
				</td>
			</tr>
			<tr>
				<td>[[Featured]]: </td>
				<td>{search property="featured"}</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="floatRight">
						<button type="submit" value="Find" class="greenButton">[[Filter]]</button>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
	$(function() {
		$(".setting_button").click(function() {
			var butt = $(this);
			$(this).next(".setting_block").slideToggle("normal", function() {
				if ($(this).css("display") == "block") {
					butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
				} else {
					butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
				}
			});
		});
	});
</script>