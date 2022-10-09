<?php

namespace App\View\Components;

use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\View\Component;

class PanelSideBarComponent extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $outstandingRankApps = BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)->count();
        $outstandingShowcaseApps = ShowcaseApplication::where('status', ApplicationStatus::IN_PROGRESS->value)->count();
        $outstandingBanAppeals = BanAppeal::pending()->count();

        return view('admin.layouts._sidebar')
            ->with(compact(
                'outstandingBanAppeals',
                'outstandingRankApps',
                'outstandingShowcaseApps'
            ));
    }
}
