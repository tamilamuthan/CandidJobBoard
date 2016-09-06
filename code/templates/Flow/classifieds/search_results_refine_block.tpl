{if !empty($currentSearch)}
	<div class="current-search">
		<div class="current-search__title">[[Current Search]]</div>
		{capture name="urlParams"}searchId={$searchId|escape:'url'}&amp;action=undo{/capture}
		{foreach from=$currentSearch item="fieldInfo" key="fieldID"}
			{foreach from=$fieldInfo.field item="fieldValue" key="fieldType"}
				{foreach from=$fieldValue item="val" key="realVal"}
					<a class="badge" href="?{$smarty.capture.urlParams}&amp;param={$fieldID}&amp;type={$fieldType}&amp;value={$realVal|escape:'url'}">{tr}{$val}{/tr|escape}</a>
				{/foreach}
			{/foreach}
		{/foreach}
	</div>
{/if}

{if $currentSearch.GooglePlace.field}
	<div class="refine-search__block">
		<a class="btn__refine-search" role="button" data-toggle="collapse" href="#refine-block-radius" aria-expanded="true" aria-controls="refine-block-radius}">
			[[Search within]]
		</a>
		<div class="collapse in clearfix dropdown" id="refine-block-radius">
			<a href="#" class="refine-search__item dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				50 [[{$GLOBALS.settings.radius_search_unit}]]
			</a>
			<div class="dropdown-menu">
				<a class="refine-search__item refine-search__item-radius" href="#" data-value="10">
					<span class="refine-search__value">10 [[{$GLOBALS.settings.radius_search_unit}]]</span>
				</a>
				<a class="refine-search__item refine-search__item-radius" href="#" data-value="20">
					<span class="refine-search__value">20 [[{$GLOBALS.settings.radius_search_unit}]]</span>
				</a>
				<a class="refine-search__item refine-search__item-radius" href="#" data-value="50">
					<span class="refine-search__value">50 [[{$GLOBALS.settings.radius_search_unit}]]</span>
				</a>
				<a class="refine-search__item refine-search__item-radius" href="#" data-value="100">
					<span class="refine-search__value">100 [[{$GLOBALS.settings.radius_search_unit}]]</span>
				</a>
				<a class="refine-search__item refine-search__item-radius" href="#" data-value="200">
					<span class="refine-search__value">200 [[{$GLOBALS.settings.radius_search_unit}]]</span>
				</a>
			</div>
		</div>
	</div>
{/if}

{if !empty($refineFields)}
	{capture name="trLess"}[[Less]]{/capture}
	{capture name="trMore"}[[More]]{/capture}

	{capture name="urlParams"}searchId={$searchId|escape:'url'}&amp;action=refine{/capture}
	{foreach from=$refineFields item=refineField}
		{if $refineField.show && $refineField.count_results}
			<div class="refine-search__block">
				<a class="btn__refine-search" role="button" data-toggle="collapse" href="#refine-block-{$refineField.field_name}" aria-expanded="true" aria-controls="refine-block-{$refineField.field_name}">
					{assign var="field_caption" value=$refineField.caption|tr}
					[[Refine by $field_caption]]
				</a>
				<div class="collapse in clearfix" id="refine-block-{$refineField.field_name}">
					{foreach from=$refineField.search_result item=val name=fieldValue}
						{capture name="refineFieldCriteria"}{$refineField.field_name}{if in_array($refineField.type, array('string'))}[multi_like_and]{else}[multi_like]{/if}[]={if $val.sid}{$val.sid}{else}{$val.value|escape:'url'}{/if}{/capture}
						{if $smarty.foreach.fieldValue.iteration == 7}
							<div class="less-more" style="display: none">
						{/if}
						<a class="refine-search__item" href="?{$smarty.capture.urlParams}&amp;{$smarty.capture.refineFieldCriteria}">
							<span class="refine-search__value">{tr}{$val.value}{/tr|escape}</span>
							<span class="refine-search__count">{if empty($refineField.criteria)}&nbsp;({$val.count}){/if}</span>
						</a>
					{/foreach}
					{if $smarty.foreach.fieldValue.total >= 7}
						</div><a href="#" class="less-more__btn link">{$smarty.capture.trMore}</a>
					{/if}
				</div>
			</div>
		{/if}
	{/foreach}
{/if}
{if !$GLOBALS.is_ajax}
	<div id="refine-block-preloader"></div>
{/if}
{javascript}
	<script>
		$(document).on('click', '.less-more__btn', function(e) {
			e.preventDefault();
			var butt = $(this);
			butt.toggleClass('collapse');
			$(this).prev('.less-more').slideToggle('normal', function() {
				if ($(this).css('display') == 'block') {
					butt.html('{$smarty.capture.trLess|escape}');
				} else {
					butt.html('{$smarty.capture.trMore|escape}');
				}
			});
		});
	</script>
{/javascript}