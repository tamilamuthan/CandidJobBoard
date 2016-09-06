<input type="text" name="{$id}[location][value]" id="{$id}" class="form-control form-control__google-location" value="{$value.location.value}" placeholder="{tr}Location{/tr|escape}"/>
<input type="hidden" name="{$id}[location][radius]" value="{if $value.location.radius}{$value.location.radius}{else}50{/if}" id="radius" class="hidden-radius"/>
{javascript}
    <script>
        function initService() {
            var input = /** @type {!HTMLInputElement} */($('.form-control__google-location'));
            var options = {
                componentRestrictions: {
                    {if $GLOBALS.settings.location_limit}
                        country: '{$GLOBALS.settings.location_limit}'
                    {/if}
                }
            };

            for(var i=0; i<input.length; i++){
                new google.maps.places.Autocomplete(input[i], options);
            }

        }

        $('#ajax-refine-search').on('click', '.refine-search__item-radius', function(e) {
            e.preventDefault();
            var radiusValue = $(this).data('value');

            $('.hidden-radius').each(function() {
                $(this).val(radiusValue);
            });

            $('#refine-block-radius .dropdown-toggle').text($(this).data('value') + ' [[{$GLOBALS.settings.radius_search_unit}]]');
            $('.quick-search__wrapper').find('form').submit();
        });

        $('#{$id}').keydown(function (e) {
            if (e.which == 13 && $('.pac-container:visible').length) {
                return false;
            }
        });
    </script>
{/javascript}