<?php

namespace App\View\Components;

use App\Domains\BuilderRankApplications\Entities\ApplicationStatus;
use App\Models\BanAppeal;
use App\Models\BuilderRankApplication;
use Illuminate\View\Component;

class PanelSideBarComponent extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $outgoingRankApplications = BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)->count();
        $outstandingBanAppeals = BanAppeal::pending()->count();

        return view('admin.layouts._sidebar', [
            'outgoing_rank_apps' => $outgoingRankApplications,
            'outstanding_ban_appeals' => $outstandingBanAppeals,
        ]);
    }
}
