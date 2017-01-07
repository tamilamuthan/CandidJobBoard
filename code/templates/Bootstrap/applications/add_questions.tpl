{title}[[Create New Screening Questionnaire]]{/title}
<h1 class="my-account-title">[[My Account]]</h1>
<div class="my-account-list">
    <ul class="nav nav-pills">
        <li class="presentation"><a href="{$GLOBALS.site_url}/my-listings/job/">[[Job Postings]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Applications]]</a></li>
        <li class="presentation active"> <a href="{$GLOBALS.site_url}/system/applications/screening-questionnaires/">[[Screening Questions]]</a></li>
        <li class="presentation"> <a href="{$GLOBALS.site_url}/edit-profile/">[[Company Profile]]</a></li>
    </ul>
</div>

{if $action == 'edit'}
<div class="BreadCrumbs">
	<p><a href="{$GLOBALS.site_url}/my-listings/job/">[[My Account]]</a> &#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a>
	&#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/edit/{$questionnaire_sid}">[[Edit Questionnaire]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-questions/{$questionnaire_sid}">[[Questions]]</a> &#187; [[Edit Question]]</p>
</div>
{/if}

<h3>[[Add Question]]</h3>

{include file="../classifieds/field_errors.tpl" field_errors=$errors}

<form method="post" action="" id="add-listing-form" class="form">

<input type="hidden" name="action" value="add" />
{foreach from=$form_fields key=field_name item=form_field}
{if $form_field.id == 'type'}
	<div class="form-group">
		<label class="form-label">[[$form_field.caption]] &nbsp;{if $form_field.is_required}*{/if}</label>
		{input property=$form_field.id  template='radio.tpl'}
	</div>
{else}
	<div class="form-group">
		<label class="form-label">[[$form_field.caption]] &nbsp;{if $form_field.is_required}*{/if}</label>
		{input property=$form_field.id}
	</div>
{/if}
{/foreach}
    
<div class="form-group" id="boolean" {if !$answer_boolean}style="display:none"{/if}>
	<label class="form-label">[[Answers]] &nbsp;</label>
    <div>
        [[Yes]]&nbsp;<input class="form-control" type="hidden" name="answer_boolean[]" value="Yes" />
		<select name="score_boolean[]">
			<option value="no" {if $score_boolean.yes == 'no'} selected="selected"{/if}>[[Don't assign score]]</option>
			<option value="0" {if $score_boolean.yes == '0'} selected="selected"{/if}>[[Not acceptable - 0]]</option>
			<option value="1" {if $score_boolean.yes == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
			<option value="2" {if $score_boolean.yes == 2} selected="selected"{/if}>[[Good - 2]]</option>
			<option value="3" {if $score_boolean.yes == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
			<option value="4" {if $score_boolean.yes == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
		</select>

		&nbsp;&nbsp;[[No]]&nbsp;<input type="hidden" name="answer_boolean[]" value="No" class="form-control"/>
		<select name="score_boolean[]">
			<option value="no" {if $score_boolean.no == 'no'} selected="selected"{/if}>[[Don't assign score]]</option>
			<option value="0" {if $score_boolean.no == '0'} selected="selected"{/if}>[[Not acceptable - 0]]</option>
			<option value="1" {if $score_boolean.no == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
			<option value="2" {if $score_boolean.no == 2} selected="selected"{/if}>[[Good - 2]]</option>
			<option value="3" {if $score_boolean.no == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
			<option value="4" {if $score_boolean.no == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
		</select>
	</div>
</div>
    
<div class="form-group" id="answers"  {if !$answers}style="display:none"{/if}>
	<label class="form-label">[[Answers]] &nbsp;</label>
		{if $answers}
            {foreach from=$answers key=key item=answer}
                <div id="answerBlock{$key}" >
                    <div style="float:left;"><input class="form-control" type="text" name="answer[]" value="{$answer}" /></div>
                    <div style="float:left;">
                        <select name="score[]" class="form-control">
                            <option value="no" {if $score.$key == 'no'} selected="selected"{/if}>[[Don't assign score]]</option>
                            <option value="0" {if $score.$key == '0'} selected="selected"{/if}>[[Not acceptable - 0]]</option>
                            <option value="1" {if $score.$key == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
                            <option value="2" {if $score.$key == 2} selected="selected"{/if}>[[Good - 2]]</option>
                            <option value="3" {if $score.$key == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
                            <option value="4" {if $score.$key == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
                        </select>
                    </div>
                    <div>&nbsp;&nbsp;<a href="#" onclick="deleteAnswerBlock('answerBlock{$key}'); return false;" class="remove">[[Delete]]</a></div>
                </div>
            {/foreach}
            <div class="clr"></div>
		    <div id="answerAdd"></div>
		    <div id="add_answer"><a href="#" onclick="addAnswerBlock();  return false;" class="add">[[Add Answer]]</a></div>
        {else}
            <div id="answerBlock">
                <input type="text" name="answer[]" value=""/>
                <select name="score[]">
                        <option value="no">[[Don't assign score]]</option>
                        <option value="0">[[Not acceptable - 0]]</option>
                        <option value="1">[[Acceptable - 1]]</option>
                        <option value="2">[[Good - 2]]</option>
                        <option value="3">[[Very Good - 3]]</option>
                        <option value="4">[[Excellent - 4]]</option>
                 </select>
                 &nbsp;&nbsp;<a href="#" onclick="deleteAnswerBlock('answerBlock'); return false;">[[Delete]]</a>
            </div>
            <div id="answerAdd"></div>
            <div id="add_answer"><a href="#" onclick="addAnswerBlock();  return false;">[[Add Answer]]</a></div>
        {/if}
    
		<div id="answerBlockNone" style="display: none">
		        <input type="text" name="answer[]" value="" />
		        <select name="score[]">
			        <option value="no">[[Don't assign score]]</option>
			        <option value="0">[[Not acceptable - 0]]</option>
			        <option value="1">[[Acceptable - 1]]</option>
			        <option value="2">[[Good - 2]]</option>
			        <option value="3">[[Very Good - 3]]</option>
			        <option value="4">[[Excellent - 4]]</option>
		        </select>
                 &nbsp;&nbsp;<a href="#" onclick="deleteAnswerBlock('ANSWER_BLOCK'); return false;">[[Delete]]</a>
		</div>
	</div>
<div class="form-group">
	<label class="form-label">&nbsp; &nbsp;</label>
	{if $action == 'edit'}
       <input type="submit" name="action_add" value="[[Save]]" class="btn btn__orange btn__bold" />
    {else}
        <input type="submit" name="action_add" value="[[Add]]" class="btn btn__orange btn__bold" />
    {/if}
</div>
</form>

{javascript}
<script type="text/javascript">
var i = 1;
function addAnswerBlock() {
	var id = "answerAdd"+i;
	$("<div id='"+id+"'><\/div>").appendTo("#answerAdd");
	var block = $('#answerBlockNone').clone();
	block.appendTo('#'+id); 
	block.show();
	$('#'+ id +' input[type=text]').val('');
    var html = $('#'+id).html();
    $('#'+id).html(html.replace("ANSWER_BLOCK",id));
	console.log($('#'+id).html());
    //$('#'+ id).html($('#'+ id).html() + "&nbsp;&nbsp;<a href='#' onclick=\"deleteAnswerBlock('"+id+"'); return false;\">[[Delete]]<\/a>");
	i++;
}

function deleteAnswerBlock(id) {
	$('#'+ id).remove();
}
</script>
{/javascript}