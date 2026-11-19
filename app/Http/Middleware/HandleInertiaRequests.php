<?php

namespace App\Http\Middleware;

use App\Http\Data\AdminAccountSummaryData;
use App\Http\Data\AdminSharedPagePropsData;
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
        $versionProps = [
            'appVersion' => config('app.version'),
            'commitSha' => config('app.commit'),
            'environment' => config('sentry.environment') ?? config('app.env'),
        ];

        $isAdminArea = $request->is(patterns: [
            'manage*',
            'review*',
            'audit*',
        ]);
        if ($isAdminArea) {
            $sharedProps = [
                ...parent::share($request),
                ...$versionProps,
            ];

            $sharedProps['success'] = fn () => AdminSharedPagePropsData::serializeProp(
                'success',
                $request->session()->get('success'),
            );
            $sharedProps['pending_appeals'] = fn () => AdminSharedPagePropsData::serializeProp(
                'pending_appeals',
                BanAppeal::pending()->count(),
            );
            $sharedProps['pending_build_rank_apps'] = fn () => AdminSharedPagePropsData::serializeProp(
                'pending_build_rank_apps',
                BuilderRankApplication::pending()->count(),
            );

            $account = $request->user();
            $sharedProps['account'] = fn () => AdminSharedPagePropsData::serializeProp(
                'account',
                $account instanceof Account
                    ? new AdminAccountSummaryData(
                        name: $account->username,
                        avatarUuid: $account->minecraftAccount()->value('uuid'),
                    )
                    : null,
            );

            return $sharedProps;
        }

        return $versionProps;
    }
}
