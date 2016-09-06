{breadcrumbs}
	<a href="{$GLOBALS.site_url}/edit-email-templates/">[[Email Templates]]</a> &#187;
	[[Edit]] "[[{$tplInfo.name}]]" [[Template]]
{/breadcrumbs}
<h1><img src="{image}/icons/contactbook32.png" border="0" alt="" class="titleicon"/>[[Edit]] "[[{$tplInfo.name}]]" [[Template]]</h1>

<fieldset>
	<legend>&nbsp;[[Edit Email Template]]</legend>
	{include file='../users/field_errors.tpl'}
	<p>[[Fields marked with an asterisk (<span class="required">*</span>) are mandatory]]</p>
	<form method="post" enctype="multipart/form-data" action="" id="email-template-edit">
		<input type="hidden" id="action" name="action" value="save_info"/>
		<table>
			{foreach from=$form_fields item=form_field}
				<tr {if $form_field.id == 'name'}style="display: none;"{/if}>
					<td valign="top">[[{$form_field.caption}]]</td>
					<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
					<td>{if $form_field.id eq 'file'}{input property=$form_field.id template="file_et.tpl"}{else}{input property=$form_field.id}{/if}</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="3">
                    <div class="floatRight">
                        <input type="submit" id="apply" name="apply" value="[[Apply]]" class="grayButton"/>
                        <input type="submit" name="save" value="[[Save]]" class="grayButton" />
                    </div>
                </td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">
	$('#apply').click(
		function(){
			$('#action').attr('value', 'apply_info');
		}
	);
</script>
