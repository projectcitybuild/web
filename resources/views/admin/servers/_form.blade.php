<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $server->name) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">IP address</label>
    <div class="col-sm-9">
        <input type="text" id="ip" name="ip" class="form-control" placeholder="192.168.0.1" value="{{ old('ip', $server->ip) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="port" class="col-sm-3 col-form-label horizontal-label">Port</label>
    <div class="col-sm-9">
        <input type="text" id="port" name="port" class="form-control" placeholder="25565" value="{{ old('port', $server->port) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="ip_alias" class="col-sm-3 col-form-label horizontal-label">IP alias (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="ip_alias" name="ip_alias" class="form-control" placeholder="pcbmc.co" value="{{ old('ip_alias', $server->ip_alias) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="display_order" class="col-sm-3 col-form-label horizontal-label">Display order</label>
    <div class="col-sm-9">
        <input type="text" id="display_order" name="display_order" class="form-control" placeholder="1" value="{{ old('display_order', $server->display_order) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="game_type" class="col-sm-3 col-form-label horizontal-label">Game type</label>
    <div class="col-sm-9">
        <select name="game_type">
            @foreach (\Entities\Models\GameType::cases() as $gameType)
                <option
                    value="{{ $gameType->value }}"
                    {{ $server->game_type === $gameType->value ? 'selected' : '' }}
                >
                    {{ $gameType->name() }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="row mb-3">
    <label for="game_type" class="col-sm-3 col-form-label horizontal-label">Options</label>
    <div class="col-sm-9">
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                value="1"
                id="is_visible"
                name="is_visible"
                {{ $server->is_visible ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_visible">Is visible (on homepage)</label>
        </div>
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                value="1"
                id="is_port_visible"
                name="is_port_visible"
                {{ $server->is_port_visible ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_port_visible">Show port (on homepage)</label>
        </div>
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                value="1"
                id="is_querying"
                name="is_querying"
                {{ $server->is_querying ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_querying">Continuously query for status</label>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
