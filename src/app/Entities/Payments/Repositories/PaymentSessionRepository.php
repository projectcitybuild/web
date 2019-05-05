<?php

namespace App\Entities\Payments\Repositories;

use App\Repository;
use App\Entities\Payments\Models\PaymentSession;
use Illuminate\Support\Carbon;

final class PaymentSessionRepository extends Repository
{
    protected $model = PaymentSession::class;

    public function create(string $externalSessionId, string $serializedData, Carbon $expiresAt)
    {
        $this->getModel()->create([
            'external_session_id' => $externalSessionId,
            'data' => $serializedData,
            'expires_at' => $expiresAt,
        ]);
    }
}