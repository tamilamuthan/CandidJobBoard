{breadcrumbs}
    <a href="{$GLOBALS.site_url}/manage-users/{$userGroup.id|lower}/">
        [[{$userGroup.name} Profiles]]
    </a>
    &#187;
    [[Import {$userGroup.name}s]]
{/breadcrumbs}
<h1>[[Import {$userGroup.name}s]]</h1>
{include file='import_users_errors.tpl'}
<p class="message">{$imported_users_count} [[users were successfully imported]]</p>