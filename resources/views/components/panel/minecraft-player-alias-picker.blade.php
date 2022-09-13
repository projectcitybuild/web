@props([
    'fieldName',
    'playerId',
    'aliasString',
])

<select
    name="{{ $fieldName }}"
    id="{{ $fieldName }}"
    data-pcb-player-picker
    @isset($playerId)
        data-player-id="{{ $playerId }}"
    @endisset
    @isset($aliasString)
        data-alias-string="{{ $aliasString }}"
    @endisset
>
</select>
