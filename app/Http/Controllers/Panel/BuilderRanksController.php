<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuilderRanksController extends WebController
{
    public function index(Request $request)
    {
        $applications = BuilderRankApplication::orderbyRaw("FIELD(status, ".ApplicationStatus::IN_PROGRESS->value.") DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.builder-rank.index')->with(compact('applications'));
    }

    public function show(int $applicationId)
    {
        $application = BuilderRankApplication::find($applicationId);

        return view('admin.builder-rank.show')->with(compact('application'));
    }

    public function approve(Request $request, int $applicationId)
    {

    }

    public function deny(Request $request, int $applicationId)
    {

    }
}
