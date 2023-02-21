<div class="row mb-3">
    <label for="token" class="col-sm-3 col-form-label horizontal-label">Token</label>
    <div class="col-sm-9">
        <input type="text" id="token" name="token" class="form-control" value="{{ old('token', $token->token ?: $generatedToken) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="server_id" class="col-sm-3 col-form-label horizontal-label">Server ID</label>
    <div class="col-sm-9">
        <input type="text" id="server_id" name="server_id" class="form-control" value="{{ old('server_id', $token->server_id) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="description" class="col-sm-3 col-form-label horizontal-label">Description (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="description" name="description" class="form-control" value="{{ old('description', $token->description) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="description" class="col-sm-3 col-form-label horizontal-label">Scopes</label>
    <div class="col-sm-9">
        @foreach ($scopes as $scope)
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    value="{{ $scope->scope }}"
                    id="scope_{{ $scope->getKey() }}"
                    name="scopes[]"
                    {{ $token->scopes->contains($scope->getKey()) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="scope_{{ $scope->getKey() }}">
                    {{ $scope->scope }}
                </label>
            </div>
        @endforeach
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
