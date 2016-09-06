{assign var="complexField" value=$form_field.id scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
<div class="complex-wrapper">
	<div id='complexFields_{$complexField}' class="complex clearfix">
		{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}

			{if $complexElementKey != 1}
				<div id='complexFieldsAdd_{$complexField}_{$complexElementKey}' class="clearfix complex">
			{/if}
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == "ED_From" || $form_field.id == "WE_From"}
					<div class="form-group form-group__half margin form-group__range">
						<label class="form-label">{tr}{$form_field.caption}{/tr|escape:'html'} {if $form_field.is_required}*{/if}</label>
						{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
						{if $form_field.instructions}
							{assign var="instructionsExist" value="1"}
							{include file="instructions.tpl" form_field=$form_field}
						{/if}
					</div>
				{elseif $form_field.id == "ED_To" || $form_field.id == "WE_To"}
					<div class="form-group form-group__half pull-right margin form-group__range">
						<label class="form-label">{tr}{$form_field.caption}{/tr|escape:'html'} {if $form_field.is_required}*{/if}</label>
						{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
						{if $form_field.instructions}
							{assign var="instructionsExist" value="1"}
							{include file="instructions.tpl" form_field=$form_field}
						{/if}
					</div>
				{elseif $form_field.id == "ED_DegreeSpecialty" || $form_field.id == "WE_JobTitle"}
					<div class="form-group form-group__half margin">
						<label class="form-label">{tr}{$form_field.caption}{/tr|escape:'html'} {if $form_field.is_required}*{/if}</label>
						{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
						{if $form_field.instructions}
							{assign var="instructionsExist" value="1"}
							{include file="instructions.tpl" form_field=$form_field}
						{/if}
					</div>
				{elseif $form_field.id == "ED_UniversityInstitution" || $form_field.id == "WE_Company"}
					<div class="form-group form-group__half pull-right margin">
						<label class="form-label">{tr}{$form_field.caption}{/tr|escape:'html'} {if $form_field.is_required}*{/if}</label>
						{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
						{if $form_field.instructions}
							{assign var="instructionsExist" value="1"}
							{include file="instructions.tpl" form_field=$form_field}
						{/if}
					</div>
				{else}
					<div class="form-group">
						<label class="form-label">{tr}{$form_field.caption}{/tr|escape:'html'} {if $form_field.is_required}*{/if}</label>
						{input property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
						{if $form_field.instructions}
							{assign var="instructionsExist" value="1"}
							{include file="instructions.tpl" form_field=$form_field}
						{/if}
					</div>
				{/if}
			{/foreach}
			{if $complexElementKey != 1}
				<div class="form-group text-right form-group__remove">
					<button class="remove btn btn__white" onclick='removeComplexField_{$complexField}({$complexElementKey}); return false;' >[[Remove]]</button>
				</div>
				</div>
			{elseif $complexElementKey == 1}
				</div>
			{/if}
		{/foreach}
</div>
<div class="form-group form-group__add">
	{assign var="field_caption" value=$caption|tr}
	<button class="add btn btn__white" onclick='addComplexField_{$complexField}(); return false;' >[[Add $field_caption]]</button>
</div>
{javascript}
<script type="text/javascript">

	var i_{$complexField} = {$complexElementKey} + 1;

	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yyyy");

	$('.form-control__visible').datepicker({
		language: '{$GLOBALS.current_language}',
		autoclose: true,
		format: 'mm.yyyy',
		startView: 1,
		minViewMode: 1,
		startDate: new Date(1940, 1 - 1, 1),
		endDate: '+10y',
	});

	$('.form-control__visible').on('change', function() {
		$(this).closest('.form-group').find('input[type="hidden"]').val($(this).data('datepicker').getFormattedDate(dFormat));
	});

	function addComplexField_{$complexField}() {ldelim}
		var id = "complexFieldsAdd_{$complexField}_" + i_{$complexField};
		var oldField = $('#complexFields_{$complexField}');
		var conf;
		var data = [];
		var oldTextAreas = oldField.find('.inputField textarea');
		oldTextAreas.each(function(){ldelim}
			var name = $(this).attr('name');
			var editor = CKEDITOR.instances[name];
			if (editor) {ldelim}
				conf = editor.config;
				data[name] =  editor.getData();
				editor.destroy(true);
				$(this).next().remove();
			{rdelim}
		{rdelim});

		var newField = oldField.clone();

		oldTextAreas.each(function(){ldelim}
			var name = $(this).attr('name');
			CKEDITOR.replace(name, conf);
			var editor = CKEDITOR.instances[name];
			editor.setData(data[name]);
		{rdelim});

		$("<div id='" + id + "'/>").addClass('complex clearfix').appendTo($('#complexFields_{$complexField}').closest('.complex-wrapper'));
		newField.removeClass('complex clearfix');
		newField.append('<div class="form-group text-right form-group__remove"><button class="remove btn btn__white" onclick="removeComplexField_{$complexField}(' + i_{$complexField} + '); return false;">[[Remove]]<\/button><\/div>');

		var str = newField.html();
		re = /\[1]/g;
		var newStr = str.replace(re, '['+i_{$complexField}+']');
		newField.html(newStr);
		newField.appendTo('#' + id);
		newField.find('.inputField textarea').each(function(){ldelim}
			$(this).val('');
			CKEDITOR.replace($(this).attr('name'), conf);
		{rdelim});

		newField.closest('.form-group').find('textarea').val('');
		newField.find('.cke').last().remove();

		$('#' + id + ' input[type=text]').val('');
		$("#" + id + " .form-group__range input").attr('value', ''); //почему то только так обнуляется значение в инпуте

		$('.form-control__visible').datepicker({
			language: '{$GLOBALS.current_language}',
			autoclose: true,
			format: 'mm.yyyy',
			startView: 1,
			minViewMode: 1,
			startDate: new Date(1940, 1 - 1, 1),
			endDate: '+10y',
		});

		$('.form-control__visible').on('change', function() {
			$(this).closest('.form-group').find('input[type="hidden"]').val($(this).data('datepicker').getFormattedDate(dFormat));
		});

		$('.ui-datepicker-trigger').on('click', function () {
			$(this).closest('.form-group').find('.form-control__visible').focus();
		});

		if (typeof window.instructionFunc == 'function') {
			instructionFunc();
		}

		i_{$complexField}++;

	{rdelim}

	function removeComplexField_{$complexField}(id) {ldelim}
		$('#complexFieldsAdd_{$complexField}_' + id).remove();
	{rdelim}
</script>
{/javascript}
{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
