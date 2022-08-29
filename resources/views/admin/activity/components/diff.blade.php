<div class="card mb-2">
    <div class="card-header d-flex justify-content-between align-items-center pb-0 pe-0">
        <div>Updated <strong>{{ $attribute }}</strong></div>
        <ul class="nav nav-tabs border-bottom-0">
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#{{ $attribute }}-old">Old</button>
            </li>
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#{{ $attribute }}-diff">Diff</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#{{ $attribute }}-new">New</button>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div id="{{ $attribute }}-old" class="tab-pane p-1 font-monospace">
            {!! nl2br(e($old))  !!}
        </div>
        <div id="{{ $attribute }}-diff" class="tab-pane active font-monospace">
            {{ $diff }}
        </div>
        <div id="{{ $attribute }}-new" class="tab-pane p-1 font-monospace">
            {!! nl2br(e($new)) !!}
        </div>
    </div>
</div>
