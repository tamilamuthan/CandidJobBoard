{breadcrumbs}[[System Settings]]{/breadcrumbs}
<h1><img src="{image}/icons/gear32.png" border="0" alt="" class="titleicon"/>[[System Settings]]</h1>
{foreach from=$errors item=error}
	<p class="error">[[{$error}]]</p>
{/foreach}

<form method="post" action="{$GLOBALS.site_url}/settings/" id="settingsPane">
	<input type="hidden" id="action" name="action" value="save_settings" />
    <input type="hidden" id="page" name="page" value="#generalTab"/>
	<div id="settingsPane">
		<ul class="ui-tabs-nav">
			<li class="ui-tabs-selected"><a href="#generalTab"><span>[[Job Board Settings]]</span></a></li>
			{*<li class="ui-tabs-selected"><a href="#employerTab"><span>[[Employer Settings]]</span></a></li>*}
			{*<li class="ui-tabs-selected"><a href="#jobSeekerTab"><span>[[Job Seeker/Resume Settings]]</span></a></li>*}
            {*{if not $isSaas}<li class="ui-tabs-unselect"><a href="#performanceTab"><span>[[Performance]]</span></a></li>{/if}*}
			<li class="ui-tabs-unselect"><a href="#ecommerceTab"><span>[[Ecommerce Settings]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#seoTab"><span>[[SEO]]</span></a></li>
			{if not $isSaas}
				<li class="ui-tabs-unselect"><a href="#mailTab"><span>[[Mail]]</span></a></li>
			{/if}
		</ul>

		<div id="generalTab" class="ui-tabs-panel">
			<table width="100%">
				<thead>
					<tr>
						<th>[[Name]]</th>
						<th>[[Value]]</th>
					</tr>
				</thead>
				<tbody>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Site Name]]</td>
					<td><input type="text" name="site_title" value="{$settings.site_title}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Site Email]]</td>
					<td><input type="text" name="system_email" value="{$settings.system_email}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Custom Domain Name]]</td>
					<td>
						<input type="text" name="domain" value="{$settings.domain}" />
						{if $isSaas}
							<br>
							<sub>
								[[Please point your A-record to our IP address:]] <strong>{$ip}</strong>
								<br>
								<a target="_blank" href="http://help.smartjobboard.com/knowledge_base/topics/connecting-a-custom-domain-name">
									[[Learn more]]
								</a>
							</sub>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Public Resume Access]]</td>
					<td><input type="hidden" name="public_resume_access" value="0" /><input type="checkbox" name="public_resume_access" value="1"{if $settings.public_resume_access} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Maintenance Mode]] {if not $isSaas}<a href="{$GLOBALS.site_url}/edit-templates/?module_name=miscellaneous&template_name=maintenance_mode.tpl" target="_blank" title="[[Edit maintenance_mode.tpl]]" class="edit-email-template"></a>{/if}</td>
					<td><input type="hidden" name="maintenance_mode" value="0" /><input id="maintenance_mode_" type="checkbox" name="maintenance_mode" value="1"{if $settings.maintenance_mode} checked="checked"{/if} /><br/>
						[[enter IP or IP range to access the site]]<br/>
						<input type="text" value="{$settings.maintenance_mode_ip}" name="maintenance_mode_ip"/><br/>
						<sub>[[use * for replacing one or several digits<br />use comma (,) to specify two or more IPs]]</sub>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Delete Expired Listings]]</td>
					<td><input type="hidden" name="automatically_delete_expired_listings" value="0" /><input type="checkbox" name="automatically_delete_expired_listings" value="1"{if $settings.automatically_delete_expired_listings} checked="checked"{/if} /> [[after]] <input type="text"  style="width:100px" name="period_delete_expired_listings" value="{$settings.period_delete_expired_listings}"/> [[days]]</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Timezone]]</td>
					<td>
						<select name="timezone">
							{foreach from=$timezones item=timezone}
								<option value="{$timezone}" {if $settings.timezone == $timezone} selected="selected"{/if}>{$timezone}</option>
							{/foreach}
						</select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Date Format]]</td>
					<td>
						<select name="date_format">
							{foreach from=$date_formats item=date_format}
								<option value="{$date_format|escape}" {if $settings.date_format == $date_format} selected="selected"{/if}>{$smarty.now|date:$date_format@key|escape}</option>
							{/foreach}
						</select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Google Analytics ID]]</td>
					<td><input type="text" name="google_TrackingID" value="{$settings.google_TrackingID}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Search by location]]</td>
					<td><input type="hidden" name="search_by_location" value="0" /><input type="checkbox" name="search_by_location" value="1"{if $settings.search_by_location} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'} location__sub-setting">
					<td>[[Display radius in]]</td>
					<td>
						<select name="radius_search_unit">
							<option value="miles" {if $settings.radius_search_unit == 'miles'} selected="selected"{/if}>[[Miles]]</option>
							<option value="kilometers" {if $settings.radius_search_unit == 'kilometers'} selected="selected"{/if}>[[Kilometers]]</option>
						</select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'} location__sub-setting">
					<td>[[Limit location selection to]]</td>
					<td>
						<select name="location_limit">
							<option value="" {if not $settings.location_limit}selected="selected"{/if}>[[Any Country]]</option>
							<option value="AD" {if $settings.location_limit == 'AD'}selected="selected"{/if}>Andorra</option>
							<option value="AE" {if $settings.location_limit == 'AE'}selected="selected"{/if}>United Arab Emirates</option>
							<option value="AF" {if $settings.location_limit == 'AF'}selected="selected"{/if}>Afghanistan</option>
							<option value="AG" {if $settings.location_limit == 'AG'}selected="selected"{/if}>Antigua and Barbuda</option>
							<option value="AI" {if $settings.location_limit == 'AI'}selected="selected"{/if}>Anguilla</option>
							<option value="AL" {if $settings.location_limit == 'AL'}selected="selected"{/if}>Albania</option>
							<option value="AM" {if $settings.location_limit == 'AM'}selected="selected"{/if}>Armenia</option>
							<option value="AO" {if $settings.location_limit == 'AO'}selected="selected"{/if}>Angola</option>
							<option value="AQ" {if $settings.location_limit == 'AQ'}selected="selected"{/if}>Antarctica</option>
							<option value="AR" {if $settings.location_limit == 'AR'}selected="selected"{/if}>Argentina</option>
							<option value="AS" {if $settings.location_limit == 'AS'}selected="selected"{/if}>American Samoa</option>
							<option value="AT" {if $settings.location_limit == 'AT'}selected="selected"{/if}>Austria</option>
							<option value="AU" {if $settings.location_limit == 'AU'}selected="selected"{/if}>Australia</option>
							<option value="AW" {if $settings.location_limit == 'AW'}selected="selected"{/if}>Aruba</option>
							<option value="AX" {if $settings.location_limit == 'AX'}selected="selected"{/if}>Åland Islands</option>
							<option value="AZ" {if $settings.location_limit == 'AZ'}selected="selected"{/if}>Azerbaijan</option>
							<option value="BA" {if $settings.location_limit == 'BA'}selected="selected"{/if}>Bosnia and Herzegovina</option>
							<option value="BB" {if $settings.location_limit == 'BB'}selected="selected"{/if}>Barbados</option>
							<option value="BD" {if $settings.location_limit == 'BD'}selected="selected"{/if}>Bangladesh</option>
							<option value="BE" {if $settings.location_limit == 'BE'}selected="selected"{/if}>Belgium</option>
							<option value="BF" {if $settings.location_limit == 'BF'}selected="selected"{/if}>Burkina Faso</option>
							<option value="BG" {if $settings.location_limit == 'BG'}selected="selected"{/if}>Bulgaria</option>
							<option value="BH" {if $settings.location_limit == 'BH'}selected="selected"{/if}>Bahrain</option>
							<option value="BI" {if $settings.location_limit == 'BI'}selected="selected"{/if}>Burundi</option>
							<option value="BJ" {if $settings.location_limit == 'BJ'}selected="selected"{/if}>Benin</option>
							<option value="BL" {if $settings.location_limit == 'BL'}selected="selected"{/if}>Saint Barthélemy</option>
							<option value="BM" {if $settings.location_limit == 'BM'}selected="selected"{/if}>Bermuda</option>
							<option value="BN" {if $settings.location_limit == 'BN'}selected="selected"{/if}>Brunei Darussalam</option>
							<option value="BO" {if $settings.location_limit == 'BO'}selected="selected"{/if}>Bolivia, Plurinational State of</option>
							<option value="BQ" {if $settings.location_limit == 'BQ'}selected="selected"{/if}>Bonaire, Sint Eustatius and Saba</option>
							<option value="BR" {if $settings.location_limit == 'BR'}selected="selected"{/if}>Brazil</option>
							<option value="BS" {if $settings.location_limit == 'BS'}selected="selected"{/if}>Bahamas</option>
							<option value="BT" {if $settings.location_limit == 'BT'}selected="selected"{/if}>Bhutan</option>
							<option value="BV" {if $settings.location_limit == 'BV'}selected="selected"{/if}>Bouvet Island</option>
							<option value="BW" {if $settings.location_limit == 'BW'}selected="selected"{/if}>Botswana</option>
							<option value="BY" {if $settings.location_limit == 'BY'}selected="selected"{/if}>Belarus</option>
							<option value="BZ" {if $settings.location_limit == 'BZ'}selected="selected"{/if}>Belize</option>
							<option value="CA" {if $settings.location_limit == 'CA'}selected="selected"{/if}>Canada</option>
							<option value="CC" {if $settings.location_limit == 'CC'}selected="selected"{/if}>Cocos (Keeling) Islands</option>
							<option value="CD" {if $settings.location_limit == 'CD'}selected="selected"{/if}>Congo, the Democratic Republic of the</option>
							<option value="CF" {if $settings.location_limit == 'CF'}selected="selected"{/if}>Central African Republic</option>
							<option value="CG" {if $settings.location_limit == 'CG'}selected="selected"{/if}>Congo</option>
							<option value="CH" {if $settings.location_limit == 'CH'}selected="selected"{/if}>Switzerland</option>
							<option value="CI" {if $settings.location_limit == 'CI'}selected="selected"{/if}>Côte d'Ivoire</option>
							<option value="CK" {if $settings.location_limit == 'CK'}selected="selected"{/if}>Cook Islands</option>
							<option value="CL" {if $settings.location_limit == 'CL'}selected="selected"{/if}>Chile</option>
							<option value="CM" {if $settings.location_limit == 'CM'}selected="selected"{/if}>Cameroon</option>
							<option value="CN" {if $settings.location_limit == 'CN'}selected="selected"{/if}>China</option>
							<option value="CO" {if $settings.location_limit == 'CO'}selected="selected"{/if}>Colombia</option>
							<option value="CR" {if $settings.location_limit == 'CR'}selected="selected"{/if}>Costa Rica</option>
							<option value="CU" {if $settings.location_limit == 'CU'}selected="selected"{/if}>Cuba</option>
							<option value="CV" {if $settings.location_limit == 'CV'}selected="selected"{/if}>Cabo Verde</option>
							<option value="CW" {if $settings.location_limit == 'CW'}selected="selected"{/if}>Curaçao</option>
							<option value="CX" {if $settings.location_limit == 'CX'}selected="selected"{/if}>Christmas Island</option>
							<option value="CY" {if $settings.location_limit == 'CY'}selected="selected"{/if}>Cyprus</option>
							<option value="CZ" {if $settings.location_limit == 'CZ'}selected="selected"{/if}>Czech Republic</option>
							<option value="DE" {if $settings.location_limit == 'DE'}selected="selected"{/if}>Germany</option>
							<option value="DJ" {if $settings.location_limit == 'DJ'}selected="selected"{/if}>Djibouti</option>
							<option value="DK" {if $settings.location_limit == 'DK'}selected="selected"{/if}>Denmark</option>
							<option value="DM" {if $settings.location_limit == 'DM'}selected="selected"{/if}>Dominica</option>
							<option value="DO" {if $settings.location_limit == 'DO'}selected="selected"{/if}>Dominican Republic</option>
							<option value="DZ" {if $settings.location_limit == 'DZ'}selected="selected"{/if}>Algeria</option>
							<option value="EC" {if $settings.location_limit == 'EC'}selected="selected"{/if}>Ecuador</option>
							<option value="EE" {if $settings.location_limit == 'EE'}selected="selected"{/if}>Estonia</option>
							<option value="EG" {if $settings.location_limit == 'EG'}selected="selected"{/if}>Egypt</option>
							<option value="EH" {if $settings.location_limit == 'EH'}selected="selected"{/if}>Western Sahara</option>
							<option value="ER" {if $settings.location_limit == 'ER'}selected="selected"{/if}>Eritrea</option>
							<option value="ES" {if $settings.location_limit == 'ES'}selected="selected"{/if}>Spain</option>
							<option value="ET" {if $settings.location_limit == 'ET'}selected="selected"{/if}>Ethiopia</option>
							<option value="FI" {if $settings.location_limit == 'FI'}selected="selected"{/if}>Finland</option>
							<option value="FJ" {if $settings.location_limit == 'FJ'}selected="selected"{/if}>Fiji</option>
							<option value="FK" {if $settings.location_limit == 'FK'}selected="selected"{/if}>Falkland Islands (Malvinas)</option>
							<option value="FM" {if $settings.location_limit == 'FM'}selected="selected"{/if}>Micronesia, Federated States of</option>
							<option value="FO" {if $settings.location_limit == 'FO'}selected="selected"{/if}>Faroe Islands</option>
							<option value="FR" {if $settings.location_limit == 'FR'}selected="selected"{/if}>France</option>
							<option value="GA" {if $settings.location_limit == 'GA'}selected="selected"{/if}>Gabon</option>
							<option value="GB" {if $settings.location_limit == 'GB'}selected="selected"{/if}>United Kingdom of Great Britain and Northern Ireland</option>
							<option value="GD" {if $settings.location_limit == 'GD'}selected="selected"{/if}>Grenada</option>
							<option value="GE" {if $settings.location_limit == 'GE'}selected="selected"{/if}>Georgia</option>
							<option value="GF" {if $settings.location_limit == 'GF'}selected="selected"{/if}>French Guiana</option>
							<option value="GG" {if $settings.location_limit == 'GG'}selected="selected"{/if}>Guernsey</option>
							<option value="GH" {if $settings.location_limit == 'GH'}selected="selected"{/if}>Ghana</option>
							<option value="GI" {if $settings.location_limit == 'GI'}selected="selected"{/if}>Gibraltar</option>
							<option value="GL" {if $settings.location_limit == 'GL'}selected="selected"{/if}>Greenland</option>
							<option value="GM" {if $settings.location_limit == 'GM'}selected="selected"{/if}>Gambia</option>
							<option value="GN" {if $settings.location_limit == 'GN'}selected="selected"{/if}>Guinea</option>
							<option value="GP" {if $settings.location_limit == 'GP'}selected="selected"{/if}>Guadeloupe</option>
							<option value="GQ" {if $settings.location_limit == 'GQ'}selected="selected"{/if}>Equatorial Guinea</option>
							<option value="GR" {if $settings.location_limit == 'GR'}selected="selected"{/if}>Greece</option>
							<option value="GS" {if $settings.location_limit == 'GS'}selected="selected"{/if}>South Georgia and the South Sandwich Islands</option>
							<option value="GT" {if $settings.location_limit == 'GT'}selected="selected"{/if}>Guatemala</option>
							<option value="GU" {if $settings.location_limit == 'GU'}selected="selected"{/if}>Guam</option>
							<option value="GW" {if $settings.location_limit == 'GW'}selected="selected"{/if}>Guinea-Bissau</option>
							<option value="GY" {if $settings.location_limit == 'GY'}selected="selected"{/if}>Guyana</option>
							<option value="HK" {if $settings.location_limit == 'HK'}selected="selected"{/if}>Hong Kong</option>
							<option value="HM" {if $settings.location_limit == 'HM'}selected="selected"{/if}>Heard Island and McDonald Islands</option>
							<option value="HN" {if $settings.location_limit == 'HN'}selected="selected"{/if}>Honduras</option>
							<option value="HR" {if $settings.location_limit == 'HR'}selected="selected"{/if}>Croatia</option>
							<option value="HT" {if $settings.location_limit == 'HT'}selected="selected"{/if}>Haiti</option>
							<option value="HU" {if $settings.location_limit == 'HU'}selected="selected"{/if}>Hungary</option>
							<option value="ID" {if $settings.location_limit == 'ID'}selected="selected"{/if}>Indonesia</option>
							<option value="IE" {if $settings.location_limit == 'IE'}selected="selected"{/if}>Ireland</option>
							<option value="IL" {if $settings.location_limit == 'IL'}selected="selected"{/if}>Israel</option>
							<option value="IM" {if $settings.location_limit == 'IM'}selected="selected"{/if}>Isle of Man</option>
							<option value="IN" {if $settings.location_limit == 'IN'}selected="selected"{/if}>India</option>
							<option value="IO" {if $settings.location_limit == 'IO'}selected="selected"{/if}>British Indian Ocean Territory</option>
							<option value="IQ" {if $settings.location_limit == 'IQ'}selected="selected"{/if}>Iraq</option>
							<option value="IR" {if $settings.location_limit == 'IR'}selected="selected"{/if}>Iran, Islamic Republic of</option>
							<option value="IS" {if $settings.location_limit == 'IS'}selected="selected"{/if}>Iceland</option>
							<option value="IT" {if $settings.location_limit == 'IT'}selected="selected"{/if}>Italy</option>
							<option value="JE" {if $settings.location_limit == 'JE'}selected="selected"{/if}>Jersey</option>
							<option value="JM" {if $settings.location_limit == 'JM'}selected="selected"{/if}>Jamaica</option>
							<option value="JO" {if $settings.location_limit == 'JO'}selected="selected"{/if}>Jordan</option>
							<option value="JP" {if $settings.location_limit == 'JP'}selected="selected"{/if}>Japan</option>
							<option value="KE" {if $settings.location_limit == 'KE'}selected="selected"{/if}>Kenya</option>
							<option value="KG" {if $settings.location_limit == 'KG'}selected="selected"{/if}>Kyrgyzstan</option>
							<option value="KH" {if $settings.location_limit == 'KH'}selected="selected"{/if}>Cambodia</option>
							<option value="KI" {if $settings.location_limit == 'KI'}selected="selected"{/if}>Kiribati</option>
							<option value="KM" {if $settings.location_limit == 'KM'}selected="selected"{/if}>Comoros</option>
							<option value="KN" {if $settings.location_limit == 'KN'}selected="selected"{/if}>Saint Kitts and Nevis</option>
							<option value="KP" {if $settings.location_limit == 'KP'}selected="selected"{/if}>Korea, Democratic People's Republic of</option>
							<option value="KR" {if $settings.location_limit == 'KR'}selected="selected"{/if}>Korea, Republic of</option>
							<option value="KW" {if $settings.location_limit == 'KW'}selected="selected"{/if}>Kuwait</option>
							<option value="KY" {if $settings.location_limit == 'KY'}selected="selected"{/if}>Cayman Islands</option>
							<option value="KZ" {if $settings.location_limit == 'KZ'}selected="selected"{/if}>Kazakhstan</option>
							<option value="LA" {if $settings.location_limit == 'LA'}selected="selected"{/if}>Lao People's Democratic Republic</option>
							<option value="LB" {if $settings.location_limit == 'LB'}selected="selected"{/if}>Lebanon</option>
							<option value="LC" {if $settings.location_limit == 'LC'}selected="selected"{/if}>Saint Lucia</option>
							<option value="LI" {if $settings.location_limit == 'LI'}selected="selected"{/if}>Liechtenstein</option>
							<option value="LK" {if $settings.location_limit == 'LK'}selected="selected"{/if}>Sri Lanka</option>
							<option value="LR" {if $settings.location_limit == 'LR'}selected="selected"{/if}>Liberia</option>
							<option value="LS" {if $settings.location_limit == 'LS'}selected="selected"{/if}>Lesotho</option>
							<option value="LT" {if $settings.location_limit == 'LT'}selected="selected"{/if}>Lithuania</option>
							<option value="LU" {if $settings.location_limit == 'LU'}selected="selected"{/if}>Luxembourg</option>
							<option value="LV" {if $settings.location_limit == 'LV'}selected="selected"{/if}>Latvia</option>
							<option value="LY" {if $settings.location_limit == 'LY'}selected="selected"{/if}>Libya</option>
							<option value="MA" {if $settings.location_limit == 'MA'}selected="selected"{/if}>Morocco</option>
							<option value="MC" {if $settings.location_limit == 'MC'}selected="selected"{/if}>Monaco</option>
							<option value="MD" {if $settings.location_limit == 'MD'}selected="selected"{/if}>Moldova, Republic of</option>
							<option value="ME" {if $settings.location_limit == 'ME'}selected="selected"{/if}>Montenegro</option>
							<option value="MF" {if $settings.location_limit == 'MF'}selected="selected"{/if}>Saint Martin (French part)</option>
							<option value="MG" {if $settings.location_limit == 'MG'}selected="selected"{/if}>Madagascar</option>
							<option value="MH" {if $settings.location_limit == 'MH'}selected="selected"{/if}>Marshall Islands</option>
							<option value="MK" {if $settings.location_limit == 'MK'}selected="selected"{/if}>Macedonia, the former Yugoslav Republic of</option>
							<option value="ML" {if $settings.location_limit == 'ML'}selected="selected"{/if}>Mali</option>
							<option value="MM" {if $settings.location_limit == 'MM'}selected="selected"{/if}>Myanmar</option>
							<option value="MN" {if $settings.location_limit == 'MN'}selected="selected"{/if}>Mongolia</option>
							<option value="MO" {if $settings.location_limit == 'MO'}selected="selected"{/if}>Macao</option>
							<option value="MP" {if $settings.location_limit == 'MP'}selected="selected"{/if}>Northern Mariana Islands</option>
							<option value="MQ" {if $settings.location_limit == 'MQ'}selected="selected"{/if}>Martinique</option>
							<option value="MR" {if $settings.location_limit == 'MR'}selected="selected"{/if}>Mauritania</option>
							<option value="MS" {if $settings.location_limit == 'MS'}selected="selected"{/if}>Montserrat</option>
							<option value="MT" {if $settings.location_limit == 'MT'}selected="selected"{/if}>Malta</option>
							<option value="MU" {if $settings.location_limit == 'MU'}selected="selected"{/if}>Mauritius</option>
							<option value="MV" {if $settings.location_limit == 'MV'}selected="selected"{/if}>Maldives</option>
							<option value="MW" {if $settings.location_limit == 'MW'}selected="selected"{/if}>Malawi</option>
							<option value="MX" {if $settings.location_limit == 'MX'}selected="selected"{/if}>Mexico</option>
							<option value="MY" {if $settings.location_limit == 'MY'}selected="selected"{/if}>Malaysia</option>
							<option value="MZ" {if $settings.location_limit == 'MZ'}selected="selected"{/if}>Mozambique</option>
							<option value="NA" {if $settings.location_limit == 'NA'}selected="selected"{/if}>Namibia</option>
							<option value="NC" {if $settings.location_limit == 'NC'}selected="selected"{/if}>New Caledonia</option>
							<option value="NE" {if $settings.location_limit == 'NE'}selected="selected"{/if}>Niger</option>
							<option value="NF" {if $settings.location_limit == 'NF'}selected="selected"{/if}>Norfolk Island</option>
							<option value="NG" {if $settings.location_limit == 'NG'}selected="selected"{/if}>Nigeria</option>
							<option value="NI" {if $settings.location_limit == 'NI'}selected="selected"{/if}>Nicaragua</option>
							<option value="NL" {if $settings.location_limit == 'NL'}selected="selected"{/if}>Netherlands</option>
							<option value="NO" {if $settings.location_limit == 'NO'}selected="selected"{/if}>Norway</option>
							<option value="NP" {if $settings.location_limit == 'NP'}selected="selected"{/if}>Nepal</option>
							<option value="NR" {if $settings.location_limit == 'NR'}selected="selected"{/if}>Nauru</option>
							<option value="NU" {if $settings.location_limit == 'NU'}selected="selected"{/if}>Niue</option>
							<option value="NZ" {if $settings.location_limit == 'NZ'}selected="selected"{/if}>New Zealand</option>
							<option value="OM" {if $settings.location_limit == 'OM'}selected="selected"{/if}>Oman</option>
							<option value="PA" {if $settings.location_limit == 'PA'}selected="selected"{/if}>Panama</option>
							<option value="PE" {if $settings.location_limit == 'PE'}selected="selected"{/if}>Peru</option>
							<option value="PF" {if $settings.location_limit == 'PF'}selected="selected"{/if}>French Polynesia</option>
							<option value="PG" {if $settings.location_limit == 'PG'}selected="selected"{/if}>Papua New Guinea</option>
							<option value="PH" {if $settings.location_limit == 'PH'}selected="selected"{/if}>Philippines</option>
							<option value="PK" {if $settings.location_limit == 'PK'}selected="selected"{/if}>Pakistan</option>
							<option value="PL" {if $settings.location_limit == 'PL'}selected="selected"{/if}>Poland</option>
							<option value="PM" {if $settings.location_limit == 'PM'}selected="selected"{/if}>Saint Pierre and Miquelon</option>
							<option value="PN" {if $settings.location_limit == 'PN'}selected="selected"{/if}>Pitcairn</option>
							<option value="PR" {if $settings.location_limit == 'PR'}selected="selected"{/if}>Puerto Rico</option>
							<option value="PS" {if $settings.location_limit == 'PS'}selected="selected"{/if}>Palestine, State of</option>
							<option value="PT" {if $settings.location_limit == 'PT'}selected="selected"{/if}>Portugal</option>
							<option value="PW" {if $settings.location_limit == 'PW'}selected="selected"{/if}>Palau</option>
							<option value="PY" {if $settings.location_limit == 'PY'}selected="selected"{/if}>Paraguay</option>
							<option value="QA" {if $settings.location_limit == 'QA'}selected="selected"{/if}>Qatar</option>
							<option value="RE" {if $settings.location_limit == 'RE'}selected="selected"{/if}>Réunion</option>
							<option value="RO" {if $settings.location_limit == 'RO'}selected="selected"{/if}>Romania</option>
							<option value="RS" {if $settings.location_limit == 'RS'}selected="selected"{/if}>Serbia</option>
							<option value="RU" {if $settings.location_limit == 'RU'}selected="selected"{/if}>Russian Federation</option>
							<option value="RW" {if $settings.location_limit == 'RW'}selected="selected"{/if}>Rwanda</option>
							<option value="SA" {if $settings.location_limit == 'SA'}selected="selected"{/if}>Saudi Arabia</option>
							<option value="SB" {if $settings.location_limit == 'SB'}selected="selected"{/if}>Solomon Islands</option>
							<option value="SC" {if $settings.location_limit == 'SC'}selected="selected"{/if}>Seychelles</option>
							<option value="SD" {if $settings.location_limit == 'SD'}selected="selected"{/if}>Sudan</option>
							<option value="SE" {if $settings.location_limit == 'SE'}selected="selected"{/if}>Sweden</option>
							<option value="SG" {if $settings.location_limit == 'SG'}selected="selected"{/if}>Singapore</option>
							<option value="SH" {if $settings.location_limit == 'SH'}selected="selected"{/if}>Saint Helena, Ascension and Tristan da Cunha</option>
							<option value="SI" {if $settings.location_limit == 'SI'}selected="selected"{/if}>Slovenia</option>
							<option value="SJ" {if $settings.location_limit == 'SJ'}selected="selected"{/if}>Svalbard and Jan Mayen</option>
							<option value="SK" {if $settings.location_limit == 'SK'}selected="selected"{/if}>Slovakia</option>
							<option value="SL" {if $settings.location_limit == 'SL'}selected="selected"{/if}>Sierra Leone</option>
							<option value="SM" {if $settings.location_limit == 'SM'}selected="selected"{/if}>San Marino</option>
							<option value="SN" {if $settings.location_limit == 'SN'}selected="selected"{/if}>Senegal</option>
							<option value="SO" {if $settings.location_limit == 'SO'}selected="selected"{/if}>Somalia</option>
							<option value="SR" {if $settings.location_limit == 'SR'}selected="selected"{/if}>Suriname</option>
							<option value="SS" {if $settings.location_limit == 'SS'}selected="selected"{/if}>South Sudan</option>
							<option value="ST" {if $settings.location_limit == 'ST'}selected="selected"{/if}>Sao Tome and Principe</option>
							<option value="SV" {if $settings.location_limit == 'SV'}selected="selected"{/if}>El Salvador</option>
							<option value="SX" {if $settings.location_limit == 'SX'}selected="selected"{/if}>Sint Maarten (Dutch part)</option>
							<option value="SY" {if $settings.location_limit == 'SY'}selected="selected"{/if}>Syrian Arab Republic</option>
							<option value="SZ" {if $settings.location_limit == 'SZ'}selected="selected"{/if}>Swaziland</option>
							<option value="TC" {if $settings.location_limit == 'TC'}selected="selected"{/if}>Turks and Caicos Islands</option>
							<option value="TD" {if $settings.location_limit == 'TD'}selected="selected"{/if}>Chad</option>
							<option value="TF" {if $settings.location_limit == 'TF'}selected="selected"{/if}>French Southern Territories</option>
							<option value="TG" {if $settings.location_limit == 'TG'}selected="selected"{/if}>Togo</option>
							<option value="TH" {if $settings.location_limit == 'TH'}selected="selected"{/if}>Thailand</option>
							<option value="TJ" {if $settings.location_limit == 'TJ'}selected="selected"{/if}>Tajikistan</option>
							<option value="TK" {if $settings.location_limit == 'TK'}selected="selected"{/if}>Tokelau</option>
							<option value="TL" {if $settings.location_limit == 'TL'}selected="selected"{/if}>Timor-Leste</option>
							<option value="TM" {if $settings.location_limit == 'TM'}selected="selected"{/if}>Turkmenistan</option>
							<option value="TN" {if $settings.location_limit == 'TN'}selected="selected"{/if}>Tunisia</option>
							<option value="TO" {if $settings.location_limit == 'TO'}selected="selected"{/if}>Tonga</option>
							<option value="TR" {if $settings.location_limit == 'TR'}selected="selected"{/if}>Turkey</option>
							<option value="TT" {if $settings.location_limit == 'TT'}selected="selected"{/if}>Trinidad and Tobago</option>
							<option value="TV" {if $settings.location_limit == 'TV'}selected="selected"{/if}>Tuvalu</option>
							<option value="TW" {if $settings.location_limit == 'TW'}selected="selected"{/if}>Taiwan, Province of China</option>
							<option value="TZ" {if $settings.location_limit == 'TZ'}selected="selected"{/if}>Tanzania, United Republic of</option>
							<option value="UA" {if $settings.location_limit == 'UA'}selected="selected"{/if}>Ukraine</option>
							<option value="UG" {if $settings.location_limit == 'UG'}selected="selected"{/if}>Uganda</option>
							<option value="UM" {if $settings.location_limit == 'UM'}selected="selected"{/if}>United States Minor Outlying Islands</option>
							<option value="US" {if $settings.location_limit == 'US'}selected="selected"{/if}>United States of America</option>
							<option value="UY" {if $settings.location_limit == 'UY'}selected="selected"{/if}>Uruguay</option>
							<option value="UZ" {if $settings.location_limit == 'UZ'}selected="selected"{/if}>Uzbekistan</option>
							<option value="VA" {if $settings.location_limit == 'VA'}selected="selected"{/if}>Holy See</option>
							<option value="VC" {if $settings.location_limit == 'VC'}selected="selected"{/if}>Saint Vincent and the Grenadines</option>
							<option value="VE" {if $settings.location_limit == 'VE'}selected="selected"{/if}>Venezuela, Bolivarian Republic of</option>
							<option value="VG" {if $settings.location_limit == 'VG'}selected="selected"{/if}>Virgin Islands, British</option>
							<option value="VI" {if $settings.location_limit == 'VI'}selected="selected"{/if}>Virgin Islands, U.S.</option>
							<option value="VN" {if $settings.location_limit == 'VN'}selected="selected"{/if}>Viet Nam</option>
							<option value="VU" {if $settings.location_limit == 'VU'}selected="selected"{/if}>Vanuatu</option>
							<option value="WF" {if $settings.location_limit == 'WF'}selected="selected"{/if}>Wallis and Futuna</option>
							<option value="WS" {if $settings.location_limit == 'WS'}selected="selected"{/if}>Samoa</option>
							<option value="YE" {if $settings.location_limit == 'YE'}selected="selected"{/if}>Yemen</option>
							<option value="YT" {if $settings.location_limit == 'YT'}selected="selected"{/if}>Mayotte</option>
							<option value="ZA" {if $settings.location_limit == 'ZA'}selected="selected"{/if}>South Africa</option>
							<option value="ZM" {if $settings.location_limit == 'ZM'}selected="selected"{/if}>Zambia</option>
							<option value="ZW" {if $settings.location_limit == 'ZW'}selected="selected"{/if}>Zimbabwe</option>
						</select>
					</td>
				</tr>

				<tr id="clearTable">
					<td colspan="2" align="right"><div class="floatRight"><input type="submit" class="greenButton" value="[[Save]]" /></div></td>
				</tr>

				</tbody>
			</table>

		</div>

		{*<div id="employerTab" class="ui-tabs-panel">*}
			{*<table width="100%">*}
				{*<thead>*}
				{*<tr>*}
					{*<th>[[Name]]</th>*}
					{*<th>[[Value]]</th>*}
				{*</tr>*}
				{*</thead>*}
				{*<tbody>*}

				{*<span style="color:red">Not implemented yet</span>*}
				{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
					{*<td>[[Sign up with Linked In]]</td>*}
					{*<td><input type="hidden" name="signup_employer_linkedin" value="0" /><input type="checkbox" name="signup_employer_linkedin" value="1"{if $settings.signup_employer_linkedin} checked="checked"{/if} /></td>*}
				{*</tr>*}
				{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
					{*<td>[[Sign up with Facebook]]</td>*}
					{*<td><input type="hidden" name="signup_employer_facebook" value="0" /><input type="checkbox" name="signup_employer_facebook" value="1"{if $settings.signup_employer_facebook} checked="checked"{/if} /></td>*}
				{*</tr>*}
				{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
					{*<td>[[Sign up with Google+]]</td>*}
					{*<td><input type="hidden" name="signup_employer_googleplus" value="0" /><input type="checkbox" name="signup_employer_googleplus" value="1"{if $settings.signup_employer_googleplus} checked="checked"{/if} /></td>*}
				{*</tr>*}

				{*<tr id="clearTable">*}
					{*<td colspan="2" align="right"><div class="floatRight"><input type="submit" class="greenButton" value="[[Save]]" /></div></td>*}
				{*</tr>*}

				{*</tbody>*}
			{*</table>*}
		{*</div>*}
		{*<div id="jobSeekerTab" class="ui-tabs-panel">*}
			{*<table width="100%">*}
				{*<thead>*}
				{*<tr>*}
					{*<th>[[Name]]</th>*}
					{*<th>[[Value]]</th>*}
				{*</tr>*}
				{*</thead>*}
				{*<tbody>*}

				{*<span style="color:red">Not implemented yet</span>*}
				{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
					{*<td>[[Sign up with Linked In]]</td>*}
					{*<td><input type="hidden" name="signup_jobseeker_linkedin" value="0" /><input type="checkbox" name="signup_jobseeker_linkedin" value="1"{if $settings.signup_jobseeker_linkedin} checked="checked"{/if} /></td>*}
				{*</tr>*}
				{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
					{*<td>[[Sign up with Facebook]]</td>*}
					{*<td><input type="hidden" name="signup_jobseeker_facebook" value="0" /><input type="checkbox" name="signup_jobseeker_facebook" value="1"{if $settings.signup_jobseeker_facebook} checked="checked"{/if} /></td>*}
				{*</tr>*}
				{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
					{*<td>[[Sign up with Google+]]</td>*}
					{*<td><input type="hidden" name="signup_jobseeker_googleplus" value="0" /><input type="checkbox" name="signup_jobseeker_googleplus" value="1"{if $settings.signup_jobseeker_googleplus} checked="checked"{/if} /></td>*}
				{*</tr>*}

				{*<tr id="clearTable">*}
					{*<td colspan="2" align="right"><div class="floatRight"><input type="submit" class="greenButton" value="[[Save]]" /></div></td>*}
				{*</tr>*}

				{*</tbody>*}
			{*</table>*}
		{*</div>*}


		<div id="ecommerceTab" class="ui-tabs-panel">

			<table class="basetable" width="100%">
				<tr class="headrow">
					<td>[[Name]]</td>
					<td>[[Value]]</td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Currency]]</td>
					<td>
						<select name="transaction_currency">
							{foreach from=$currencies key=code item=currency}
								<option value="{$code}" {if $settings.transaction_currency == $code}selected="selected"{/if}>{$code} - {$currency.caption}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><small>* [[This currency will be used for displaying your site services prices]]</small></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Tax]]</td>
					<td><input type="text" name="tax" value="{$settings.tax}" /> %</td>
				</tr>
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Billing Address]]</td>
                    <td><textarea name="send_payment_to" cols="50" rows="6">{$settings.send_payment_to}</textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><small>*&nbsp;[[This text will be displayed in invoices]]</small></td>
                </tr>
				<tr id="clearTable">
					<td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>
			</table>

		</div>
		{*<tr class="{cycle values = 'evenrow,oddrow'}">*}
		{*<td>[[Behavior With Escape HTML Tags]]</td>*}
		{*<td>*}
		{*<select name="escape_html_tags">*}
		{*<option value="">[[Raw output (unsafe, XSS possible)]]</option>*}
		{*<option value="htmlentities"{if $settings.escape_html_tags == 'htmlentities'} selected="selected"{/if}>[[Convert escape chars to ASCII symbols (beta)]]</option>*}
		{*<option value="htmlpurifier"{if $settings.escape_html_tags == 'htmlpurifier'} selected="selected"{/if}>[[Strip Tags]]</option>*}
		{*</select>*}
		{*</td>*}
		{*</tr>*}

		<div id="seoTab" class="ui-tabs-panel">
			<table width="100%">
				<thead>
				<tr>
					<th>[[Name]]</th>
					<th>[[Value]]</th>
				</tr>
				</thead>
				<tbody>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Homepage Title]]</td>
					<td><input type="text" name="home_page_title" value="{$settings.home_page_title}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Meta Description]]</td>
					<td>
						<textarea name="home_page_description" cols="50" rows="6">{$settings.home_page_description}</textarea>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Meta Keywords]]</td>
					<td><input type="text" name="home_page_keywords" value="{$settings.home_page_keywords}" /></td>
				</tr>

				<tr id="clearTable">
					<td colspan="2" align="right">
						<div class="floatRight">
							<input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
							<input type="submit" class="greenButton" value="[[Save]]" />
						</div>
					</td>
				</tr>

				</tbody>
			</table>

		</div>

		{if not $isSaas}
			<div id="mailTab" class="ui-tabs-panel">
				<table class="basetable" width="100%" >
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td class="strong"><input type="radio" name="smtp" value="1" {if $settings.smtp == 1}checked="checked"{/if} /> [[SMTP]]</td>
					</tr>
				</table>
				<div class="smtp">
					<table class="basetable" width="100%">
						<tr class="headrow">
							<td>[[Name]]</td>
							<td>[[Value]]</td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[SMTP Sender Mail]]</td>
							<td><input type="text" name="smtp_sender" value="{$settings.smtp_sender}" /></td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[SMTP Port]]</td>
							<td><input type="text" name="smtp_port" value="{$settings.smtp_port}" /></td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[SMTP Host]]</td>
							<td><input type="text" name="smtp_host" value="{$settings.smtp_host}" /></td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[SMTP Security]]</td>
							<td>
								<input type="radio" name="smtp_security" value="none" {if $settings.smtp_security != 'ssl' && $settings.smtp_security != 'tls'}checked="checked"{/if} />[[None]]&nbsp;&nbsp;
								<input type="radio" name="smtp_security" value="ssl" {if $settings.smtp_security == 'ssl'}checked="checked"{/if} />[[SSL]]&nbsp;&nbsp;
								<input type="radio" name="smtp_security" value="tls" {if $settings.smtp_security == 'tls'}checked="checked"{/if} />[[TLS]]

							</td>
						</tr>
						<tr>
							<td colspan="2"><small>* [[Look for your SMTP mail host requirements]]</small></td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[Username]]</td>
							<td><input type="text" name="smtp_username" value="{$settings.smtp_username}" /></td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td>[[Password]]</td>
							<td><input type="password" name="smtp_password" value="{$settings.smtp_password}" /></td>
						</tr>
					</table>
				</div>
				<div class="sendmail">
					<table class="basetable" width="100%">
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td style="font-weight: bold;"><input type="radio" name="smtp" value="0"  {if $settings.smtp == 0}checked="checked"{/if} /> [[Sendmail]]</td>
						</tr>
					</table>
					<table class="basetable" width="100%">
						<tr class="headrow">
							<td>[[Name]]</td>
							<td>[[Value]]</td>
						</tr>
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td width="226px">[[Path to sendmail]]</td>
							<td><input type="text" name="sendmail_path" value="{$settings.sendmail_path}" /></td>
						</tr>
					</table>
				</div>
				<div class='sendmail'>
					<table class="basetable" width="100%">
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td class="strong"><input type="radio" name="smtp" value="3"  {if $settings.smtp == 3}checked="checked"{/if} /> [[PHP Mail Function]]</td>
						</tr>
					</table>
				</div>
				<div class="sendmail">
					<table class="basetable" width="100%">
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td width="226px">[[Check mail set up]]</td>
							<td><input id="checkMail" type="submit" value="[[Check]]" class="grayButton"/></td>
						</tr>
					</table>
				</div>
				<table class="basetable" width="100%">
					<tr id="clearTable">
						<td colspan="2" align="right">
							<div class="floatRight">
								<input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
								<input type="submit" class="grayButton" value="[[Save]]" />
							</div>
						</td>
					</tr>
				</table>
			</div>
		{/if}
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		$("#settingsPane").tabs();
        checkUncheckIPBlock();
        checkUncheckCacheLifetime();
		$("#maintenance_mode_").click(function() {
			checkUncheckIPBlock();
		});
        $("#enableCache").click(function() {
            checkUncheckCacheLifetime();
        });

		$("#checkMail").click(function () {

			var preloader = $(this).after(getPreloaderCodeForFieldId("checkMailLoader"));
			$("#checkMail").attr("disabled", "disabled");

			$.ajax({
				type:"POST",
				url:window.SJB_GlobalSiteUrl + "/system/miscellaneous/mail_check/",
				data:$("#settingsPane").serialize(),
				success:function (html) {
							$(preloader).next("span").remove();
							var result = JSON.parse(html);
							$(".message").remove();
							$(".error").remove();
							if (result["status"] == true) {
								$("#settingsPane").before('<p class="' + result["type"] + '">[[Your mail is set up correctly and functions fine.]]</p>');
							}
							if (result["status"] == false) {
								$("#settingsPane").before('<p class="' + result["type"] + '">[[Your mail is not functioning. Please check Admin Panel and server settings.]]</p>');
							}
							if (result["status"] == "fieldError") {
								var fieldCaption = {
									"smtp_host" : "[[SMTP Host]]",
									"smtp_port" : "[[SMTP Port]]",
									"smtp_sender" : "[[SMTP Sender Mail]]",
									"smtp_username" : "[[Username]]",
									"smtp_password" : "[[Password]]",
									"sendmail_path" : "[[Path to sendmail]]",
									"system_email" : "[[System email]]"
								};
								var messages = result["message"];
								$.each(messages, function(key) {
									if (key == "EMPTY_VALUE") {
										$.each(messages[key], function(key, value) {
											$("#settingsPane").before('<p class="' + result["type"] + '">"' + fieldCaption[value] + '" [[field is empty.]]</p>');
										});
									}

									if (key == "NOT_VALID") {
										$.each(messages[key], function(key, value) {
											$("#settingsPane").before('<p class="' + result["type"] + '">"' + fieldCaption[value] + '" [[field is not valid.]]</p>');
										});
									}
								});
							}
							$("#checkMail").attr("disabled", "");
							$(window).scrollTop(0);
						}
			});
			return false;
		});

		$('[name="search_by_location"]').change(function() {
			$('.location__sub-setting').toggle($('[name="search_by_location"]:checked').length > 0);
		}).change();
	});

	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
            if ($(this).css("display") == "block") {
                butt.children(".setting_icon").html("[-]");
            } else {
                butt.children(".setting_icon").html("[+]");
            }
        });
	});

    function checkUncheckIPBlock() {
        if ($("#maintenance_mode_").attr("checked"))
            $("input[name='maintenance_mode_ip']").removeAttr("disabled");
        else
            $("input[name='maintenance_mode_ip']").attr("disabled", "disabled");
    }

    function checkUncheckCacheLifetime() {
        if ($("#enableCache").attr("checked")) {
            $("input[name='cacheHours']").removeAttr("disabled");
            $("input[name='cacheMinutes']").removeAttr("disabled");
        }
        else {
            $("input[name='cacheHours']").attr("disabled", "disabled");
            $("input[name='cacheMinutes']").attr("disabled", "disabled");
        }
    }

    function applySettings(id) {
        var div_id = $(id).parents('.ui-tabs-panel').attr('id');
        div_id = '#' + div_id;
        $('#page').attr('value', div_id);
        $('#action').attr('value', 'apply_settings');
    }

    var page = '{$page}';
    if (page) {
        $("#settingsPane ul li").each(function(){
            if ($('a', this).attr('href') == page) {
                var cl = $(this).attr('class') + ' ui-tabs-selected';
                $(this).attr('class', cl);
            } else {
                $(this).attr('class', 'ui-tabs-unselect');
            }
        });
    }
</script>

