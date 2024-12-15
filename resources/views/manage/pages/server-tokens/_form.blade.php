<div class="row mb-3">
    <label for="token" class="col-sm-3 col-form-label horizontal-label">Token</label>
    <div class="col-sm-9">
        <input type="text" id="token" name="token" class="form-control" value="{{ old('token', $serverToken->token ?: $generatedToken) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="server_id" class="col-sm-3 col-form-label horizontal-label">Server ID</label>
    <div class="col-sm-9">
        <input type="text" id="server_id" name="server_id" class="form-control" value="{{ old('server_id', $serverToken->server_id) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="description" class="col-sm-3 col-form-label horizontal-label">Description</label>
    <div class="col-sm-9">
        <input type="text" id="description" name="description" class="form-control" value="{{ old('description', $serverToken->description) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
