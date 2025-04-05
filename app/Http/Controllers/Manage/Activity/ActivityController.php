<?php

namespace App\Http\Controllers\Manage\Activity;

use App\Core\Utilities\Traits\FiltersWithParameters;
use App\Http\Controllers\WebController;
use App\Models\Activity;
use Illuminate\Support\Facades\Gate;

class ActivityController extends WebController
{
    use FiltersWithParameters;

    protected array $filterParams = ['subject_type', 'description', 'causer_type', 'causer_id'];

    public function index()
    {
        Gate::authorize('viewAny', Activity::class);

        $activities = Activity::latest()
            ->with(['subject', 'causer'])
            ->where($this->activeFiltersQuery())
            ->paginate(100);

        $allActions = Activity::distinctActions()->get();

        return view('manage.pages.activity.index')->with([
            'activities' => $activities,
            'allActions' => $allActions,
            'activeFilters' => $this->activeFilters(),
        ]);
    }

    public function show(Activity $activity)
    {
        Gate::authorize('view', $activity);

        return view('manage.pages.activity.show')->with([
            'activity' => $activity,
        ]);
    }
}
