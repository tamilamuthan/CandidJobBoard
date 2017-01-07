<div class="BreadCrumbs">
	<p><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a> &#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a> &#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/edit/{$sid}/">[[Edit Questionnaire]]</a> &#187; [[Questions]]</p>
</div>

<h1>[[Questions of]] &quot;{$questionnarieInfo.name}&quot;</h1>

{include file="../classifieds/field_errors.tpl" field_errors=$errors}

{if $edit}
	<p class="message">[[Your changes were successfully saved]]</p>
{/if}

<div class="row" style="padding-top: 20px;">

<div class="col-md-7">

<table cellspacing="0" id="edit-questions" class="table table-condensed table-striped">
	<thead>
		<tr>
			<th class="tableLeft">&nbsp;</th>
			<th width="5%" colspan="2">[[Order]]</th>
			<th width="25%">&nbsp;[[Name]]</th>
			<th width="20%">[[Required]]</th>
			<th width="40%">[[Answer Type]]</th>
			<th width="10%" colspan="2">[[Actions]]</th>
			<th class="tableRight">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$questions item=question  name=question_block}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td></td>
			<td>
                {if $smarty.foreach.question_block.iteration < $smarty.foreach.question_block.total}
               		<a href="?action=move_down&amp;question={$question.sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
                {/if} 
            </td>
            <td>
                {if $smarty.foreach.question_block.iteration > 1}
                	<a href="?action=move_up&amp;question={$question.sid}"><img src="{image}b_up_arrow.gif" border="0" alt="" /></a>
                {/if} 
            </td>
			<td><span class="strong">{$question.caption}</span></td>
			<td>{if $question.is_required}[[Yes]]{else}[[No]]{/if}</td>
			<td>{if $question.type=='boolean'}[[Yes/No]]{elseif $question.type=='string'}[[Text]]{elseif $question.type=='multilist'}[[List of answers with multiple choice]]{elseif  $question.type=='list'}[[List of answers with single choice]]{/if}</td>
			<td><a href="{$GLOBALS.site_url}/edit-question/{$question.sid}">[[Edit]]</a></td>
			<td><a href="{$GLOBALS.site_url}/edit-questions/{$question.questionnaire_sid}/{$question.sid}/?action=delete" onclick="return confirm('{capture name="areYouSureToDelete"}[[Are you sure you want to delete this question?]]{/capture}{$smarty.capture.areYouSureToDelete|escape:'quotes'}')">[[Delete]]</a></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="9" class="separateListing"></td>
		</tr>
	{/foreach}
	</tbody>
</table>

</div>

<div class="col-md-5">

