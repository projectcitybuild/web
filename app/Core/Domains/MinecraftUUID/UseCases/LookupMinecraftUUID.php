<?php

namespace App\Core\Domains\MinecraftUUID\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUIDLookup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LookupMinecraftUUID
{
    public function fetch(MinecraftUUID $uuid): ?MinecraftUUIDLookup
    {
        $trimmedUuid = $uuid->trimmed();
        $secondsTtl = 60 * 5;

        return Cache::remember('uuid_'.$trimmedUuid, $secondsTtl, function () use ($trimmedUuid) {
            $response = Http::withHeaders(['user-agent' => config('app.url')])
                ->get('https://playerdb.co/api/player/minecraft/'.$trimmedUuid)
                ->json();

            Log::debug('Fetched Minecraft UUID details', ['response' => $response]);

            $lookup = MinecraftUUIDLookup::fromResponse($response);

            Log::debug('Parsed lookup response', [
                'username' => $lookup?->username,
                'uuid' => $lookup?->uuid,
            ]);

            return $lookup;
        });
    }
}
