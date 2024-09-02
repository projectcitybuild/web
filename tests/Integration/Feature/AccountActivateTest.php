<?php

use App\Domains\Registration\Notifications\AccountActivationNotification;
use App\Models\Account;
use App\Models\Group;
use Illuminate\Support\Facades\Notification;

//    public function test_user_can_verify_email()
//    {
//        $unactivatedAccount = Account::factory()->unactivated()->create();
//
//        // TODO: find way to do this without manually creating the URL here
//        $signedURLGenerator = new LaravelSignedURLGenerator();
//        $activationURL = $signedURLGenerator->makeTemporary(
//            routeName: 'front.register.activate',
//            expiresAt: now()->addDay(),
//            parameters: ['email' => $unactivatedAccount->email],
//        );
//
//        $this->get($activationURL)
//            ->assertSuccessful();
//
//        $this->assertEquals(true, Account::first()->activated);
//    }
//
//    public function test_user_is_redirected_to_intent_after_verification()
//    {
//        Session::put('url.intended', '/my/path');
//
//        $unactivatedAccount = Account::factory()->unactivated()->create();
//
//        // TODO: find way to do this without manually creating the URL here
//        $signedURLGenerator = new LaravelSignedURLGenerator();
//        $activationURL = $signedURLGenerator->makeTemporary(
//            routeName: 'front.register.activate',
//            expiresAt: now()->addDay(),
//            parameters: ['email' => $unactivatedAccount->email],
//        );
//
//        $this->get($activationURL)
//            ->assertRedirect('/my/path');
//    }
