<div class="row mb-3">
    <label for="amount" class="col-sm-3 col-form-label horizontal-label">Amount</label>
    <div class="col-sm-9">
        <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount', $donation->amount) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="account_id" class="col-sm-3 col-form-label horizontal-label">Donator</label>
    <div class="col-sm-9">
        <x-panel.account-picker :account="$donation->account" />
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label horizontal-label">Date</label>
    <div class="col-sm-9">
        <input type="text" id="created_at" name="created_at" class="form-control" value="{{ old('created_at', $donation->created_at ?? now()) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
