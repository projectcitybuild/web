<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Warp Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="name" class="form-control" placeholder="warp_name" value="{{ old('name', $warp->name) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="title" class="col-sm-3 col-form-label horizontal-label">Display Title (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="title" name="title" class="form-control" placeholder="Warp Name" value="{{ old('title', $warp->title) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="description" class="col-sm-3 col-form-label horizontal-label">Description (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="description" name="description" class="form-control" placeholder="A build of a very high quality" value="{{ old('description', $warp->description) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="creators" class="col-sm-3 col-form-label horizontal-label">Creators (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="creators" name="creators" class="form-control" placeholder="Herobrine" value="{{ old('creators', $warp->creators) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="location_world" class="col-sm-3 col-form-label horizontal-label">World</label>
    <div class="col-sm-9">
        <input type="text" id="location_world" name="location_world" class="form-control" placeholder="creative" value="{{ old('location_world', $warp->location_world) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="location_x" class="col-sm-3 col-form-label horizontal-label">X</label>
    <div class="col-sm-9">
        <input type="number" id="location_x" name="location_x" class="form-control" placeholder="0" value="{{ old('location_x', $warp->location_x) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="location_y" class="col-sm-3 col-form-label horizontal-label">Y</label>
    <div class="col-sm-9">
        <input type="number" id="location_y" name="location_y" class="form-control" placeholder="0" value="{{ old('location_y', $warp->location_y) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="location_z" class="col-sm-3 col-form-label horizontal-label">Z</label>
    <div class="col-sm-9">
        <input type="number" id="location_z" name="location_z" class="form-control" placeholder="0" value="{{ old('location_z', $warp->location_z) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="location_pitch" class="col-sm-3 col-form-label horizontal-label">Pitch</label>
    <div class="col-sm-9">
        <input type="text" id="location_pitch" name="location_pitch" class="form-control" placeholder="0.0" value="{{ old('location_pitch', $warp->location_pitch) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="location_yaw" class="col-sm-3 col-form-label horizontal-label">Yaw</label>
    <div class="col-sm-9">
        <input type="text" id="location_yaw" name="location_yaw" class="form-control" placeholder="0.0" value="{{ old('location_yaw', $warp->location_yaw) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="built_at" class="col-sm-3 col-form-label horizontal-label">Built at</label>
    <div class="col-sm-9">
        <input type="number" id="built_at" name="built_at" class="form-control" value="{{ old('built_at', $warp->built_at?->getTimestamp() ?: now()->getTimestamp()) }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
