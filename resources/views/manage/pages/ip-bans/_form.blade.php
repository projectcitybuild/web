<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">IP Address</label>
    <div class="col-sm-9">
        <input
            type="text"
            id="ip_address"
            name="ip_address"
            class="form-control"
            placeholder="192.168.0.1"
            value="{{ old('ip_address', $ban->ip_address) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <label for="banner_player_id" class="col-sm-3 col-form-label horizontal-label">Banned By</label>
    <div class="col-sm-9">
        <x-manage::minecraft-player-alias-picker
            fieldName="banner_player_id"
            :playerId="$ban?->bannerPlayer?->getKey()"
            :aliasString="$ban?->bannerPlayer?->alias"
        />
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Reason for Ban</label>
    <div class="col-sm-9">
        <input
            type="reason"
            id="reason"
            name="reason"
            class="form-control"
            placeholder="Ban evasion. Alternate account of Notch"
            value="{{ old('reason', $ban->reason) }}"
        >
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
            value="{{ old('created_at', $ban->created_at ?? now()) }}"
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
            value="{{ old('updated_at', $ban->updated_at ?? now()) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
