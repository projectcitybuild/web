<div class="row mb-3">
    <label for="donation_id" class="col-sm-3 col-form-label text-end">Donation ID</label>
    <div class="col-sm-9">
        <input type="text" id="donation_id" name="donation_id" class="form-control" value="{{ old('donation_id', $perk->donation_id ?? request()->query('donation_id')) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="account_id" class="col-sm-3 col-form-label text-end">Account ID</label>
    <div class="col-sm-9">
        <select name="account_id" id="account_id" data-pcb-user-picker>
            @isset($perk->account_id)
                <option value="{{ $perk->account_id }}" selected data-data='@json($perk->account->toResource())'></option>
            @endisset
        </select>
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label text-end">Start Date</label>
    <div class="col-sm-9">
        <input type="text" id="created_at" name="created_at" class="form-control" value="{{ old('created_at', $perk->created_at ?? now()) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label text-end">Expiry Date</label>
    <div class="col-sm-9">
        <input type="text" id="created_at" name="created_at" aria-describedby="created_at_help" class="form-control" value="{{ old('expires_at', $perk->expires_at ?? now()->addMonth()) }}">
        <div id="created_at_help" class="form-text">Required unless lifetime.</div>
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
        <div class="form-check">
            <input class="form-check-input" name="is_lifetime_perks" id="is_lifetime_perks" type="checkbox" value="1" {{ old('is_lifetime_perks', $perk->is_lifetime_perks ?? false) ? ' checked' : '' }}>
            <label class="form-check-label" for="is_lifetime_perks">
                Lifetime
            </label>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
