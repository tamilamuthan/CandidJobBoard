<input id="{$id}" type="text" value="{$value}" class="form-control " name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{elseif $parentID}{$parentID}[{$id}]{else}{$id}{/if}" />
