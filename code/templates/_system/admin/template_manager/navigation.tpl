{breadcrumbs}
	{foreach name="foreach" item="item" from=$navigation}
		{if $item.reference eq ""}
			[[{$item.name}]]
		{else}
			<a href="{$item.reference}">[[{$item.name}]]</a>
		{/if}
		{if not $smarty.foreach.foreach.last} &#187; {/if}
	{/foreach}
{/breadcrumbs}

<h1><img src="{image}/icons/article32.png" border="0" alt="" class="titleicon"/> [[{$title}]]</h1>

{if !empty($errors.CANT_DELETE_FILES)}
	<p class="error">[[The following files could not be removed]]:</p>
	<p class="errorList">
	{foreach from=$errors.CANT_DELETE_FILES key=key item=file}
		-{$file};<br />
	{/foreach}
	</p>
{elseif !empty($result)}
	<p class="message">[[{$result}]]</p>
{/if}

<p>[[Active theme]]: <b>{$GLOBALS.settings.TEMPLATE_USER_THEME}</b></p>
