{breadcrumbs}<a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/">[[Plugins]]</a> &#187; <a href="{$GLOBALS.site_url}{$GLOBALS.user_page_uri}">[[JobG8 Integration Plugin Settings]]</a> &#187; [[Mapping]]{/breadcrumbs}
<h1><img src="{image}/icons/plug32.png" border="0" alt="" class="titleicon"/>[[Mapping]]</h1>
{if $errors}
	{foreach from=$errors item="error"}
		<p id="mapping-error" class="error">{$error}</p>
	{/foreach}
{/if}
<fieldset id="mapping-instruction">
	<legend>[[Mapping Instructions]]</legend>
	<p>[[To make sure that Jobg8 field values fit your job board fields you should do mapping. Use this mapping tool in order to map Jobg8 field values with your job board field values.<br/><br/>In the first column you see Jobg8 field values. You need to select the best match for these values from the second column where your field values are displayed. The selected values will be used for posting of Jobg8 jobs on your site instead of Jobg8 values. Do not forget to press ‘Save’ button after you have mapped all the fields.<br/><br/>If you want to change the mapping field you should enter the ID of this field (not caption) in ‘SJB field:’ and click ‘Change Field’ button. In case you do not want Jobg8 jobs with certain field values to be posted on your site you can deactivate this field by unchecking it in the first column.]]</p>
</fieldset>
<form method="post" class="mapping">
	<input type="hidden" name="action" value="mapping" />
	<input type="hidden" id="type" name="type" value="" />
	<input type="hidden" id="mappingField" name="mappingField" value="" />
	<input type="hidden" id="submit" name="submit" value="" />
	<input type="hidden" id="changeMappingField" name="changeMappingField" value="" />
	<div class="setting_button" id="mediumButton">[[Click for categories mapping]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
	<div class="setting_block" style="display: none" id="clearTable">
		<table id="category" class="mapping-category" width="100%">
			<thead>
				<tr>
					<th>[[JobG8 categories]]</th>
					<th>
						[[SJB field]]: 
						<input type="text" class="mappingField" value="{$categoryMappingFieldID}" />
						<input type="submit" class="grayButton"  value="{if $categoryMappingFieldID}[[Change Field]]{else}[[Set Field]]{/if}" />
					</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$categoryMappingFieldValues item="categoryMappingFieldValue"}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td valign="top">
							<input type="hidden" name="allow[{$categoryMappingFieldValue.sid}]" value="0" />
							<input type="checkbox" name="allow[{$categoryMappingFieldValue.sid}]" value="1" {if $categoryMappingFieldValue.allow}checked="checked"{/if} />
							[[{$categoryMappingFieldValue.jobg8_field_value}]]
						</td>
						<td valign="top">
							<input type="hidden" name="category[{$categoryMappingFieldValue.sid}][]" value="" />
							<select multiple="multiple">
								{foreach from=$sjbCategories item="sjbCategory"}
									<option value="{$sjbCategory.id}" {if in_array($sjbCategory.id, explode(',', $categoryMappingFieldValue.sjb_field_value))}selected="selected"{/if}>{tr}{$sjbCategory.caption}{/tr|escape:'html'}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				{/foreach}
				<tr id="clearTable">
					<td colspan="2" align="right">
						<div class="floatRight mappingActions">
							<input type="submit" class="grayButton" value="[[Apply]]" data-value="apply" />
							<input type="submit" class="grayButton" value="[[Save]]" data-value="save" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"><br/></div>
	<div class="setting_button" id="mediumButton">[[Click for employment types mapping]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
	<div class="setting_block" style="display: none" id="clearTable">
		<table id="employment" class="mapping-employment" width="100%">
			<thead>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<th>[[JobG8 employment types]]</th>
					<th>
						[[SJB field]]:
						<input type="text" class="mappingField" value="{$employmentMappingFieldID}" />
						<input type="submit" class="grayButton"  value="{if $employmentMappingFieldID}[[Change Field]]{else}[[Set Field]]{/if}" />
					</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$employmentMappingFieldValues item="employmentMappingFieldValue"}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td valign="top">
							<input type="hidden" name="allow[{$employmentMappingFieldValue.sid}]" value="0" />
							<input type="checkbox" name="allow[{$employmentMappingFieldValue.sid}]" value="1" {if $employmentMappingFieldValue.allow}checked="checked"{/if} />
							[[{$employmentMappingFieldValue.jobg8_field_value}]]
						</td>
						<td valign="top">
							<input type="hidden" name="employment[{$employmentMappingFieldValue.sid}][]" value="" />
							<select>
								{foreach from=$sjbEmploymentTypes item="sjbEmploymentType"}
									<option value="{$sjbEmploymentType.id}" {if in_array($sjbEmploymentType.id, explode(',', $employmentMappingFieldValue.sjb_field_value))}selected="selected"{/if}>{tr}{$sjbEmploymentType.caption}{/tr|escape:'html'}</option>
								{/foreach}
							</select>
						</td>
					</tr>
				{/foreach}
				<tr id="clearTable">
					<td colspan="2" align="right">
						<div class="floatRight mappingActions">
							<input type="submit" class="grayButton" value="[[Apply]]" data-value="apply" />
							<input type="submit" class="grayButton" value="[[Save]]" data-value="save" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>

<script type="text/javascript">
	$('input[type="submit"]').click(function() {
		var mappingType = $(this).parents('table').attr('id');
		$('#type').val(mappingType);
		if ($(this).parent().is('div.mappingActions')) {
			$('table#'+ mappingType).find('select').each(function() {
				$(this).prev("input[type='hidden']").val($(this).val());
			});
			$('#submit').val($(this).attr('data-value'));
		} else {
			$('#changeMappingField').val(1);
			$('#mappingField').val($(this).prev('.mappingField').val());
		}
	});
	
	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
			if ($(this).css("display") == "block") {
				butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
				butt.children("b").text("Click to hide search criteria");
			} else {
				butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
				butt.children("b").text("Click to modify search criteria");
			}
		});
	});
	
	function ignoreMappingField() {
		var mappingField = $(this).parent().next().children('select');
		if ($(this).is(':checked')) {
			mappingField.attr("disabled", false);
		} else {
			mappingField.attr("disabled", true);
		}
	}
	
	$("input[type='checkbox']").each(ignoreMappingField).click(ignoreMappingField);
</script>