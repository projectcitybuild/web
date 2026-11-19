<?php

namespace App\Http\Controllers\Admin\Auditing;

use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\Admin\SortsAdminLists;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminPlayerOpElevationData;
use App\Models\PlayerOpElevation;
use Illuminate\Http\Request;
use Inertia\Response;

class PlayerOpElevationController extends WebController
{
    use RendersAdminApp;
    use SortsAdminLists;

    public function index(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $sortableColumns = [
            'reason' => 'reason',
            'startedAt' => 'started_at',
            'endedAt' => 'ended_at',
        ];
        $sortState = $this->adminListSortState($request, $sortableColumns);

        $query = PlayerOpElevation::query()->with('player:id,alias');
        $this->applyAdminListSort($query, $sortableColumns, $sortState, 'started_at');
        $elevations = $query->paginate(100, pageName: 'elevation_page')
            ->withQueryString()
            ->through(fn (PlayerOpElevation $elevation): array => AdminPlayerOpElevationData::fromModel($elevation)->toArray());

        return $this->inertiaRender('OpElevations/OpElevationList', [
            'elevations' => $elevations,
            ...$sortState,
        ]);
    }
}
