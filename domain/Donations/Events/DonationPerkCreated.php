<?php

namespace Domain\Donations\Events;

use Entities\Models\Eloquent\DonationPerk;
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
