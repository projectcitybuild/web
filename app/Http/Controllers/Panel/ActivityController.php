<?php

namespace App\Http\Controllers\Panel;

use App\Http\Concerns\FiltersWithParameters;
use App\Http\Controllers\WebController;
use App\Models\Activity;

class ActivityController extends WebController
{
    use FiltersWithParameters;

    protected array $filterParams = ['subject_type', 'description', 'causer_type', 'causer_id'];

    public function index()
    {
        $activities = Activity::latest()
            ->with(['subject', 'causer'])
            ->where($this->activeFiltersQuery())
            ->paginate(100);

        $allActions = Activity::distinctActions()->get();

        return view('admin.activity.index')->with([
            'activities' => $activities,
            'allActions' => $allActions,
            'activeFilters' => $this->activeFilters(),
        ]);
    }

    public function show(Activity $activity)
    {
        return view('admin.activity.show')->with([
            'activity' => $activity,
        ]);
    }
}
