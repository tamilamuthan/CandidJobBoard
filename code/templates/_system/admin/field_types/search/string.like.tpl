<input type="text" name="{$id}[like]" value="{if is_array($value)}{if $value.like}{$value.like}{elseif $value.equal}{$value.equal}{/if}{else}{$value}{/if}" />