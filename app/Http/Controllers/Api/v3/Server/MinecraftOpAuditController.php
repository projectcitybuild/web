<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Domains\Pim\Services\OpAuditService;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftOpAuditController extends ApiController
{
    public function __construct(
        private readonly OpAuditService $opAuditService,
    ) {}

    public function store(Request $request)
    {
        $validated = collect($request->validate([
            'command' => ['required'],
            'actor' => ['required', 'in:console,command_block'],
            'ip' => ['required', 'ip'],
        ]));

        return $this->opAuditService->logOpCommand(
            command: $validated->get('command'),
            actor: $validated->get('actor'),
            ip: $validated->get('ip'),
        );
    }
}
