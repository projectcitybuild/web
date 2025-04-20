<?php

namespace App\Http\Middleware;

use App\Models\BanAppeal;
use App\Models\BuilderRankApplication;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    public function rootView(Request $request)
    {
        // We define this inside Controllers instead due to supporting
        // multiple Inertia apps
        return parent::rootView($request);
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if ($request->is(patterns: 'manage*')) {
            return [
                ...parent::share($request),
                'success' => fn () => $request->session()->get('success'),
            ];
        }
        if ($request->is(patterns: 'review*')) {
            $pendingAppeals = BanAppeal::pending()->count();
            $pendingBuildRankApps = BuilderRankApplication::pending()->count();

            return [
                ...parent::share($request),
                'success' => fn () => $request->session()->get('success'),
                'pending_appeals' => fn () => $pendingAppeals,
                'pending_build_rank_apps' => fn () => $pendingBuildRankApps,
            ];
        }
        return [];
    }
}
