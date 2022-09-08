<div class="row mb-3">
    <label for="activity_type" class="col-sm-1 col-form-label horizontal-label">Filter</label>
    <div class="col-sm-4">
        <select name="activity_type" id="activity_type" data-pcb-navigation-select data-pcb-search-select>
            <option value="{{ route('front.panel.activity.index') }}" placeholder>all</option>
            @foreach($allActions as $action)
                <option
                    value="{{ route('front.panel.activity.index', array_merge($activeFilters, $action->only('subject_type', 'description'))) }}"
                    @selected(empty(array_diff_assoc($action->only('subject_type', 'description'), $activeFilters)))
                >
                    {{ $action->human_action }}
                </option>
            @endforeach
        </select>
    </div>
</div>
