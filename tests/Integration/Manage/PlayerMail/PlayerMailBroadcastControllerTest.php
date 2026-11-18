<?php

use App\Domains\Permissions\WebManagePermission;
use App\Domains\PlayerMail\Data\MailCategory;
use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Domains\PlayerMail\Jobs\DistributePlayerMailJob;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\Permission;
use App\Models\PlayerMail;
use App\Models\PlayerMailBroadcast;
use App\Models\Role;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
});

it('queues an authorized staff announcement for the captured player audience', function () {
    $account = $this->createStaffAccountWithPermissions([
        'web.manage.*',
        WebManagePermission::PLAYER_MAIL_SEND->value,
    ]);
    $players = MinecraftPlayer::factory()->count(2)->create();
    Queue::fake([DistributePlayerMailJob::class]);

    $response = $this->actingAs($account)
        ->postJson(route('manage.player-mail.broadcasts.store'), [
            'subject' => 'Server maintenance',
            'body' => 'The server will be offline Saturday.',
        ]);

    $response
        ->assertAccepted()
        ->assertJsonPath('status', PlayerMailBroadcastStatus::QUEUED->value)
        ->assertJsonPath('recipients_count', 0);

    $mail = PlayerMail::query()->firstOrFail();
    $broadcast = PlayerMailBroadcast::query()->firstOrFail();

    $this->assertDatabaseHas('player_mails', [
        'id' => $mail->id,
        'category' => MailCategory::ANNOUNCEMENT->value,
        'sender_player_id' => null,
        'sender_name' => 'Project City Build',
        'created_by_account_id' => $account->id,
    ]);
    expect($broadcast->max_player_id)->toBe($players->last()->id);
    expect($broadcast->mail_id)->toBe($mail->id);
    Queue::assertPushed(DistributePlayerMailJob::class, fn (DistributePlayerMailJob $job): bool => $job->broadcastId === $broadcast->id && $job->expectedLastPlayerId === 0
    );
});

