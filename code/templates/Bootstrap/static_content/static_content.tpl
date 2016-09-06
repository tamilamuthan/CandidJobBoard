<h1 class="title__primary title__primary-small title__centered title__bordered">{$TITLE|escape}</h1>
{if $staticContent}
    <div class="static-pages content-text">
        {eval var=$staticContent}
    </div>
{/if}

{javascript}
    <script>
        $(document).ready(function() {
            $('table').each(function() {
                $(this).wrap('<div class="table-responsive"/>')
            });
        });
    </script>
{/javascript}
