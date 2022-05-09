<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Name</label>
    <div class="col-sm-9">
        <input type="text" id="name" name="name" class="form-control" min="0" value="{{ old('name', $page->name) }}">
    </div>
</div>

<div class="row mb-3">
    <label for="is_draft" class="col-sm-3 col-form-label horizontal-label">Is draft?</label>
    <div class="col-sm-9">
        <input type="checkbox" id="is_draft" name="is_draft" value="1" checked />
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <textarea
            name="contents"
            class="form-control"
            placeholder="Put content here..."
            rows="20"
        >
            @isset($page)
                {{ $page->contents }}
            @endisset
        </textarea>
    </div>
    <div class="col">
        <div class="card" style="width: 100%">
            <div class="card-body">
                Test
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-9 justify-content-start">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>
