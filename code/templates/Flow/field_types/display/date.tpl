{if $format}
    {$value|date:$format}
{else}
    {$value|date}
{/if}