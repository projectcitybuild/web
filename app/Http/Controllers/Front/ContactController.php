<?php

namespace App\Http\Controllers\Front;

use App\Domains\Contact\Notifications\ContactNotification;
use App\Http\Controllers\WebController;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

final class ContactController extends WebController
{
    public function index()
    {
        return view('front.pages.contact');
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $notification = new ContactNotification(
            name: $validated['name'],
            email: $validated['email'],
            subject: $validated['subject'],
            inquiry: $validated['inquiry'],
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        $discordChannel = config('discord.webhook_contact_channel');
        throw_if (empty($discordChannel), 'No discord channel set for contact form');
        Notification::route('discord', $discordChannel)
            ->notify($notification);

        $email = config('mail.admin_email');
        throw_if (empty($email), 'No admin email set for contact form');
        Notification::route('mail', $email)
            ->notify($notification);

        Log::info('Contact form submitted', [
            'subject' => $validated['subject'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('front.contact')
            ->with(['success' => 'Your inquiry has been submitted. Thank you']);
    }
}
