<?php

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Data\AdminBuilderRankApplicationData;
use App\Models\Account;
use App\Models\BuilderRankApplication;
use App\Models\MinecraftPlayer;
use App\Models\Role;
use Carbon\CarbonInterface;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes builder rank review pages through application and role DTOs', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = $this->createStaffAccountWithPermissions([
        WebReviewPermission::BUILD_RANK_APPS_VIEW->value,
    ]);
    $account = Account::factory()->create(['username' => 'contract-builder']);
    $player = MinecraftPlayer::factory()->create([
        'alias' => 'ContractBuilder',
        'account_id' => $account->id,
    ]);
    $application = BuilderRankApplication::factory()->for($account)->create([
        'minecraft_alias' => 'ContractBuilder',
        'status' => ApplicationStatus::PENDING,
    ]);
    $buildRole = Role::factory()->architect()->create();

    $this->actingAs($reviewer)
        ->get(route('review.builder-ranks.show', $application))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BuilderRankApplications/BuilderRankApplicationShow')
            ->where('application.id', $application->id)
            ->where('application.status', ApplicationStatus::PENDING->value)
            ->where('application.account.minecraft_account.0.id', $player->id)
            ->where('buildRoles.0.id', $buildRole->id)
        );

    $version = $this->actingAs($reviewer)
        ->withHeader('X-Inertia', 'true')
        ->get('/review/builder-ranks')
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'BuilderRankApplications/BuilderRankApplicationList',
        'X-Inertia-Partial-Data' => 'applications',
    ])
        ->get('/review/builder-ranks')
        ->assertOk()
        ->assertJsonPath('props.applications.data.0.id', $application->id)
        ->assertJsonPath('props.applications.data.0.account.username', 'contract-builder')
        ->assertJsonPath('props.applications.data.0.account.minecraft_account.0.alias', 'ContractBuilder');
});

it('casts builder rank application closure dates before DTO construction', function () {
    $application = BuilderRankApplication::factory()
        ->for(Account::factory())
        ->approved()
        ->create()
        ->refresh();

    $data = AdminBuilderRankApplicationData::fromModel($application);

    expect($data->closed_at)->toBeInstanceOf(CarbonInterface::class);
});
