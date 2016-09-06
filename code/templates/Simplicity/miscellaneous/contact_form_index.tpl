{extends '../main/index.tpl'}
{block name='main_content'}
	{$smarty.block.parent}
	{module name='miscellaneous' function='contact_form'}
{/block}