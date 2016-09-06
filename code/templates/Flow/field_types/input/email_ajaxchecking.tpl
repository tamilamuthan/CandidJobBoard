<div class="form-group">
    <span id="am_{$id}" class="form-message form-message__error"></span>
    <input type="email" value="{$value|escape}" class="form-control {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][original]{else}{$id}[original]{/if}" onblur="checkField($(this), '{$id}')"/>
</div>
