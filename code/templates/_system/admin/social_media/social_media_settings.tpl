{breadcrumbs}
	<a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/">[[Plugins]]</a> &#187; [[Social Login]]
{/breadcrumbs}
<h1><img src="{image}/icons/gear32.png" border="0" alt="" class="titleicon"/>[[Social Login]]</h1>

{foreach from=$errors item="error"}
	<p class="error">
		[[{$error}]]
	</p>
{/foreach}
{foreach from=$messages item="message"}
	<p class="message">
		{if $message == 'ACCOUNT_UPDATED'}
			[[Account is successfully updated.]]
		{else}
			[[{$message}]]
		{/if}
	</p>
{/foreach}

<div id="social-media">
	<div id="settingsPane">
		<form method="post">
			{if $network != 'googleplus'}
				<ul class="ui-tabs-nav">
					<li class="ui-tabs-selected"><a href="#connectionSettings"><span>[[$networkName Settings]]</span></a></li>
				</ul>
			{elseif $network == 'googleplus'}
				<ul class="ui-tabs-nav">
					<li class="ui-tabs-selected"><a href="#connectionSettings"><span>[[$networkName Connect Settings]]</span></a></li>
				</ul>
			{/if}
			<input type="hidden" name="action" value="save_settings">
			<div id="connectionSettings" class="ui-tabs-panel">
				<table class="basetable" width="100%">
					<input type="hidden" name="soc_network" value="{$network}">
					<input type="hidden" name="submit" value="save">
					{foreach from=$settings item=networkSettings name=networkSettings}
						<tr class="{cycle values = 'oddrow,evenrow'}
							{if $networkSettings.id == "oauth2_client_id" || $networkSettings.id == "client_secret" || $networkSettings.id == "developer_key"}network-field-oddrow{/if}
							{if $networkSettings.id == "fb_appID" || $networkSettings.id == "fb_appSecret" || $networkSettings.id == "li_apiKey" || $networkSettings.id == "li_secKey"}network-field-evenrow{/if}
							{if $networkSettings.id == "developer_key" || $networkSettings.id == "fb_appSecret" || $networkSettings.id == "li_secKey"}network-field-last{/if}">
							{assign var=setting_name value=$networkSettings.id}
							<td>
								<label for="{$networkSettings.id}">[[{$networkSettings.caption}]]</label>
								<span class="right required">{if $networkSettings.is_required}*{/if}</span>
							</td>

							<td class="clear-border-left"
								{if $networkSettings.id !== "oauth2_client_id" && $networkSettings.id !== "client_secret" && $networkSettings.id !== "developer_key" &&
									$networkSettings.id !== "fb_appID" && $networkSettings.id !== "fb_appSecret" &&
									$networkSettings.id !== "li_secKey" && $networkSettings.id !== "li_apiKey"
									}colspan="2"{/if}>
								{$pluginSetting.tabName.id}
								{if $networkSettings.type == 'boolean'}
									<input type="hidden" name="{$setting_name}" value="0" /><input type="checkbox" id="{$networkSettings.id}" name="{$setting_name}" value="1" {if $savedSettings.$setting_name}checked="checked" {/if} />
								{elseif  $networkSettings.type == 'string'}
									<input type="text" name="{$networkSettings.id}" value="{$savedSettings.$setting_name|escape:'html'}" id="{$networkSettings.id}" />
								{elseif  $networkSettings.type == 'text'}
									<textarea name="{$networkSettings.id}" id="{$networkSettings.id}">{$savedSettings.$setting_name|escape:'html'}</textarea>
								{elseif  $networkSettings.type == 'integer'}
									<input type="text" class="inputInteger" value="{$savedSettings.$setting_name}" name="{$networkSettings.id}" id="{$networkSettings.id}"/>
								{elseif  $networkSettings.type == 'list'}
									<select name="{$networkSettings.id}" id="{$networkSettings.id}">
										<option value="">[[Please Select Item]]:</option>
										{foreach from=$networkSettings.list_values item=list}
											<option value="{$list.id}" {if $savedSettings.$setting_name == $list.id}selected="selected" {/if}>{$list.caption}</option>
										{/foreach}
									</select>
								{elseif  $networkSettings.type == 'multilist'}
									<select name="{$networkSettings.id}[]" multiple="multiple" id="{$networkSettings.id}">
										<option value="">[[Please Select Items]]:</option>
										{assign var=selectedItems value=$savedSettings.$setting_name}
										{foreach from=$networkSettings.list_values item=list}
											<option value="{$list.id}" {if (is_array($selectedItems) && in_array($list.id, $selectedItems)) || (!is_array($selectedItems) && in_array($list.id, explode(',', $selectedItems)))}selected{/if}>[[{$list.caption}]]</option>
										{/foreach}
									</select>
								{/if}
							</td>
							{if $networkSettings.id == "oauth2_client_id"}
								<td rowspan="3" class="comment-td">
									[[{$networkSettings.comment}]]
								</td>
							{elseif $networkSettings.id == "fb_appID" || $networkSettings.id == "li_apiKey"}
								<td rowspan="2" class="comment-td">
									[[{$networkSettings.comment}]]
								</td>
							{/if}
						</tr>
					{/foreach}
				</table>
			</div>

			<div class="clr"><br/></div>
			<div style="width: 900px;">
				<div class="floatRight" style="text-align: right;">
					<input type="submit" value="[[Apply]]" class="grayButton" id="applySettings"/>
					<input type="submit" class="grayButton" value="[[Save]]" />
				</div>
			</div>
		</form>
	</div>
</div>
{assign var="Close" value="[[Close]]"}
<script type="text/javascript">
	$("#settingsPane").tabs();
	$('#applySettings').click(
		function(){
			$('input[name="submit"]').attr('value', 'apply');
		}
	);
	$('#li_signin').change(function() {
		$(this).closest('tr').nextAll().toggle(this.checked);
	}).change();
</script>
