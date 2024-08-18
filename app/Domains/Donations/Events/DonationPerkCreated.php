<?php

namespace App\Domains\Donations\Events;

use App\Models\DonationPerk;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationPerkCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DonationPerk $donationPerk)
    {
    }
}
