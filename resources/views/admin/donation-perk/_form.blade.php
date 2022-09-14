<div class="row mb-3">
    <label for="donation_id" class="col-sm-3 col-form-label horizontal-label">Donation ID</label>
    <div class="col-sm-9">
        <input type="text" id="donation_id" name="donation_id" class="form-control" value="{{ old('donation_id', $perk->donation_id ?? request()->query('donation_id')) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="account_id" class="col-sm-3 col-form-label horizontal-label">Account ID</label>
    <div class="col-sm-9">
        <x-panel.account-picker :account="$perk->account" />
    </div>
</div>
<div class="row mb-3">
    <label for="donation_id" class="col-sm-3 col-form-label horizontal-label">Donation Tier ID</label>
    <div class="col-sm-9">
        <input type="text" id="donation_tier_id" name="donation_tier_id" aria-describedby="donation_tier_id_help" class="form-control" value="{{ old('donation_tier_id', $perk->donation_tier_id ?? request()->query('donation_tier_id')) }}">
        <div id="donation_tier_id_help" class="form-text">Leave empty for legacy donations</div>
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Start Date</label>
    <div class="col-sm-9">
        <input type="text" id="created_at" name="created_at" class="form-control" value="{{ old('created_at', $perk->created_at ?? now()) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Expiry Date</label>
    <div class="col-sm-9">
        <input type="text" id="expires_at" name="expires_at" class="form-control" value="{{ old('expires_at', $perk->expires_at ?? now()->addMonth()) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <div class="form-check">
            <input class="form-check-input" name="is_active" id="is_active" type="checkbox" value="1" {{ old('is_active', $perk->is_active ?? true) ? ' checked' : '' }}>
            <label class="form-check-label" for="is_active">
                Active
            </label>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
