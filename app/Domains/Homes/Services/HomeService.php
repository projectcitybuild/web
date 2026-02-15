<?php

namespace App\Domains\Homes\Services;

use App\Core\Domains\MinecraftCoordinate\MinecraftCoordinate;
use App\Domains\Homes\Data\HomeCount;
use App\Domains\Homes\Exceptions\HomeAlreadyExistsException;
use App\Domains\Homes\Exceptions\HomeLimitReachedException;
use App\Domains\Roles\Services\PlayerRolesAggregator;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Auth\Access\AuthorizationException;

class HomeService
{
    public function __construct(
        // TODO: inject with interface to break coupling
        private readonly PlayerRolesAggregator $playerRolesAggregator,
    ) {}

    public function count(MinecraftPlayer $player): ?HomeCount
    {
        $account = $player->account;
        $roles = optional($account, fn ($it) => $this->playerRolesAggregator->get($it))
            ?? collect();

        $sources = $roles
            ->filter(fn ($role) => ($role->additional_homes ?? 0) > 0)
            ->pluck('additional_homes', 'name');

        return new HomeCount(
            used: MinecraftHome::where('player_id', $player->getKey())->count(),
            allowed: max(1, $sources->sum()), // Always grant at least 1 home
            sources: $sources->toArray(),
        );
    }

    public function create(
        MinecraftPlayer $player,
        MinecraftCoordinate $coordinate,
        string $name,
    ): MinecraftHome {
        $exists = MinecraftHome::where('name', $name)
            ->where('player_id', $player->getKey())
            ->exists();

        if ($exists) {
            throw new HomeAlreadyExistsException;
        }

        $count = $this->count($player);
        if ($count->atLimit()) {
            throw new HomeLimitReachedException('You cannot create more than '.$count->allowed.' homes');
        }

        return MinecraftHome::create([
            'name' => $name,
            'player_id' => $player->getKey(),
            ...$coordinate->toArray(),
        ]);
    }

    public function update(
        MinecraftHome $home,
        MinecraftPlayer $player,
        MinecraftCoordinate $coordinate,
        string $name,
    ): MinecraftHome {
        $this->assertCanAccess($home, $player);

        $exists = MinecraftHome::where('name', $name)
            ->where('player_id', $player->getKey())
            ->where('id', '!=', $home->getKey())
            ->exists();

        if ($exists) {
            throw new HomeAlreadyExistsException;
        }

        $home->update([
            'name' => $name,
            ...$coordinate->toArray(),
        ]);

        return $home;
    }

    public function delete(
        MinecraftHome $home,
        MinecraftPlayer $player,
    ) {
        $this->assertCanAccess($home, $player);

        $home->delete();
    }

    public function show(
        MinecraftHome $home,
        MinecraftPlayer $player,
    ): MinecraftHome {
        $this->assertCanAccess($home, $player);
        return $home;
    }

    private function assertCanAccess(MinecraftHome $home, MinecraftPlayer $player): void
    {
        $isOwner = $home->player_id === $player->getKey();
        $isStaff = $player->account?->isStaff() ?? false;

        throw_if(! $isOwner && ! $isStaff, AuthorizationException::class);
    }
}
