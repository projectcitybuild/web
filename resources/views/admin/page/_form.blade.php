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
        >{{ old('description', $page?->description) }}</textarea>
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
    <div class="markdown-preview">
        <div class="input">
            <textarea
                name="contents"
                id="contents"
                class="form-control"
                placeholder="Put content here..."
                rows="20"
                oninput="parseMarkdown()"
            >{{ old('contents', $page?->contents) }}</textarea>
        </div>
        <div class="preview">
            <div id="markdown"></div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-9 justify-content-start">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>

<script>
    const markdown = document.getElementById('markdown')
    const contents = document.getElementById('contents')

    function parseMarkdown() {
        markdown.innerHTML = marked.parse(
            DOMPurify.sanitize(contents.value)
        )
    }

    document.addEventListener('DOMContentLoaded', parseMarkdown)
</script>
