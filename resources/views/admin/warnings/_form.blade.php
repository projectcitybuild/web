<div class="row mb-3">
    <label for="warned_player_id" class="col-sm-3 col-form-label horizontal-label">Player</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
            fieldName="warned_player_id"
            :playerId="$warning?->warnedPlayer?->getKey()"
            :aliasString="$warning?->warnedPlayer?->currentAlias()?->alias"
        />
    </div>
</div>
<div class="row mb-3">
    <label for="warner_player_id" class="col-sm-3 col-form-label horizontal-label">Warned By</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
            fieldName="warner_player_id"
            :playerId="$warning?->warnerPlayer?->getKey()"
            :aliasString="$warning?->warnerPlayer?->currentAlias()?->alias"
        />
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Reason for Warning</label>
    <div class="col-sm-9">
        <textarea
            id="reason"
            name="reason"
            placeholder="Put just a description of the offense here. The player will see it and need to press a button to acknowledge it"
            class="form-control"
        >{{ old('reason', $warning->reason) }}</textarea>

        <div class="form-text">This field will be shown to the user. They will have to read it and press a button to acknowledge it</div>
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Additional Info</label>
    <div class="col-sm-9">
        <textarea
            id="additional_info"
            name="additional_info"
            placeholder="Put any detailed, extra information, context, etc about the offense here"
            class="form-control"
            rows="6"
        >{{ old('additional_info', $warning->additional_info) }}</textarea>

        <div class="form-text">This field is not shown to the user. It's for recording purposes</div>
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
            value="{{ old('updated_at', $warning->updated_at ?? now()) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Acknowledged At</label>
    <div class="col-sm-9">
        <input
            type="text"
            id="acknowledged_at"
            name="acknowledged_at"
            class="form-control"
            value="{{ old('acknowledged_at', $warning->acknowledged_at) }}"
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
                {{ ($warning->is_acknowledged || old('is_acknowledged')) ? 'checked' : '' }}
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
