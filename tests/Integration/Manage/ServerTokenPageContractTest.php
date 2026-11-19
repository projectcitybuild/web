<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\ServerToken;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes server token list and edit pages through the server token DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_VIEW->value,
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);
    $token = ServerToken::factory()->create([
        'token' => 'contract-token',
        'description' => 'Contract credential',
        'allowed_ips' => '192.0.2.1',
    ]);

    $this->actingAs($manager)
        ->get(route('manage.server-tokens.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ServerTokens/ServerTokensList')
            ->where('tokens.0.id', $token->id)
            ->where('tokens.0.token', 'contract-token')
            ->where('tokens.0.allowed_ips', '192.0.2.1')
        );

    $this->get(route('manage.server-tokens.edit', $token))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ServerTokens/ServerTokenEdit')
            ->where('token.id', $token->id)
            ->where('token.description', 'Contract credential')
        );
});
