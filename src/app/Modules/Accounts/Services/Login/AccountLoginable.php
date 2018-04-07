<?php
namespace App\Modules\Accounts\Services\Login;

use App\Modules\Discourse\Services\Authentication\DiscoursePayload;


interface AccountLoginable {
    public function execute(string $nonce) : DiscoursePayload;
}