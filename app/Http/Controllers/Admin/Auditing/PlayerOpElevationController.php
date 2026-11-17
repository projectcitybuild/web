<?php

namespace App\Http\Controllers\Admin\Auditing;

use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Models\PlayerOpElevation;
use Illuminate\Http\Request;
use Inertia\Response;

class PlayerOpElevationController extends WebController
{
    use RendersAdminApp;

    public function index(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $elevations = PlayerOpElevation::query()
            ->with('player:id,alias')
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->paginate(100, pageName: 'elevation_page')
            ->withQueryString()
            ->through(fn (PlayerOpElevation $elevation): array => [
                'id' => $elevation->id,
                'player' => $elevation->player,
                'reason' => $elevation->reason,
                'startedAt' => $elevation->started_at->toIso8601String(),
                'endedAt' => $elevation->ended_at->toIso8601String(),
                'isActive' => $elevation->ended_at->isFuture(),
            ]);

        return $this->inertiaRender('OpElevations/OpElevationList', [
            'elevations' => $elevations,
        ]);
    }
}
