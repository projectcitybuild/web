<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Server;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes server list and edit pages through the server DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVERS_VIEW->value,
        WebManagePermission::SERVERS_EDIT->value,
    ]);
    $server = Server::factory()->create([
        'name' => 'Contract Server',
        'ip_alias' => 'play.example.test',
        'web_port' => null,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.servers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Servers/ServerList')
            ->where('servers.0.id', $server->id)
            ->where('servers.0.ip_alias', 'play.example.test')
            ->where('servers.0.web_port', null)
        );

    $this->get(route('manage.servers.edit', $server))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Servers/ServerEdit')
            ->where('server.id', $server->id)
            ->where('server.name', 'Contract Server')
        );
});
