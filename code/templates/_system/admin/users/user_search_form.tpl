{breadcrumbs}[[{$userGroupInfo.name} Profiles]]{/breadcrumbs}
<h1><img src="{image}users-online.png" border="0" alt="" class="titleicon" />[[{$userGroupInfo.name} Profiles]]</h1>
<div class="right">
	<a href="{$GLOBALS.site_url}/import-users/?user_group_id={$userGroupInfo.id}" class="grayButton">[[Import {$userGroupInfo.name}s]]</a>
	<a href="{$GLOBALS.site_url}/export-users/?user_group_id={$userGroupInfo.id}" class="grayButton">[[Export {$userGroupInfo.name}s]]</a>
	<a href="{$GLOBALS.site_url}/add-user/{$userGroupInfo.id|lower}" class="grayButton" style="margin-left: 15px !important;">[[Add New {$userGroupInfo.name}]]</a>
</div>

<div class="setting_button" id="mediumButton"><strong>[[Filter {$userGroupInfo.name}s]]</strong><div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div class="setting_block" style="display: none"  id="clearTable">
	<form method="get" name="search_form">
		<table  width="100%">
			{if $userGroupInfo.id == 'Employer'}
               <tr><td>[[Company Name]]:</td><td>{search property="CompanyName" template="string.like.tpl"}</td></tr>
            {/if}
			<tr><td>[[Email]]:</td><td>{search property="username" template="string.like.tpl"}</td></tr>
			<tr>
				<td>[[Product Purchased]]:</td>
				<td>
					<select name="product[simple_equal]">
						<option value="">[[Any Product]]</option>
						{foreach from=$products item=product}
							<option value="{$product.sid}" {if $selectedProduct eq $product.sid}selected="selected"{/if}>[[{$product.name}]]</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr><td>[[Status]]:</td><td>{search property="active"}</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<div class="floatRight">
						<input type="hidden" name="action" value="search" />
						<input type="hidden" name="page" value="1" />
						<input type="submit" value="[[Filter]]" class="grayButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
	$( function () {
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

