{breadcrumbs}[[Customize Theme]]{/breadcrumbs}

<link rel="stylesheet" href="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/codemirror.css">

<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/codemirror.js"></script>
<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/match-highlighter.js"></script>
<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/css/css.js"></script>
<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/mode/javascript/javascript.js"></script>
<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/search.js"></script>
<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/searchcursor.js"></script>
<script src="{$GLOBALS.user_site_url}/system/ext/CodeMirror/lib/util/dialog.js"></script>
<style>
	.CodeMirror {
		min-height: 90px;
		height: auto;
	}
	.CodeMirror-scroll,
	.CodeMirror-gutters {
		min-height: 90px;
	}
	table td:first-child {
		width: 180px;
	}
	table.cke_dialog td:first-child {
		width: auto;
	}
	.theme-banner__label {
		margin-right: 50px;
	}
	.theme-banner__contents {
		margin-top: 15px;
	}
	.theme-banner__file {
		display: block;
		margin-top: 15px;
		margin-bottom: 15px;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		var cmOptions = {
			lineNumbers: true,
			matchBrackets: true,
			mode: "css",
			indentUnit: 5,
			indentWithTabs: true,
			enterMode: "keep",
			tabMode: "shift",
			theme: "default"
		};
		CodeMirror.fromTextArea(document.getElementById("custom_css"), cmOptions);

		cmOptions.mode = 'javascript';
		CodeMirror.fromTextArea(document.getElementById("custom_js"), cmOptions);

		cmOptions.mode = 'htmlmixed';
		$('.theme-banner textarea').each(function() {
			CodeMirror.fromTextArea(this, cmOptions);
		});
	});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
