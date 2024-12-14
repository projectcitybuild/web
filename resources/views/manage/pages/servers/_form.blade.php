<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $server->name) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">IP address</label>
    <div class="col-sm-9">
        <input type="text" id="ip" name="ip" class="form-control" placeholder="192.168.0.1"
               value="{{ old('ip', $server->ip) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="ip_alias" class="col-sm-3 col-form-label horizontal-label">IP alias (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="ip_alias" name="ip_alias" class="form-control" placeholder="pcbmc.co"
               value="{{ old('ip_alias', $server->ip_alias) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="port" class="col-sm-3 col-form-label horizontal-label">Port</label>
    <div class="col-sm-9">
        <input type="text" id="port" name="port" class="form-control" placeholder="25565"
               value="{{ old('port', $server->port) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="port" class="col-sm-3 col-form-label horizontal-label">Web port (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="web_port" name="web_port" class="form-control" placeholder="8080"
               value="{{ old('web_port', $server->web_port) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
