<?php

use App\Domains\Captcha\Validator\CaptchaValidator;
use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use App\Models\Account;
use App\Models\PlayerReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('renders the report guidance and single-page form', function () {
    $this->get(route('report'))
        ->assertOk()
        ->assertSeeText('Understanding the player report process')
        ->assertSeeText('Step 1 of 3')
        ->assertSeeText('3. Report details and review')
        ->assertDontSeeText('4. Review and submit')
        ->assertSee('data-player-report-form', false)
        ->assertSee('name="attachments[]"', false);
});

it('creates a guest report and stores its evidence privately', function () {
    $this->app->instance(CaptchaValidator::class, new class implements CaptchaValidator
    {
        public function passed(?string $token, string $ip): bool
        {
            return true;
        }
    });
    Storage::fake('player-reports');

    $response = $this->post(route('report.submit'), [
        'reporter_name' => 'ReporterName',
        'reporter_contact' => 'Discord: reporter',
        'reporter_email' => 'reporter@example.com',
        'reported_player_name' => 'ReportedPlayer',
        'subject' => 'Inappropriate messages',
        'report_details' => 'The player sent threatening messages in chat.',
        'visibility' => 'standard',
        'captcha-response' => 'valid-token',
        'details' => '',
        'attachments' => [
            UploadedFile::fake()->create('evidence.pdf', 100, 'application/pdf'),
        ],
    ]);

    $report = PlayerReport::query()->with('attachments')->firstOrFail();

    $response
        ->assertRedirect(route('report'))
        ->assertSessionHas('success')
        ->assertSessionHas('report_id', $report->id);

    $this->assertModelExists($report);

    expect($report->reporter_account_id)->toBeNull()
        ->and($report->visibility)->toBe(PlayerReportVisibility::STANDARD)
        ->and($report->attachments)->toHaveCount(1);

    Storage::disk('player-reports')
        ->assertExists($report->attachments->first()->path);
});

it('associates a signed-in reporter with their account', function () {
    $this->app->instance(CaptchaValidator::class, new class implements CaptchaValidator
    {
        public function passed(?string $token, string $ip): bool
        {
            return true;
        }
    });
    $account = Account::factory()->create();

    $this->actingAs($account)
        ->post(route('report.submit'), [
            'reporter_name' => 'ReporterName',
            'reporter_contact' => 'Discord: reporter',
            'reported_player_name' => 'ReportedPlayer',
            'subject' => 'Harassment',
            'report_details' => 'The player repeatedly harassed me in chat.',
            'visibility' => 'administrators',
            'captcha-response' => 'valid-token',
            'details' => '',
        ])
        ->assertRedirect(route('report'));

    $report = PlayerReport::query()->firstOrFail();
    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
        'reporter_account_id' => $account->id,
        'visibility' => PlayerReportVisibility::ADMINISTRATORS->value,
    ]);
});

it('rejects incomplete reports without creating a record', function () {
    $this->app->instance(CaptchaValidator::class, new class implements CaptchaValidator
    {
        public function passed(?string $token, string $ip): bool
        {
            return true;
        }
    });

    $this->from(route('report'))
        ->post(route('report.submit'), [
            'reporter_name' => 'ReporterName',
            'reporter_contact' => 'Discord: reporter',
            'captcha-response' => 'valid-token',
            'details' => '',
        ])
        ->assertSessionHasErrors(['reported_player_name', 'subject', 'report_details', 'visibility']);

    $this->assertDatabaseCount('player_reports', 0);
});

it('redirects oversized attachments back with a validation error', function () {
    $this->app->instance(CaptchaValidator::class, new class implements CaptchaValidator
    {
        public function passed(?string $token, string $ip): bool
        {
            return true;
        }
    });

    $this->from(route('report'))
        ->post(route('report.submit'), [
            'reporter_name' => 'ReporterName',
            'reporter_contact' => 'Discord: reporter',
            'reported_player_name' => 'ReportedPlayer',
            'subject' => 'Harassment',
            'report_details' => 'The player repeatedly harassed me in chat.',
            'visibility' => 'standard',
            'captcha-response' => 'valid-token',
            'details' => '',
            'attachments' => [
                UploadedFile::fake()->create('oversized.pdf', 5121, 'application/pdf'),
            ],
        ])
        ->assertRedirectToRoute('report')
        ->assertSessionHasErrors(['attachments.0']);

    $this->get(route('report'))
        ->assertOk()
        ->assertSeeText('5120 kilobytes');

    $this->assertDatabaseCount('player_reports', 0);
});

it('rejects unsupported attachment types without creating a report', function () {
    $this->app->instance(CaptchaValidator::class, new class implements CaptchaValidator
    {
        public function passed(?string $token, string $ip): bool
        {
            return true;
        }
    });

    $this->from(route('report'))
        ->post(route('report.submit'), [
            'reporter_name' => 'ReporterName',
            'reporter_contact' => 'Discord: reporter',
            'reported_player_name' => 'ReportedPlayer',
            'subject' => 'Harassment',
            'report_details' => 'The player repeatedly harassed me in chat.',
            'visibility' => 'standard',
            'captcha-response' => 'valid-token',
            'details' => '',
            'attachments' => [
                UploadedFile::fake()->create('payload.exe', 10, 'application/octet-stream'),
            ],
        ])
        ->assertSessionHasErrors(['attachments.0']);

    $this->assertDatabaseCount('player_reports', 0);
});
