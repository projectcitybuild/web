<?php

namespace Domain\Payments;

use App\Entities\Payments\Models\AccountPaymentSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class DonationService
{
    private PaymentAdapter $paymentAdapter;

    public function __constructor(PaymentAdapter $paymentAdapter)
    {
        $this->paymentAdapter = $paymentAdapter;
    }

    public function startCheckout(string $productId, ?int $accountId): string
    {
        $pcbSessionUUID = Str::uuid();
        $checkoutURL = $this->paymentAdapter->createCheckoutSession($pcbSessionUUID, $productId);

        $pcbPaymentSession = AccountPaymentSession::create([
            'session_id' => $pcbSessionUUID->toString(),
            'account_id' => $accountId,
            'is_processed' => false,
        ]);

        Log::debug('Generated payment session', ['session' => $pcbPaymentSession]);

        return $checkoutURL;
    }
}
