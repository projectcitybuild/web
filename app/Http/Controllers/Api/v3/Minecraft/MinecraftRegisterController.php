<?php

namespace App\Http\Controllers\Api\v3\Minecraft;

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Data\Exceptions\ForbiddenException;
use App\Core\Data\Exceptions\UnauthorisedException;
use App\Core\Domains\MinecraftUUID\Actions\LookupMinecraftUUID;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Core\Domains\Tokens\TokenGenerator;
use App\Http\Controllers\ApiController;
use App\Http\Resources\AccountResource;
use App\Models\MinecraftAuthCode;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class MinecraftRegisterController extends ApiController
{
    public function store(
        Request $request,
        LookupMinecraftUUID $lookupMinecraftUUID,
    ): array {
        $request->validate([
            'email' => ['required', 'email'],
            'minecraft_uuid' => ['required', MinecraftUUIDRule::class],
            'minecraft_alias' => ['required', 'string'],
        ]);

        $uuid = new MinecraftUUID($request->get('minecraft_uuid'));
        $lookup = $lookupMinecraftUUID->fetch($uuid);

        if ($lookup === null) {
            throw ValidationException::withMessages(['error' => 'Minecraft UUID does not exist']);
        }

        MinecraftRegistration::where('minecraft_uuid', $uuid)->delete();

        return MinecraftRegistration::create([
            'email' => $request->get('email'),
            'minecraft_uuid' => $uuid,
            'minecraft_alias' => $request->get('minecraft_alias'),
            'code' => Str::random(6),
            'expires_at' => now()->addMinutes(15),
        ]);
    }
}
