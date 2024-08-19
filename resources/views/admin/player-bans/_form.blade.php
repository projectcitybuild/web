<div class="row mb-3">
    <label for="banner_player_id" class="col-sm-3 col-form-label horizontal-label">Banned Player</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
                fieldName="banned_player_id"
                :playerId="$ban?->bannedPlayer?->getKey()"
                :aliasString="$ban?->bannedPlayer?->currentAlias()?->alias"
        />
    </div>
</div>
<div class="row mb-3">
    <label for="banner_player_id" class="col-sm-3 col-form-label horizontal-label">Banned Player's Name</label>
    <div class="col-sm-9">
        <input
                type="text"
                id="banned_alias_at_time"
                name="banned_alias_at_time"
                class="form-control"
                placeholder="Notch"
                value="{{ old('banned_alias_at_time', $ban->banned_alias_at_time) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <label for="banner_player_id" class="col-sm-3 col-form-label horizontal-label">Banned By</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
                fieldName="banner_player_id"
                :playerId="$ban?->bannerPlayer?->getKey()"
                :aliasString="$ban?->bannerPlayer?->currentAlias()?->alias"
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
                placeholder="Destroyed Notch's town and set fire to spawn"
                value="{{ old('reason', $ban->reason) }}"
        >
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Expires At</label>
    <div class="col-sm-9">
        <input
                type="text"
                id="expires_at"
                name="expires_at"
                class="form-control"
                value="{{ old('expires_at', $ban->expires_at) }}"
        >
        <div class="form-text">Leave blank to make this a permanent ban</div>
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
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Unbanned At</label>
    <div class="col-sm-9">
        <input
                type="text"
                id="unbanned_at"
                name="unbanned_at"
                class="form-control"
                value="{{ old('unbanned_at', $ban->unbanned_at) }}"
        >
        <div class="form-text">Leave blank if this ban should be active</div>
    </div>
</div>
<div class="row mb-3">
    <label for="banner_player_id" class="col-sm-3 col-form-label horizontal-label">Unbanned By</label>
    <div class="col-sm-9">
        <x-panel.minecraft-player-alias-picker
                fieldName="unbanner_player_id"
                :playerId="$ban?->unbannerPlayer?->getKey()"
                :aliasString="$ban?->unbannerPlayer?->currentAlias()?->alias"
        />
        <div class="form-text">Leave blank if this ban should be active</div>
    </div>
</div>
<div class="row mb-3">
    <label for="game_type" class="col-sm-3 col-form-label horizontal-label">Unban Type</label>
    <div class="col-sm-9">
        <select name="unban_type">
            <option
                    value=""
                    {{ old('unban_type') === null || $ban->unban_type === null ? 'selected' : '' }}
            ></option>
            @foreach (\App\Domains\Bans\Data\UnbanType::cases() as $unbanType)
                <option
                        value="{{ $unbanType->value }}"
                        {{ old('unban_type') === $unbanType || $ban->unban_type === $unbanType ? 'selected' : '' }}
                >
                    {{ $unbanType->value }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Leave blank if this ban should be active</div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
