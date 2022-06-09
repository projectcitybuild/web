<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\Activity;

class ActivityController extends WebController
{
    public function index()
    {
        $activities = Activity::latest()
            ->with('subject', 'causer')
            ->paginate(100);

        return view('admin.activity.index')->with([
            'activities' => $activities
        ]);
    }

    public function show(Activity $activity)
    {
        return view('admin.activity.show')->with([
            'activity' => $activity
        ]);
    }
}
