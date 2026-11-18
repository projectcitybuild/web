<?php

use App\Http\Middleware\MfaAuthenticated;
use App\Http\Middleware\RequireMfaEnabled;
use App\Models\MinecraftConfig;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);
});

it('renders the remote config with its explicit page shape', function () {
    $account = $this->adminAccount();
    $config = MinecraftConfig::factory()->create([
        'version' => 7,
        'config' => ['messages' => ['welcome' => 'Welcome back']],
    ]);

    $this->withoutMiddleware([
        MfaAuthenticated::class,
        RequireMfaEnabled::class,
    ]);

    $this->actingAs($account)
        ->get(route('manage.minecraft.config.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('MinecraftConfig/MinecraftConfig')
            ->where('config', [
                'version' => $config->version,
                'config' => $config->config,
                'created_at' => $config->created_at->toISOString(),
                'updated_at' => $config->updated_at->toISOString(),
            ])
        );
});
