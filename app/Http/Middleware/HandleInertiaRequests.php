<?php

namespace App\Http\Middleware;

use App\Models\Account;
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
        $isAdminArea = $request->is(patterns: 'manage*')
            || $request->is(patterns: 'review*')
            || $request->is(patterns: 'auditing*');

        if ($isAdminArea) {
            $sharedProps = [
                ...parent::share($request),
            ];

            $sharedProps['success'] = fn () => $request->session()->get('success');
            $sharedProps['pending_appeals'] = fn () => BanAppeal::pending()->count();
            $sharedProps['pending_build_rank_apps'] = fn () => BuilderRankApplication::pending()->count();

            $account = $request->user();
            $sharedProps['account'] = fn () => $account instanceof Account
                ? [
                    'name' => $account->username,
                    'avatarUuid' => $account->minecraftAccount()->value('uuid'),
                ]
                : null;

            return $sharedProps;
        }

        return [];
    }
}
