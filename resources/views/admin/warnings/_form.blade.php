<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Player</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
            fieldName="warned_player_id"
            :playerId="$warning?->warnedPlayer?->getKey()"
            :aliasString="$warning?->warnedPlayer?->currentAlias()?->alias"
        />
    </div>
</div>
<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Warned By</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
            fieldName="warner_player_id"
            :playerId="$warning?->warnerPlayer?->getKey()"
            :aliasString="$warning?->warnerPlayer?->currentAlias()?->alias"
        />
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Reason</label>
    <div class="col-sm-9">
        <textarea
            id="reason"
            name="reason"
            placeholder="Put just a description of the offense here. The player will see it and need to press a button to acknowledge it"
            class="form-control"
        >{{ old('reason', $warning->reason) }}</textarea>
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Created At</label>
    <div class="col-sm-9">
        <input
            type="text"
            id="created_at"
            name="created_at"
            class="form-control"
            value="{{ old('created_at', $warning->created_at ?? now()) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Updated At</label>
    <div class="col-sm-9">
        <input
            type="text"
            id="updated_at"
            name="updated_at"
            class="form-control"
            value="{{ old('updated_at', $perk->updated_at ?? now()) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <label for="is_acknowledged" class="col-sm-3 col-form-label horizontal-label"></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                value="1"
                id="is_acknowledged"
                name="is_acknowledged"
                {{ $warning->is_acknowledged ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_acknowledged">Has been acknowledged by the user?</label>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