it('forbids staff without the player mail permission from broadcasting', function () {
    $account = Account::factory()
        ->hasFinishedTotp()
        ->create();
    $role = Role::factory()->staff()->create();
    $account->roles()->attach($role->id);
    $role->permissions()->attach(Permission::create(['name' => 'web.manage.*'])->id);
    Queue::fake([DistributePlayerMailJob::class]);

    $this->actingAs($account)
        ->postJson(route('manage.player-mail.broadcasts.store'), [
            'subject' => 'Unauthorized',
            'body' => 'This should not be sent.',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('player_mails', 0);
    $this->assertDatabaseCount('player_mail_broadcasts', 0);
    Queue::assertNotPushed(DistributePlayerMailJob::class);
});

it('renders authorized player mail pages with the expected data', function () {
    $account = $this->createStaffAccountWithPermissions([
        'web.manage.*',
        WebManagePermission::PLAYER_MAIL_SEND->value,
    ]);
    $players = MinecraftPlayer::factory()->count(2)->create();
    $mail = PlayerMail::factory()->create([
        'sender_player_id' => null,
        'sender_name' => 'Project City Build',
        'created_by_account_id' => $account->id,
        'category' => MailCategory::ANNOUNCEMENT,
        'subject' => 'Server notice',
        'body' => 'Maintenance begins Saturday.',
    ]);
    $broadcast = PlayerMailBroadcast::factory()->create([
        'mail_id' => $mail->id,
        'max_player_id' => $players->last()->id,
        'recipients_count' => 1,
    ]);

    $indexResponse = $this->actingAs($account)
        ->get(route('manage.player-mail.index', ['search' => 'notice', 'status' => 'queued']));

    $indexResponse->assertSee('<script data-page="app" type="application/json">', false);
    $indexResponse->assertInertia(fn (Assert $page) => $page
        ->component('PlayerMail/Index')
        ->where('filters.search', 'notice')
        ->where('filters.status', 'queued')
        ->where('broadcasts.data.0', [
            'id' => $broadcast->id,
            'subject' => 'Server notice',
            'sender' => $account->username,
            'recipients_count' => 1,
            'status' => PlayerMailBroadcastStatus::QUEUED->value,
            'created_at' => $broadcast->created_at->toISOString(),
        ])
    );

    $this->get(route('manage.player-mail.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('PlayerMail/Create')
            ->where('recipientCount', 2)
            ->where('sender', $account->username)
        );

    $this->get(route('manage.player-mail.show', [
        'broadcast' => $broadcast,
        'search' => 'notice',
        'status' => 'queued',
        'page' => 3,
    ]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('PlayerMail/Show')
            ->where('broadcast', [
                'id' => $broadcast->id,
                'subject' => 'Server notice',
                'body' => 'Maintenance begins Saturday.',
                'sender' => $account->username,
                'status' => PlayerMailBroadcastStatus::QUEUED->value,
                'recipients_count' => 1,
                'created_at' => $broadcast->created_at->toISOString(),
                'started_at' => null,
                'completed_at' => null,
            ])
            ->where('audienceEstimate', 2)
            ->where('backUrl', route('manage.player-mail.index', [
                'search' => 'notice',
                'status' => 'queued',
                'page' => 3,
            ]))
        );
});

it('submits a confirmed manage form through the existing broadcast use case', function () {
    $account = $this->createStaffAccountWithPermissions([
        'web.manage.*',
        WebManagePermission::PLAYER_MAIL_SEND->value,
    ]);
    Queue::fake([DistributePlayerMailJob::class]);

    $response = $this->actingAs($account)
        ->post(route('manage.player-mail.store'), [
            'subject' => 'Server maintenance',
            'body' => 'The server will be offline Saturday.',
        ]);

    $broadcast = PlayerMailBroadcast::query()->firstOrFail();
    $response->assertRedirect(route('manage.player-mail.show', $broadcast));
    $this->assertDatabaseHas('player_mails', [
        'id' => $broadcast->mail_id,
        'subject' => 'Server maintenance',
        'created_by_account_id' => $account->id,
    ]);
    Queue::assertPushed(DistributePlayerMailJob::class);
});

it('forbids staff without the player mail permission from viewing or submitting manage pages', function () {
    $account = Account::factory()
        ->hasFinishedTotp()
        ->create();
    $role = Role::factory()->staff()->create();
    $account->roles()->attach($role->id);
    $role->permissions()->attach(Permission::create(['name' => 'web.manage.*'])->id);

    $this->actingAs($account)
        ->get(route('manage.player-mail.index'))
        ->assertForbidden();

    $this->get(route('manage.player-mail.create'))
        ->assertForbidden();

    $this->post(route('manage.player-mail.store'), [
        'subject' => 'Unauthorized',
        'body' => 'Must not be queued.',
    ])->assertForbidden();

    $this->assertDatabaseCount('player_mails', 0);
    $this->assertDatabaseCount('player_mail_broadcasts', 0);
});

it('forbids staff without the player mail permission from viewing broadcast details', function () {
    $account = Account::factory()
        ->hasFinishedTotp()
        ->create();
    $role = Role::factory()->staff()->create();
    $account->roles()->attach($role->id);
    $role->permissions()->attach(Permission::create(['name' => 'web.manage.*'])->id);
    $broadcast = PlayerMailBroadcast::factory()->create();

    $this->actingAs($account)
        ->get(route('manage.player-mail.show', $broadcast))
        ->assertForbidden();
});

it('rejects invalid announcement content without creating a broadcast', function () {
    $account = $this->createStaffAccountWithPermissions([
        'web.manage.*',
        WebManagePermission::PLAYER_MAIL_SEND->value,
    ]);
    Queue::fake([DistributePlayerMailJob::class]);

    $this->actingAs($account)
        ->post(route('manage.player-mail.store'), [
            'subject' => str_repeat('s', 192),
            'body' => str_repeat('m', 5001),
        ])
        ->assertSessionHasErrors(['subject', 'body']);

    $this->assertDatabaseCount('player_mails', 0);
    $this->assertDatabaseCount('player_mail_broadcasts', 0);
    Queue::assertNotPushed(DistributePlayerMailJob::class);
});
