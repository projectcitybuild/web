<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Display Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="display_name" class="form-control" value="{{ old('display_name', $badge->display_name) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Unicode Icon</label>
    <div class="col-sm-9">
        <input type="text" id="ip" name="unicode_icon" class="form-control" value="{{ old('unicode_icon', $badge->unicode_icon) }}">
    </div>
</div>
<div class="row mb-3">
    <label for="ip" class="col-sm-3 col-form-label horizontal-label">Hidden in Badge List</label>
    <div class="col-sm-9">
        <input
            type="checkbox"
            name="list_hidden"
            class="
                rounded-sm
                bg-gray-300 border-0
                checked:bg-gray-900
            "
            value="1"
            @if(old('list_hidden', $badge->list_hidden) == 1) checked @endif
        >
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-9 ms-auto">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
