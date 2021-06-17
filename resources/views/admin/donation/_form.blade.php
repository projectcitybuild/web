<div class="row mb-3">
    <label for="amount" class="col-sm-3 col-form-label text-end">Amount</label>
    <div class="col-sm-9">
        <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount', $donation->amount) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="account_id" class="col-sm-3 col-form-label text-end">Donator</label>
    <div class="col-sm-9">
        <input type="text" id="account_id" name="account_id" class="form-control" value="{{ old('account_id', $donation->account_id) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="created_at" class="col-sm-3 col-form-label text-end">Date</label>
    <div class="col-sm-9">
        <input type="text" id="created_at" name="created_at" class="form-control" value="{{ old('created_at', $donation->created_at ?? now()) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
