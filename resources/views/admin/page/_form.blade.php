<div class="row mb-3">
    <label for="title" class="col-sm-3 col-form-label horizontal-label">Title</label>
    <div class="col-sm-9">
        <input
            type="text"
            id="title"
            name="title"
            class="form-control"
            value="{{ old('title', $page->title) }}"
        >
    </div>
</div>

<div class="row mb-3">
    <label for="url" class="col-sm-3 col-form-label horizontal-label">URL</label>
    <div class="col-sm-9">
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon3">{{ config('app.url') }}/p/</span>
            <input
                type="text"
                id="url"
                name="url"
                class="form-control"
                value="{{ old('url', $page->url) }}"
            >
        </div>
    </div>
</div>

<div class="row mb-3">
    <label for="name" class="col-sm-3 col-form-label horizontal-label">Page description</label>
    <div class="col-sm-9">
        <textarea
            name="description"
            class="form-control"
            placeholder="A short description about the page shown to the user. This appears below the title..."
            rows="3"
        >@isset($page) {{ $page->description }} @endisset</textarea>
    </div>
</div>

<div class="row mb-3">
    <label for="is_draft" class="col-sm-3 col-form-label horizontal-label">Is draft?</label>
    <div class="col-sm-9">
        <input
            type="checkbox"
            id="is_draft"
            name="is_draft"
            value="1"
            @isset($page)
                {{ $page->is_draft ? 'checked' : '' }}
            @endisset
        />
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <textarea
            name="contents"
            class="form-control"
            placeholder="Put content here..."
            rows="20"
        >@isset($page) {{ $page->contents }} @endisset</textarea>
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
