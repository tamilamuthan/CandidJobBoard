
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>SmartJobBoard [[Admin Panel]]</title>
	<link rel="StyleSheet" type="text/css" href="{image src="auth.css"}"/>
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
{literal}
	<script type="text/javascript">$(function () {
		$('input[name=username]').focus();
	})</script>{/literal}
</head>
<body>
	<div id="loginForm">
		<div id="headerLogo">
			<img src="{image}authLogo.png" border="0" width="199" height="30"/><br/>
			<span>SmartJobBoard [[version]] {$GLOBALS.version.major}.{$GLOBALS.version.minor}.{$GLOBALS.version.build}</span>
		</div>
		<div class="clr"></div>
		<div id="authFormLogin">
			<form method="post" action="">
				{$form_hidden_params}
				{if $ERROR}
					{foreach from=$ERROR item=error key=errorCode}
						{if $errorCode == "LOGIN_PASS_NOT_CORRECT"}
							<fieldset id="errorAuth">[[The username or password you entered is incorrect]]</fieldset>
						{/if}
					{/foreach}
				{/if}
				<label>[[Username]]:<br/><input type="text" name="username" /></label>
				<label>[[Password]]:<br/><input type="password" name="password" /></label>

				<input type="submit" value="[[Login]]" id="loginButton"/>
			</form>
		</div>
	</div>
	<div class="clr"></div>
	<div id="copyright">[[Copyright]]  {$smarty.now|date_format:"%Y"} &copy; SmartJobBoard.com [[All rights reserved]]</div>
</body>
</html>