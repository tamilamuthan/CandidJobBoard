{assign var="complexField" value=$id scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
{if $complexField == "Education"}

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<div class="complex-block">
			{if {display property='ED_DegreeSpecialty' complexParent=$complexField complexStep=$complexElementKey}}
				<div class="listing-item__title">
					{display property='ED_DegreeSpecialty' complexParent=$complexField complexStep=$complexElementKey}
				</div>
			{/if}
			<div class="listing-item__info clearfix">
				{if {display property="ED_From" complexParent=$complexField complexStep=$complexElementKey} || {display property='ED_To' complexParent=$complexField complexStep=$complexElementKey}}
					<span class="listing-item__info--item listing-item__info--item-date">
						{display property="ED_From" complexParent=$complexField complexStep=$complexElementKey format="%b %Y"} - {display property='ED_To' complexParent=$complexField complexStep=$complexElementKey format="%b %Y"}
					</span>
				{/if}
				{if {display property='ED_UniversityInstitution' complexParent=$complexField complexStep=$complexElementKey}}
					<span class="listing-item__info--item listing-item__info--item-education">
						{display property='ED_UniversityInstitution' complexParent=$complexField complexStep=$complexElementKey}
					</span>
				{/if}
			</div>
		</div>
	{/foreach}

{elseif $complexField == "WorkExperience"}

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<div class="complex-block">
			{if {display property='WE_JobTitle' complexParent=$complexField complexStep=$complexElementKey}}
				<div class="listing-item__title">
					{display property='WE_JobTitle' complexParent=$complexField complexStep=$complexElementKey}
				</div>
			{/if}
			<div class="listing-item__info clearfix">
				{if {display property="WE_From" complexParent=$complexField complexStep=$complexElementKey} || {display property='WE_To' complexParent=$complexField complexStep=$complexElementKey}}
					<span class="listing-item__info--item listing-item__info--item-date">
						{display property="WE_From" complexParent=$complexField complexStep=$complexElementKey format="%b %Y"} - {display property='WE_To' complexParent=$complexField complexStep=$complexElementKey format="%b %Y"}
					</span>
				{/if}
				{if {display property='WE_Company' complexParent=$complexField complexStep=$complexElementKey}}
					<span class="listing-item__info--item listing-item__info--item-company">
						{display property='WE_Company' complexParent=$complexField complexStep=$complexElementKey}
					</span>
				{/if}
			</div>
			{if {display property='WE_Description' complexParent=$complexField complexStep=$complexElementKey}}
				<div>
					{display property='WE_Description' complexParent=$complexField complexStep=$complexElementKey}
				</div>
			{/if}
		</div>
	{/foreach}
{else}
	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<div class="complexField">
			{foreach from=$form_fields key=k item=form_field}
				{capture name="displayPropertyValue"}{display property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}{/capture}
				{if $smarty.capture.displayPropertyValue}
					<fieldset>
						<span class="strong"> {tr}{$form_field.caption}{/tr|escape}:&nbsp;</span>
						{$smarty.capture.displayPropertyValue}
					</fieldset>
				{/if}
			{/foreach}
		</div>
	{/foreach}

{/if}
{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
