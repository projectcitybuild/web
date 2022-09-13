<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Display Name</label>
    <div class="col-sm-9">
        <select
            name="account_id"
            id="account_id"
            data-pcb-user-picker
            @isset($warning->warned_player_id)
                data-account-id="{{ $warning->warnedPlayerId->getKey() }}"
{{--                data-account-username="{{ $minecraftPlayer->account->username }}"--}}
{{--                data-account-email="{{ $minecraftPlayer->account->email }}"--}}
            @endisset
        >
        </select>
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Reason</label>
    <div class="col-sm-9">
        <input type="text" id="reason" name="reason" class="form-control" value="{{ old('reason', $warning->reason) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
