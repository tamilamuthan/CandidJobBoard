{javascript}
	<script type="text/javascript">

		function displayInput(disableValue, disableId) {
			$("[id^='ApplicationSettings']").attr("disabled", "disabled");
			var appSettingsDiv = document.getElementById(disableId);
			$("[id!=" + disableId + "][id^='ApplicationSettings']");
			appSettingsDiv.disabled = disableValue;
		}

		function validateForm(formName) {
			var form = document.getElementById(formName);
			var appSettingsRadio		= form.elements['{$id}[add_parameter]'];
			var appSettingsEmailValue	= form.elements["{$id}_1"].value;
			var appSettingsWebValue		= form.elements["{$id}_2"].value;
			for(var i = 0; i < appSettingsRadio.length; i++) {
				if(appSettingsRadio[i].checked && appSettingsRadio[i].value == 1)
					var exp = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
				if( !appSettingsEmailValue.match(exp)) {
					message('[[Error]]', '[["How to Apply" wrong email format]]');
					return false;
				}
				else if(appSettingsRadio[i].checked && appSettingsRadio[i].value == 2) {
					if(appSettingsWebValue == '') {
						message('[[Error]]', '[["How to Apply" by url is empty]]');
						return false;
					} else if( !( appSettingsWebValue.match(/https?:\/\//)) ) {
						form.elements["{$id}_2"].value = 'http://' + appSettingsWebValue;
						return true;
					}
				}
			}
			return true;
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
{/javascript}

<div id="application-settings" class="form--move-left clearfix">
	<div class="form-group__half">
		<input  id="via-email" name="{$id}[add_parameter]" value="1" {if $value.add_parameter == 1 || $value.add_parameter == ''}checked="checked"{/if} onclick="displayInput(false, '{$id}_1');" type="radio" />
		<label for="via-email" class="form-label">
			[[By Email]]<br/>
		</label>
		<input value="{if $listing.ApplicationSettings.value && $listing.ApplicationSettings.add_parameter == '1'}{$listing.ApplicationSettings.value}{else}{$GLOBALS.current_user.username}{/if}" class="form-control"  name="{$id}[value]" {if $value.add_parameter == 2}disabled="disabled"{/if} id="{$id}_1" type="text" />
	</div>
	<div class="form-group__half">
		<input  id="via-site" name="{$id}[add_parameter]" value="2" {if $value.add_parameter == 2}checked="checked"{/if} onclick="displayInput(false, '{$id}_2');" type="radio" />
		<label for="via-site" class="form-label">
			[[By URL]]
		</label>
		<input value="{if $value.add_parameter == 2}{$value.value|escape:'html'}{/if}" class="form-control" name="{$id}[value]" id="{$id}_2" {if $value.add_parameter != 2}disabled="disabled"{/if} type="text" placeholder="[[e.g. http://www.yourwebsite.com]]"/>
	</div>
</div>