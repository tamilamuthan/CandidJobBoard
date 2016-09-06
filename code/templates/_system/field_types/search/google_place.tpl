<input type="text" name="{$id}[location][value]" id="{$id}" class="form-control" value="{$value.location.value}" placeholder="{tr}Location{/tr|escape}"/>
<input type="hidden" name="{$id}[location][radius]" value="{if $value.location.radius}{$value.location.radius}{else}50{/if}" id="radius" />
{javascript}
    <script>
        function initService() {
            var input = /** @type {!HTMLInputElement} */(document.getElementById('{$id}'));
            var options = {
                componentRestrictions: {
                    {if $GLOBALS.settings.location_limit}
                    country: '{$GLOBALS.settings.location_limit}'
                    {/if}
                }
            };
            new google.maps.places.Autocomplete(input, options);
        }
    </script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$GLOBALS.settings.google_api_key}&signed_in=true&libraries=places&callback=initService&language={$GLOBALS.current_language}" async defer></script>
{/javascript}