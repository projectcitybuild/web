<?php
namespace App\Modules\Donations\Repositories;

use App\Modules\Donations\Models\Donation;

class DonationRepository {

    private $donationModel;

    public function __construct(Donation $donationModel) {
        $this->donationModel = $donationModel;
    }

    

}