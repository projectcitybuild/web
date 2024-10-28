<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Warp Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="name" class="form-control" placeholder="warp_name" value="{{ old('name', $warp->name) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="world" class="col-sm-3 col-form-label horizontal-label">World</label>
    <div class="col-sm-9">
        <input type="text" id="world" name="world" class="form-control" placeholder="creative" value="{{ old('world', $warp->world) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="x" class="col-sm-3 col-form-label horizontal-label">X</label>
    <div class="col-sm-9">
        <input type="number" id="x" name="x" class="form-control" placeholder="0" value="{{ old('x', $warp->x) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="y" class="col-sm-3 col-form-label horizontal-label">Y</label>
    <div class="col-sm-9">
        <input type="number" id="y" name="y" class="form-control" placeholder="0" value="{{ old('y', $warp->y) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="z" class="col-sm-3 col-form-label horizontal-label">Z</label>
    <div class="col-sm-9">
        <input type="number" id="z" name="z" class="form-control" placeholder="0" value="{{ old('z', $warp->z) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="pitch" class="col-sm-3 col-form-label horizontal-label">Pitch</label>
    <div class="col-sm-9">
        <input type="text" id="pitch" name="pitch" class="form-control" placeholder="0.0" value="{{ old('pitch', $warp->pitch) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="yaw" class="col-sm-3 col-form-label horizontal-label">Yaw</label>
    <div class="col-sm-9">
        <input type="text" id="yaw" name="yaw" class="form-control" placeholder="0.0" value="{{ old('yaw', $warp->yaw) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