<script>
	$(document).ready(function() {
		$('.spectrum').each(function() {
			$(this).spectrum({
				preferredFormat: "hex",
				showPalette: true,
				showInput: true,
				palette: [
					["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
					["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
					["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
					["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
					["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
					["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
					["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
					["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
				]
			});
		});
		$("#customize-theme-pane")
			.tabs({
				select: function(event, ui){
					$('input[name="tab"]').val($(ui.panel).attr('id'));
				}
			})
			.tabs('select', {if $tab}'{$tab}'{else}0{/if});

		$('.theme-banner__label input').change(function() {
			var self = $(this);
			self.closest('.theme-banner').find('.theme-banner__contents').hide();
			if (self.val() == 'code') {
				var banner = self.closest('.theme-banner');
				banner.find('.theme-banner__contents-code').show();
				banner.find('.CodeMirror').get(0).CodeMirror.refresh();
			} else {
				self.closest('.theme-banner').find('.theme-banner__contents-image').show();
			}
		});

		$('.theme-banner__contents .deletebutton').click(function() {
			var td = $(this).closest('td');
			td.find('img').remove();
			td.find('input[value="code"]').click().change();
			td.find('input[type="text"]').val('');
			$(this).remove();
		});
	});
</script>

<h1><img src="{image}/icons/wand32.png" border="0" alt="" class="titleicon"/>[[Customize Theme]]</h1>

<form method="post" action="" enctype="multipart/form-data" class="customize-form">
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="tab" value="{$tab}" />
	{foreach from=$errors item=error}
		<p class="error">
			[[{$error}]]
		</p>
	{/foreach}

	<div id="customize-theme-pane">
		<ul class="ui-tabs-nav">
			<li class="ui-tabs-selected"><a href="#generalTab"><span>[[General Settings]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#homeTab"><span>[[Home Page]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#bannersTab"><span>[[Banners Ads]]</span></a></li>
		</ul>
		<div id="generalTab" class="ui-tabs-panel">
			<table>
				{if isset($theme_settings.logo)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Logo]]
						</td>
						<td>
							<p><img src="{$GLOBALS.user_site_url}/templates/{$settings.TEMPLATE_USER_THEME}/assets/images/{$theme_settings.logo|escape:'url'}?v={$smarty.now}" /></p><br/>
							<input type="file" name="logo" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.favicon)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Favicon]]
						</td>
						<td>
							<p>
								<img src="{$GLOBALS.user_site_url}/templates/{$settings.TEMPLATE_USER_THEME}/assets/images/{$theme_settings.favicon|escape:'url'}?v={$smarty.now}" />
							</p><br/>
							<input type="file" name="favicon" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.button_color_1)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Main Color]]
						</td>
						<td>
							<input class="spectrum" type="text" name="button_color_1" value="{$theme_settings.button_color_1}" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.button_color_2)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Accent Color 1]]
						</td>
						<td>
							<input class="spectrum" type="text" name="button_color_2" value="{$theme_settings.button_color_2}" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.button_color_3)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Accent Color 2]]
						</td>
						<td>
							<input class="spectrum" type="text" name="button_color_3" value="{$theme_settings.button_color_3}" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.font)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Font]]
						</td>
						<td>
							<select name="font">
								{foreach from=$fonts item=font key=fontId}
									<option value="{$fontId|escape}" {if $fontId == $theme_settings.font}selected="selected"{/if}>{$font.caption}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.footer)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Footer]]
						</td>
						<td>
							{WYSIWYGEditor name='footer' class='inputText' width="100%" height="150" type='ckeditor' value=$theme_settings.footer conf="BasicAdmin"}
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.custom_css)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Custom CSS]]
						</td>
						<td>
							<textarea id="custom_css" name="custom_css">{$theme_settings.custom_css|escape}</textarea>
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.custom_js)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Custom JS]]
						</td>
						<td>
							<textarea id="custom_js" name="custom_js">{$theme_settings.custom_js|escape}</textarea>
						</td>
					</tr>
				{/if}
			</table>
		</div>
		<div id="homeTab" class="ui-tabs-panel">
			<table>
				{if isset($theme_settings.main_banner)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Main Banner]]
						</td>
						<td>
							<br/>
							{if $settings.TEMPLATE_USER_THEME == "Bootstrap"}
								[[Recommended size 1350x380 px]]
							{elseif $settings.TEMPLATE_USER_THEME == "Flow"}
								[[Recommended size 1350x550 px]]
							{/if}
							<p><img class="customize-banner-img" src="{$GLOBALS.user_site_url}/templates/{$settings.TEMPLATE_USER_THEME}/assets/images/{$theme_settings.main_banner|escape:'url'}?v={$smarty.now}" /></p><br/>
							<input type="file" name="main_banner" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.main_banner_text)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Main Banner Text]]
						</td>
						<td>
							{WYSIWYGEditor name='main_banner_text' class='inputText' width="100%" height="150" type='ckeditor' value=$theme_settings.main_banner_text conf="BasicAdmin"}
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.secondary_banner)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Secondary Banner]]
						</td>
						<td>
							<br/>
							{if $settings.TEMPLATE_USER_THEME == "Bootstrap"}
								[[Recommended size 1350x390 px]]
							{elseif $settings.TEMPLATE_USER_THEME == "Flow"}
								[[Recommended size 1350x505 px]]
							{/if}
							<p><img class="customize-banner-img" src="{$GLOBALS.user_site_url}/templates/{$settings.TEMPLATE_USER_THEME}/assets/images/{$theme_settings.secondary_banner|escape:'url'}?v={$smarty.now}" /></p><br/>
							<input type="file" name="secondary_banner" />
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.secondary_banner_text)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>[[Secondary Banner Text]]</td>
						<td>
							{WYSIWYGEditor name='secondary_banner_text' class='inputText' width="100%" height="150" type='ckeditor' value=$theme_settings.secondary_banner_text conf="BasicAdmin"}
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.bottom_section_html)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>[[Bottom Section HTML]]</td>
						<td>
							{WYSIWYGEditor name='bottom_section_html' class='inputText' width="100%" height="150" type='ckeditor' value=$theme_settings.bottom_section_html conf="BasicAdmin"}
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.jobs_by_category)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>[[Display "Jobs by Category"]]</td>
						<td>
							<input type="hidden" name="jobs_by_category" value="0">
							<input type="checkbox" name="jobs_by_category" value="1" {if $theme_settings.jobs_by_category}checked="checked"{/if} >
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.jobs_by_city)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>
							[[Display "Jobs by City"]]
						</td>
						<td>
							<input type="hidden" name="jobs_by_city" value="0">
							<input type="checkbox" name="jobs_by_city" value="1" {if $theme_settings.jobs_by_city}checked="checked"{/if} >
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.jobs_by_state)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>[[Display "Jobs by State"]]</td>
						<td>
							<input type="hidden" name="jobs_by_state" value="0">
							<input type="checkbox" name="jobs_by_state" value="1" {if $theme_settings.jobs_by_state}checked="checked"{/if} >
						</td>
					</tr>
				{/if}
				{if isset($theme_settings.jobs_by_country)}
					<tr class="{cycle values='evenrow,oddrow'}">
						<td>[[Display "Jobs by Country"]]</td>
						<td>
							<input type="hidden" name="jobs_by_country" value="0">
							<input type="checkbox" name="jobs_by_country" value="1" {if $theme_settings.jobs_by_country}checked="checked"{/if} >
						</td>
					</tr>
				{/if}
			</table>
		</div>
		<div id="bannersTab" class="ui-tabs-panel">
			<table>
				<tr class="{cycle values='evenrow,oddrow'}">
					<td>
						[[Top Banner]]
					</td>
					<td class="theme-banner">
						<br />
						[[Appears at the top of all pages. Suggested size: 728x90]]
						<br /><br />
						<label class="theme-banner__label">
							<input type="radio" name="banner_top_type" {if $theme_settings.banner_top_type != 'img'}checked{/if} value="code">
							[[Html code]]
						</label>
						<label class="theme-banner__label">
							<input type="radio" name="banner_top_type" {if $theme_settings.banner_top_type == 'img'}checked{/if} value="img">
							[[Image]]
						</label>
						<div class="theme-banner__contents theme-banner__contents-code" style="{if $settings.banner_top_type == 'img'}display: none;{/if}">
							<textarea id="banner_top_code" name="banner_top_code">{$theme_settings.banner_top_code|escape}</textarea>
						</div>
						<div class="theme-banner__contents theme-banner__contents-image" style="{if $theme_settings.banner_top_type != 'img'}display: none;{/if}">
							{if $theme_settings.banner_top_img}
								<img class="theme-banner__preview-image" src="{$GLOBALS.user_site_url}/files/banners/{$theme_settings.banner_top_img|escape}"/>
							{/if}
							<div class="theme-banner__file">
								<input type="file" name="banner_top_img" /> {if $theme_settings.banner_top_img}<button type="button" class="deletebutton">[[Delete banner]]</button>{/if}
							</div>
							<p>[[Banner Link:]] <input type="text" name="banner_top_link" value="{$theme_settings.banner_top_link|escape}" /></p>
						</div>
					</td>
				</tr>
				<tr class="{cycle values='evenrow,oddrow'}">
					<td>
						[[Bottom Banners]]
					</td>
					<td class="theme-banner">
						<br />
						[[Appears at the bottom of all pages. Suggested size: 728x90]]
						<br /><br />
						<label class="theme-banner__label">
							<input type="radio" name="banner_bottom_type" {if $theme_settings.banner_bottom_type != 'img'}checked{/if} value="code">
							[[Html code]]
						</label>
						<label class="theme-banner__label">
							<input type="radio" name="banner_bottom_type" {if $theme_settings.banner_bottom_type == 'img'}checked{/if} value="img">
							[[Image]]
						</label>
						<div class="theme-banner__contents theme-banner__contents-code" style="{if $theme_settings.banner_bottom_type == 'img'}display: none;{/if}">
							<textarea id="banner_bottom_code" name="banner_bottom_code">{$theme_settings.banner_bottom_code|escape}</textarea>
						</div>
						<div class="theme-banner__contents theme-banner__contents-image" style="{if $theme_settings.banner_bottom_type != 'img'}display: none;{/if}">
							{if $theme_settings.banner_bottom_img}
								<img class="theme-banner__preview-image" src="{$GLOBALS.user_site_url}/files/banners/{$theme_settings.banner_bottom_img|escape}"/>
							{/if}
							<div class="theme-banner__file">
								<input type="file" name="banner_bottom_img" /> {if $theme_settings.banner_bottom_img}<button type="button" class="deletebutton">[[Delete banner]]</button>{/if}
							</div>
							<p>[[Banner Link:]] <input type="text" name="banner_bottom_link" value="{$theme_settings.banner_bottom_link|escape}" /></p>
						</div>
					</td>
				</tr>
				<tr class="{cycle values='evenrow,oddrow'}">
					<td>
						[[Right Side Banner]]
					</td>
					<td class="theme-banner">
						<br />
						[[Appears at the right side of all pages. Suggested size: 120x600]]
						<br /><br />
						<label class="theme-banner__label">
							<input type="radio" name="banner_right_side_type" {if $theme_settings.banner_right_side_type != 'img'}checked{/if} value="code">
							[[Html code]]
						</label>
						<label class="theme-banner__label">
							<input type="radio" name="banner_right_side_type" {if $theme_settings.banner_right_side_type == 'img'}checked{/if} value="img">
							[[Image]]
						</label>
						<div class="theme-banner__contents theme-banner__contents-code" style="{if $theme_settings.banner_right_side_type == 'img'}display: none;{/if}">
							<textarea id="banner_right_side_code" name="banner_right_side_code">{$theme_settings.banner_right_side_code|escape}</textarea>
						</div>
						<div class="theme-banner__contents theme-banner__contents-image" style="{if $theme_settings.banner_right_side_type != 'img'}display: none;{/if}">
							{if $theme_settings.banner_right_side_img}
								<img class="theme-banner__preview-image" src="{$GLOBALS.user_site_url}/files/banners/{$theme_settings.banner_right_side_img|escape}"/>
							{/if}
							<div class="theme-banner__file">
								<input type="file" name="banner_right_side_img" /> {if $theme_settings.banner_right_side_img}<button type="button" class="deletebutton">[[Delete banner]]</button>{/if}
							</div>
							<p>[[Banner Link:]] <input type="text" name="banner_right_side_link" value="{$theme_settings.banner_right_side_link|escape}" /></p>
						</div>
					</td>
				</tr>
				<tr class="{cycle values='evenrow,oddrow'}">
					<td>
						[[Inline Banner]]
					</td>
					<td class="theme-banner">
						<br />
						[[Appears inside job and resume search results. Suggested size: 600x250]]
						<br /><br />
						<label class="theme-banner__label">
							<input type="radio" name="banner_inline_type" {if $theme_settings.banner_inline_type != 'img'}checked{/if} value="code">
							[[Html code]]
						</label>
						<label class="theme-banner__label">
							<input type="radio" name="banner_inline_type" {if $theme_settings.banner_inline_type == 'img'}checked{/if} value="img">
							[[Image]]
						</label>
						<div class="theme-banner__contents theme-banner__contents-code" style="{if $theme_settings.banner_inline_type == 'img'}display: none;{/if}">
							<textarea id="banner_inline_code" name="banner_inline_code">{$theme_settings.banner_inline_code|escape}</textarea>
						</div>
						<div class="theme-banner__contents theme-banner__contents-image" style="{if $theme_settings.banner_inline_type != 'img'}display: none;{/if}">
							{if $theme_settings.banner_inline_img}
								<img class="theme-banner__preview-image" src="{$GLOBALS.user_site_url}/files/banners/{$theme_settings.banner_inline_img|escape}"/>
							{/if}
							<div class="theme-banner__file">
								<input type="file" name="banner_inline_img" /> {if $theme_settings.banner_inline_img}<button type="button" class="deletebutton">[[Delete banner]]</button>{/if}
							</div>
							<p>[[Banner Link:]] <input type="text" name="banner_inline_link" value="{$theme_settings.banner_inline_link|escape}" /></p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<p>
		<button class="grayButton">[[Save]]</button>
	</p>
</form>
