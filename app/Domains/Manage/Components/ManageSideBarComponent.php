<?php

namespace App\Domains\Manage\Components;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Models\BanAppeal;
use App\Models\BuilderRankApplication;
use Illuminate\View\Component;

class ManageSideBarComponent extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $outgoingRankApplications = BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)->count();
        $outstandingBanAppeals = BanAppeal::pending()->count();

        return view('manage.layouts._sidebar', [
            'outgoing_rank_apps' => $outgoingRankApplications,
            'outstanding_ban_appeals' => $outstandingBanAppeals,
        ]);
    }
}
