
function formatNumber(value)
{
	value = value.toString();
	var strAmount = value.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1' + langSettings.thousands_separator);
	strAmount = strAmount.replace(/\.(\d+)$/g, langSettings.decimal_separator + '$1');
	if (langSettings.showCurrencySign) {
		if (langSettings.currencySignLocation == 0) {
			if (langSettings.rightToLeft) {
				return strAmount + langSettings.currencySign;
			}
			return langSettings.currencySign + strAmount;
		}

		if (langSettings.rightToLeft) {
			return langSettings.currencySign + ' ' + strAmount;
		}
		return strAmount + ' ' + langSettings.currencySign;
	}
	return strAmount;
}

function unformatNumber(value)
{
	value = value.toString();
	if (langSettings.thousands_separator) {
		value = value.replace(new RegExp('\\' + langSettings.thousands_separator, 'g'), '');
	}
	if (langSettings.decimal_separator) {
		value = value.replace(new RegExp('\\' + langSettings.decimal_separator, 'g'), '.');
	}
	return parseFloat(value);
}

function roundNumber(value)
{
	var power = Math.pow(10, langSettings.decimals);
	return (Math.round(parseFloat(value) * power) / power).toFixed(langSettings.decimals);
}

