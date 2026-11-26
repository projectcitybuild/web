<?php

namespace App\Http\Controllers\Front;

use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use App\Domains\PlayerReports\Notifications\PlayerReportCreatedNotification;
use App\Http\Controllers\WebController;
use App\Http\Requests\StorePlayerReportRequest;
use App\Models\Account;
use App\Models\PlayerReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

final class PlayerReportController extends WebController
{
    public function create(Request $request): View
    {
        $account = $request->user();
        $playerName = $account instanceof Account
            ? $account->minecraftAccount()->value('alias')
            : null;

        return view('front.pages.player-report', [
            'playerName' => $playerName,
            'reporterEmail' => $account instanceof Account ? $account->email : null,
        ]);
    }

    public function store(StorePlayerReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $storedPaths = [];
        $report = null;

        try {
            $report = DB::transaction(function () use ($request, $validated, &$storedPaths): PlayerReport {
                $report = PlayerReport::create([
                    'reporter_account_id' => $request->user()?->getAuthIdentifier(),
                    'reporter_name' => $validated['reporter_name'],
                    'reporter_contact' => $validated['reporter_contact'],
                    'reporter_email' => $validated['reporter_email'] ?? null,
                    'reported_player_name' => $validated['reported_player_name'],
                    'subject' => $validated['subject'],
                    'details' => $validated['report_details'],
                    'visibility' => PlayerReportVisibility::from($validated['visibility']),
                ]);

                foreach ($request->file('attachments', []) as $file) {
                    $path = $file->store('player-reports', 'player-reports');
                    if ($path === false) {
                        throw new RuntimeException('Unable to store player report attachment.');
                    }

                    $storedPaths[] = $path;
                    $report->attachments()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                        'file_size' => $file->getSize(),
                    ]);
                }

                return $report;
            });
        } finally {
            if ($report === null) {
                Storage::disk('player-reports')->delete($storedPaths);
            }
        }

        $report->notify(new PlayerReportCreatedNotification($report));

        return to_route('report')
            ->with([
                'success' => 'Your report has been submitted. Thank you for taking the time to contact us.',
                'report_id' => $report->id,
            ]);
    }
}
