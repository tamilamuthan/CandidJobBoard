<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('select[name="i18n_default_language"').change(function() {
			$('.default-language-form').submit();
		});
		$('.save-phrases').click(function() {
			$('.messages *').hide();
			var phrases = [];
			$('.translated input').each(function() {
				phrases.push(
					{
						'name': $(this).attr('name'),
						'value': $(this).val()
					}
				);
			});
			$.post('{$GLOBALS.site_url}/edit-phrase/', {
					phrases: phrases
				}, function(data) {
					if (data != 'ok') {
						$('.phrases-update-error').show();
					} else {
						$('.phrases-update-success').show();
					}
				}
			);
		});
	});
</script>

{breadcrumbs}[[Edit Language]]{/breadcrumbs}
<h1><img src="{image}/icons/exchange32.png" border="0" alt="" class="titleicon"/>[[Edit Language]]</h1>
<p>
	<table>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Default Language]]</td>
			<td>
				<form class="default-language-form" action="" method="post">
					<select name="i18n_default_language">
						{foreach from=$languages item=language}
							<option value="{$language.id}"{if $settings.i18n_default_language == $language.id} selected="selected"{/if}>{$language.caption}</option>
						{/foreach}
					</select>
				</form>
			</td>
		</tr>
	</table>
</p>

<form method="post" action="{$GLOBALS.site_url}/manage-phrases/">
	<input type="hidden" name="curr_lang" id="curr_lang" value="{$criteria.language}" />
	<table>
		<tr>
			<td>[[Filter Phrases]]</td>
			<td>
				<input type="text" name="phrase_id" value="{$criteria.phrase_id|escape}" />
				<input type="hidden" name="action" value="search_phrases" />
				<input type="hidden" name="page" value="1" />
				<input type="submit" name="show" value="[[Filter]]" class="grayButton" />
			</td>
		</tr>
		<tr style="display: none;">
			<td>[[Domain]]:</td>
			<td>
				<select name="domain">
					<option value="">[[Any]]</option>
					{foreach from=$domains item=domain}
						<option value="{$domain}"{if $criteria.domain == $domain} selected="selected"{/if}>{$domain}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr style="display: none;">
			<td>[[Language]]:</td>
			<td>
				<select name="language">
					{foreach from=$languages item=language}
						<option value="{$language.id}"
								{if $criteria.language == $language.id}
							selected="selected"
								{assign var='chosen_language_id' value=$language.id}
								{assign var='chosen_language_caption' value=$language.caption}
								{/if}>{$language.caption}</option>
					{/foreach}
				</select>
			</td>
		</tr>
	</table>

	<div class="right">
		<button type="button" href="{$GLOBALS.site_url}/edit-phrase/" class="grayButton save-phrases">[[Save]]</button>
	</div>
</form>
<div class="clr"></div>

<div class="messages">
	{if $errors}
		{include file="errors.tpl" errors=$errors}
	{/if}
	<p class="phrases-update-success message" style="display: none;">[[Your changes have been successfully saved]]</p>
	<p class="phrases-update-error error" style="display: none;">
		[[Unable to update phrases]]
	</p>
</div>

<div class="clr"><br/></div>
<div class="box" id="displayResults">
	<div class="box-header">
		{include file="../pagination/pagination.tpl" layout="header"}
	</div>
	<div class="innerpadding">
		<div id="displayResultsTable">
			<table width="100%">
				<thead>
					<tr>
						<th width="50%">[[Phrase ID]]</th>
						<th width="50%">{$chosen_language_caption}</th>
					</tr>
				</thead>
				{if !empty($found_phrases)}
					{foreach from=$found_phrases item=phrase}
						{if $phrase.domain != $domain}
							<tr>
								<th colspan="2">{$phrase.domain}</th>
							</tr>
						{/if}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>{$phrase.id|escape}</td>
							<td class="translated">
								<input type="text" name="{$phrase.id|escape}" value="{$phrase.translations.$chosen_language_id|escape}" />
							</td>
						</tr>
						{assign var=domain value=$phrase.domain}
					{/foreach}
				{/if}
			</table>
		</div>
	</div>
	<div class="box-footer">
		{include file="../pagination/pagination.tpl" layout="footer"}
	</div>
</div>