<form method="post" action="" class="form" style="margin-left:0; background-color: white; padding: 10px;">
    <input type="hidden" name="action" value="add" />

    <div class="row">
        <div class="col-md-12">
            {foreach from=$form_fields key=field_name item=form_field}
            <div class="form-group">
                {if $form_field.id == 'type'}
                    <label class="form-label">[[$form_field.caption]] <span class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</span></label>
		            {input property=$form_field.id template='radio.tpl'}
                {else}
                    <label class="form-label">[[$form_field.caption]] <span class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</span></label>
		            {input property=$form_field.id}
                {/if}
            </div>
            {/foreach}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group" id="boolean" {if !$answer_boolean}style="display:none"{/if}>
                <label class="form-label">[[Answers]] <span class="inputReq">&nbsp</span></label>

                <table>
                    <tr>
                      <td>[[Yes]]&nbsp;<input type="hidden" name="answer_boolean[]" value="Yes" /></td>
                      <td>
		                <select name="score_boolean[]">
			            <option value="no" {if $score_boolean.0 == 'no'} selected="selected"{/if}>[[Don’t assign score]]</option>
			            <option value="0" {if $score_boolean.0 == 0} selected="selected"{/if}>[[Not acceptable - 0]]</option>
			            <option value="1" {if $score_boolean.0 == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
			            <option value="2" {if $score_boolean.0 == 2} selected="selected"{/if}>[[Good - 2]]</option>
			            <option value="3" {if $score_boolean.0 == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
			            <option value="4" {if $score_boolean.0 == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
		                </select>
                      </td>
                    </tr>
                    <tr>
		              <td>[[No]]&nbsp; <input type="hidden" name="answer_boolean[]" value="No" /></td>
                      <td>
		                <select name="score_boolean[]">
			            <option value="no" {if $score_boolean.1 == 'no'} selected="selected"{/if}>[[Don’t assign score]]</option>
			            <option value="0" {if $score_boolean.1 == 0} selected="selected"{/if}>[[Not acceptable - 0]]</option>
			            <option value="1" {if $score_boolean.1 == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
			            <option value="2" {if $score_boolean.1 == 2} selected="selected"{/if}>[[Good - 2]]</option>
			            <option value="3" {if $score_boolean.1 == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
			            <option value="4" {if $score_boolean.1 == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
		                </select>
                      </td>
                    </tr>
                </table>
		    </div>

            <div class="form-group" id="answers"  {if !$answers}style="display:none"{/if}>
                <label class="form-label">[[Answers]] <span class="inputReq">&nbsp</span></label>
                <div id="add_answer" style="margin-top: 5px; margin-bottom: 5px;">
                     <a href="#" onclick="addAnswerBlock();  return false;" class="add">[[Add Answer]]</a>
                </div>
		        
                {if $answers}
                    {foreach from=$answers key=key item=answer}
                        <div id="answerBlock{$key}" class="form-group">
                            <div style="float:left;padding-bottom:10px"><input type="text" name="answer[]" value="{$answer}" />&nbsp;</div>
                            <div style="float:left;">
                                <select name="score[]">
                                <option value="no" {if $score.$key == 'no'} selected="selected"{/if}>[[Don’t assign score]]</option>
                                <option value="0" {if $score.$key == '0'} selected="selected"{/if}>[[Not acceptable - 0]]</option>
                                <option value="1" {if $score.$key == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
                                <option value="2" {if $score.$key == 2} selected="selected"{/if}>[[Good - 2]]</option>
                                <option value="3" {if $score.$key == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
                                <option value="4" {if $score.$key == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
                                </select>
                            </div>
                            <div>&nbsp;&nbsp;
                             <a href="#" onclick="deleteAnswerBlock('answerBlock{$key}'); return false;" class="">[[Delete]]</a>
                            </div>
                        </div>
                        <div id="answerAdd"></div>
                    {/foreach}

                {else}
                    <div id="answerBlock" class="form-group">
                        <div style="float:left;padding-bottom:10px"><input type="text" name="answer[]" value="" />&nbsp;</div>
                        <div style="float:left;">
                            <select name="score[]">
                            <option value="no">[[Don’t assign score]]</option>
                            <option value="0">[[Not acceptable - 0]]</option>
                            <option value="1">[[Acceptable - 1]]</option>
                            <option value="2">[[Good - 2]]</option>
                            <option value="3">[[Very Good - 3]]</option>
                            <option value="4">[[Excellent - 4]]</option>
                            </select>
                        </div>
                        <div>&nbsp;&nbsp;
                            <a href="#" onclick="deleteAnswerBlock('answerBlock'); return false;" class="">[[Delete]]</a>
                        </div>
                    </div>
                    <div id="answerAdd"></div>
                {/if}

		        <div id="answerBlockNone" style="display: none">
		            <div style="float:left;padding-bottom:10px"><input type="text" name="answer[]" value="" />&nbsp;</div>
		            <div style="float:left;">
		                <select name="score[]">
			                <option value="no" >[[Don’t assign score]]</option>
			                <option value="0">[[Not acceptable - 0]]</option>
			                <option value="1">[[Acceptable - 1]]</option>
			                <option value="2">[[Good - 2]]</option>
			                <option value="3">[[Very Good - 3]]</option>
			                <option value="4">[[Excellent - 4]]</option>
		                </select>
		            </div>
		        </div>
	        </div>
        </div>
    </div>

    <div class="form-group">
	    <input type="submit" name="action_add" value="[[Add]]" class="btn btn__orange btn__bold" />
   </div>
</form>
</div>
</div>

{literal}
<script type="text/javascript">
<!--
var i = 1;
function addAnswerBlock() {
	var id = "answerAdd"+i;
	$("<div id='"+id+"' class=\"form-group\"><\/div>").appendTo("#answerAdd");
	var block = $('#answerBlockNone').clone();
	block.appendTo('#'+id); 
	block.show();
	$('#'+ id +' input[type=text]').val('');
	$('#'+ id).html($('#'+ id).html() + "<div>&nbsp;&nbsp;<a href='#' onclick=\"deleteAnswerBlock('"+id+"'); return false;\" class=\"\">{/literal}[[Delete]]{literal}<\/a><\/div><div class='clr'><\/div>");
	i++;
}

function deleteAnswerBlock(id) {
	$('#'+ id).remove();
}
//-->
</script>
{/literal}
