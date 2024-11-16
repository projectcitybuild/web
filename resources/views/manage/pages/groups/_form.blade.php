<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $group->name) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Alias (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="alias" name="alias" class="form-control" value="{{ old('alias', $group->alias) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Minecraft Name (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="minecraft_name" name="minecraft_name" class="form-control" value="{{ old('minecraft_name', $group->minecraft_name) }}">
        Name of the group in the Minecraft server's permission system (eg. LuckPerms)
    </div>
</div>
<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Minecraft Display Name (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="minecraft_display_name" name="minecraft_display_name" class="form-control" value="{{ old('minecraft_display_name', $group->minecraft_display_name) }}">
        The group will be displayed as this in server chat. Supports MiniMessage format
    </div>
</div>
<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Minecraft Hover Text (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="minecraft_hover_text" name="minecraft_hover_text" class="form-control" value="{{ old('minecraft_hover_text', $group->minecraft_hover_text) }}">
        Text displayed when the user hovers over the group name in chat. Supports MiniMessage format
    </div>
</div>
<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Group Type (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="group_type" name="group_type" class="form-control" value="{{ old('group_type', $group->group_type) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="display_order" class="col-sm-3 col-form-label horizontal-label">Display priority (optional)</label>
    <div class="col-sm-9">
        <input type="text" id="display_priority" name="display_priority" class="form-control" placeholder="1" value="{{ old('display_priority', $group->display_priority) }}">
        Used to determine which group to display when a user belongs to multiple groups of the same type. Their highest is shown.
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
