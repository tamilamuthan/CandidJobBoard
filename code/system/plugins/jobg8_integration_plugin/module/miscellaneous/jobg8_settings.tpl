{breadcrumbs}<a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/">[[Plugins]]</a> &#187; [[JobG8 Integration Plugin Settings]]{/breadcrumbs}
<h1><img src="{image}/icons/plug32.png" border="0" alt="" class="titleicon"/>[[JobG8 Integration Plugin Settings]]</h1>

{if empty($GLOBALS.settings.jobg8Installed)}
	<p><a href="{$GLOBALS.site_url}{$GLOBALS.user_page_uri}?action=install" class="editbutton">[[Installation]]</a></p>
{else}
	<form method="post">
		<input type="hidden" name="action" value="saveSettings">
		<input type="hidden" id="submit" name="submit" value="">
		<h3>[[Receiving Jobs]]</h3>
		<table class="mapping">
			<td><p>[[To make sure that Jobg8 field values fit your job board fields you should do mapping. Use this mapping tool in order to map Jobg8 fields with your job board fields.]]</p><br/><a href="{$GLOBALS.site_url}{$GLOBALS.user_page_uri}?action=mapping" class="editbutton">[[Mapping]]</a></td>
		</table>
		<br />
		<h3>[[Posting Jobs]]</h3>

		<div class="jobG8PostingJobsTypeBlock">
			<table  class="basetable" width="100%">
				<tr class="headrow">
					<td style="width: 342px;">[[Name]]</td>
					<td>[[Value]]</td>
				</tr>
				{foreach from=$settings.types item=jobPostingTypeSettings key=jobPostingTypeId name=jobPostingTypeSettings}
					<tr class="evenrow">
						{assign var="settingId" value="jobG8Buy{$jobPostingTypeId}Status"}
						<td><label for="{$settingId}">[[Buy $jobPostingTypeId]]:</label></td>
						<td>
							<select id="{$settingId}" class="jobG8PostingJobsTypeSwitches" data-id="{$smarty.foreach.jobPostingTypeSettings.index}" name="{$settingId}">
								<option value="0" {if $savedSettings.$settingId == '0'}selected="selected" {/if}>[[disabled]]</option>
								<option value="1" {if $savedSettings.$settingId == '1'}selected="selected" {/if}>[[enabled]]</option>
							</select>
						</td>
					</tr>
				{/foreach}
				{foreach from=$settings.common item=setting}
					<tr class="evenrow">
						<td><label for="{$setting.id}">[[$setting.caption]]:</label></td>
						<td>
							<input type="text" name="{$setting.id}" value="{$savedSettings.{$setting.id}}" />
						</td>
					</tr>
				{/foreach}
			</table>
		</div>

		<div id="settingsPane">
			<ul class="ui-tabs-nav" style="width: 682px;">
				<li class="ui-tabs-selected"><a href="#buyApplicationsTab"><span>[[Buy Applications]]</span></a></li>
				<li class="ui-tabs-unselect"><a href="#buyTrafficTab"><span>[[Buy Traffic]]</span></a></li>
			</ul>
			{foreach from=$settings.types item=jobPostingTypeSettings key=jobPostingTypeId}
				<div id="buy{$jobPostingTypeId}Tab" style="width: 660px;" class="ui-tabs-panel">
					<table  class="basetable" width="100%" id="jobg8-plugin">
						<tr class="headrow">
							<td>[[Name]]</td>
							<td>[[Value]]</td>
						</tr>
						{foreach from=$jobPostingTypeSettings item=pluginSettings}
							{assign var='settingID' value=$pluginSettings.id}
							{if $pluginSettings.type == 'separator'}
								<tr class="separator">
									<td colspan="2">
										{if $pluginSettings.caption}<strong>[[{$pluginSettings.caption}]]</strong>{else}&nbsp;{/if}
										{if $pluginSettings.comment}<br /><small>[[{$pluginSettings.comment}]]</small>{/if}
									</td>
								</tr>
							{else}
								{assign var="jobg8_company_list" value="{$jobPostingTypeId|lower}_jobg8_company_list"}
								{assign var="jobg8_company_name_filter" value="{$jobPostingTypeId|lower}_jobg8_company_name_filter"}
								{assign var="jobg8_product_list" value="{$jobPostingTypeId|lower}_jobg8_product_list"}
								{assign var="jobg8_product_filter" value="{$jobPostingTypeId|lower}_jobg8_product_filter"}
								{assign var="jobg8_job_category_list" value="{$jobPostingTypeId|lower}_jobg8_job_category_list"}
								{assign var="jobg8_job_category_filter" value="{$jobPostingTypeId|lower}_jobg8_job_category_filter"}
								{if $pluginSettings.id == $jobg8_company_list}
								{elseif $pluginSettings.id == $jobg8_company_name_filter}
									{foreach from=$jobPostingTypeSettings item='current'}
										{if $current.id == $jobg8_company_list}{assign var='companyList' value=$current}{/if}
									{/foreach}
									<tr class="{cycle values = 'evenrow,oddrow'}">
										<td class="filter-description">
											<input type="hidden" name="{$settingID}" value="0" /><input type="checkbox" name="{$settingID}" value="1" {if $savedSettings.$settingID}checked="checked" {/if} />
											<span>[[{$pluginSettings.caption}]]</span>
										</td>
										<td>
											<textarea name="{$companyList.id}">{$savedSettings.$jobg8_company_list}</textarea>
										</td>
									</tr>
								{elseif $pluginSettings.id == $jobg8_product_list}
								{elseif $pluginSettings.id == $jobg8_product_filter}
									{foreach from=$jobPostingTypeSettings item='current'}
										{if $current.id == $jobg8_product_list}{assign var='productList' value=$current}{/if}
									{/foreach}
									<tr class="{cycle values = 'evenrow,oddrow'}">
										<td class="filter-description">
											<input type="hidden" name="{$settingID}" value="0" /><input type="checkbox" name="{$settingID}" value="1" {if $savedSettings.$settingID}checked="checked" {/if} />
											<span>[[{$pluginSettings.caption}]]</span>
										</td>
										<td>
											{assign var='selectedItems' value=$savedSettings.$jobg8_product_list}
											<select name="{$productList.id}[]" multiple="multiple">
												{foreach from=$productList.list_values item='list'}
													<option value="{$list.id}" {if in_array($list.id, explode(',', $selectedItems))}selected{/if}>{$list.caption}</option>
												{/foreach}
											</select>
											{if $productList.comment}
												<br/><small>[[{$productList.comment}]]</small>
											{/if}
										</td>
									</tr>
								{elseif $pluginSettings.id == $jobg8_job_category_list}
								{elseif $pluginSettings.id == $jobg8_job_category_filter}
									{foreach from=$jobPostingTypeSettings item='current'}
										{if $current.id == $jobg8_job_category_list}{assign var='categoryList' value=$current}{/if}
									{/foreach}
									<tr class="{cycle values = 'evenrow,oddrow'}">
										<td class="filter-description">
											<input type="hidden" name="{$settingID}" value="0" /><input type="checkbox" name="{$settingID}" value="1" {if $savedSettings.$settingID}checked="checked" {/if} />
											<span>[[{$pluginSettings.caption}]]</span>
										</td>
										<td>
											{assign var='selectedItems' value=$savedSettings.$jobg8_job_category_list}
											<select name="{$categoryList.id}[]" multiple="multiple">
												{foreach from=$categoryList.list_values item='list'}
													<option value="{$list.id}" {if in_array($list.id, explode(',', $selectedItems))}selected{/if}>{$list.caption}</option>
												{/foreach}
											</select>
											{if $categoryList.comment}
												<br/><small>[[{$categoryList.comment}]]</small>
											{/if}
										</td>
									</tr>
								{else}
									<tr class="{cycle values = 'evenrow,oddrow'}">
										<td>[[{$pluginSettings.caption}]]</td>
										<td>{$pluginSetting.tabName.id}
											{if $pluginSettings.type == 'boolean'}
												<input type="hidden" name="{$settingID}" value="0" /><input type="checkbox" name="{$settingID}" value="1" {if $savedSettings.$settingID}checked="checked" {/if} />
											{elseif  $pluginSettings.type == 'string'}
												<input type="text" name="{$pluginSettings.id}" value="{$savedSettings.$settingID}" />
											{elseif  $pluginSettings.type == 'text'}
												<textarea name="{$pluginSettings.id}" style="height: 150px;">{$savedSettings.$settingID}</textarea>
											{elseif  $pluginSettings.type == 'list'}
												<select name="{$pluginSettings.id}">
													{foreach from=$pluginSettings.list_values item=list}
														<option value="{$list.id}" {if $savedSettings.$settingID == $list.id}selected="selected" {/if}>{$list.caption}</option>
													{/foreach}
												</select>
											{elseif  $pluginSettings.type == 'multilist'}
												<select name="{$pluginSettings.id}[]" multiple="multiple">
													{assign var=selectedItems value=$savedSettings.$settingID}
													{foreach from=$pluginSettings.list_values item=list}
														<option value="{$list.id}" {if in_array($list.id, explode(',', $selectedItems))}selected{/if}>{$list.caption}</option>
													{/foreach}
												</select>
											{/if}
											{if $pluginSettings.comment}
												<br/><small>[[{$pluginSettings.comment}]]</small>
											{/if}
										</td>
									</tr>
								{/if}
							{/if}
						{/foreach}
						<tr id="clearTable">
							<td colspan="2" align="right">
								<div class="floatRight" style="margin-top: 10px;">
									<input type="submit" class="grayButton" value="[[Apply]]" data-value="apply" />
									<input type="submit" class="grayButton" value="[[Save]]" data-value="save" />
								</div>
							</td>
						</tr>
					</table>
				</div>
			{/foreach}
		</div>
	</form>
{/if}
<script type="text/javascript">
	$(function() {
		$("#settingsPane").tabs();
	});
	$("input[type='submit']").click(function() {
		$("#submit").val($(this).attr("data-value"));
	});
</script>