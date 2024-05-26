<?php

namespace App\Core\Domains\MinecraftUUID\Actions;

use App\Core\Domains\MinecraftUUID\MinecraftUUID;
use Illuminate\Support\Facades\Http;

class LookupMinecraftUUID
{
    public function fetch(MinecraftUUID $uuid)
    {
        $url = 'https://playerdb.co/api/player/minecraft/'.$uuid->trimmed();
        $headers = [
            'user-agent' => 'https://projectcitybuild.com'
        ];
        $response = Http::withHeaders($headers)
            ->get($url)
            ->json();
    }
}
