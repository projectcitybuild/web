@props(['account'])

<select
    name="account_id"
    id="account_id"
    data-pcb-user-picker
    @isset($attributes->account)
        data-account-id="{{ $attributes->account->getKey() }}"
        data-account-username="{{ $attributes->account->username }}"
    @endisset
>
</select>
