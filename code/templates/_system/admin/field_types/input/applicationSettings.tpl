<script type="text/javascript">

	function displayInput(disableId) {
		$("[id^='ApplicationSettings']")
				.attr("disabled", "disabled")
				.closest('div').hide();
		var appSettingsDiv = document.getElementById(disableId);
		$("[id!=" + disableId + "][id^='ApplicationSettings']").val('');
		appSettingsDiv.disabled = false;
		$(appSettingsDiv).closest('div').show();
	}

	function validateForm(formName) {
		var form = document.getElementById(formName);
		var appSettingsRadio		= form.elements['{$id}[add_parameter]'];
		var appSettingsEmailValue	= form.elements["{$id}_1"].value;
		var appSettingsWebValue		= form.elements["{$id}_2"].value;
		for(var i = 0; i < appSettingsRadio.length; i++) {
			if(appSettingsRadio[i].checked && appSettingsRadio[i].value == 1)
				var exp = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
				if( (appSettingsEmailValue != '') && !(appSettingsEmailValue.match(exp)) ) {
					error('[["Application Settings" wrong Email format]]');
					return false;
				}
			else if(appSettingsRadio[i].checked && appSettingsRadio[i].value == 2) {
				if(appSettingsWebValue == '') {
					error('[["Application Settings" url is empty]]');
					return false;
				} else if( !( appSettingsWebValue.match(/https?:\/\//)) ) {
					form.elements["{$id}_2"].value = 'http://' + appSettingsWebValue;
					return true;
				}
			}
		}
		return true;
	}

	function error(error_text) {
		$("#dialog").dialog( 'destroy' ).html(error_text);
		$("#dialog").dialog({
			bgiframe: true,
			modal: true,
			title: '[[Error]]',
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
	}
	function getUrl(name) {
		var url = document.getElementById(name);
		if (url.value != '') {
			if (!(url.value.match(/https?:\/\//)) ) {
				url.value = 'http://' + url.value;
			}
			window.open(url.value, "target");
		} else {
			alert('[["Application Settings" url is empty]]');
		}
	}
</script>

<div id="dialog"></div>
<table id="application-settings">
	<tr>
		<td valign="top">
			<input id="via-email" class="inputRadio" name="{$id}[add_parameter]" value="1" {if $value.add_parameter == 1 || $value.add_parameter == ''}checked="checked"{/if} onclick="displayInput('{$id}_1');" type="radio" />
			<label for="via-email">
				[[By Email]]
			</label>
		</td>
		<td>
			<input id="via-site" class="inputRadio" name="{$id}[add_parameter]" value="2" {if $value.add_parameter == 2}checked="checked"{/if} onclick="displayInput('{$id}_2');" type="radio" />
			<label for="via-site">
				[[By URL]]
			</label>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="2">
			<div class="application-settings__email" {if $value.add_parameter == 2}style="display: none;"{/if}>
				<input value="{if $value.add_parameter == 1}{$value.value|escape:'html'}{/if}" class="inputString"  name="{$id}[value]" {if $value.add_parameter == 2}disabled="disabled"{/if} id="{$id}_1" type="text" />
				<div class="small">[[Send applications to this e-mail]]</div>
			</div>
			<div class="application-settings__site" {if $value.add_parameter != 2}style="display: none;"{/if}>
				<input value="{if $value.add_parameter == 2}{$value.value|escape:'html'}{/if}" class="inputString " name="{$id}[value]" id="{$id}_2" {if $value.add_parameter != 2}disabled="disabled"{/if} type="text" />
				<input type="button" name="browse" value="[[Test URL]]" class="grayButton" onclick="getUrl('{$id}_2')" /><br />
				<span class="small">[[Use the following format:]] <i><strong>http://</strong>yoursite.com</i></span>
			</div>
		</td>
	</tr>
</table>