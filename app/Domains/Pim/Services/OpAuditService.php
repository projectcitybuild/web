<?php

namespace App\Domains\Pim\Services;

use App\Domains\Pim\Notifications\OpCommandUsedNotification;
use App\Models\CommandAudit;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OpAuditService
{
    public function logOpCommand(
        string $command,
        string $actor,
        string $ip,
        ?string $meta
    ) {
        if (! Str::startsWith($command, '/')) {
            $command = '/'.$command;
        }

        $audit = CommandAudit::create([
            'command' => $command,
            'actor' => $actor,
            'ip' => $ip,
            'meta' => $meta,
            'created_at' => now(),
        ]);

        $channel = config('discord.webhook_op_elevation_channel', default: '');
        throw_if($channel === '', 'discord.webhook_op_elevation_channel is missing');
        Notification::route('discord', $channel)->notify(
            new OpCommandUsedNotification($audit),
        );

        return response(null, status: Response::HTTP_CREATED);
    }
}
