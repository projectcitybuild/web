<?php

namespace App\Http\Controllers\Front;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\View\View;

class PublicProfileController
{
    public function show(MinecraftUUID $uuid): View
    {
        $player = MinecraftPlayer::query()
            ->with([
                'account.badges' => function (Relation $query): void {
                    $query->where('list_hidden', false);
                },
            ])
            ->whereUuid($uuid)
            ->firstOrFail();

        $today = Carbon::today();
        $firstDay = $today->copy()->subDays(364);
        $activeDates = MinecraftPlayerSession::query()
            ->where('player_id', $player->id)
            ->where('starts_at', '>=', $firstDay->toDateTimeString())
            ->where('starts_at', '<', $today->copy()->addDay()->toDateTimeString())
            ->selectRaw('DATE(starts_at) as session_date')
            ->distinct()
            ->pluck('session_date');
        $activeDateLookup = array_fill_keys($activeDates->all(), true);

        $activityWeeks = [];
        $weekStart = $firstDay->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $today->copy()->endOfWeek(Carbon::SUNDAY);

        while ($weekStart->lessThanOrEqualTo($calendarEnd)) {
            $days = [];
            $monthLabel = null;

            for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                $date = $weekStart->copy()->addDays($dayOffset);
                $dateString = $date->toDateString();
                $isInRange = $dateString >= $firstDay->toDateString()
                    && $dateString <= $today->toDateString();

                if ($isInRange && $date->day === 1) {
                    $monthLabel = $date->format('M');
                }

                $days[] = [
                    'date' => $date,
                    'label' => $date->format('F j, Y'),
                    'isInRange' => $isInRange,
                    'isActive' => $isInRange && isset($activeDateLookup[$dateString]),
                ];
            }

            $activityWeeks[] = [
                'monthLabel' => $monthLabel,
                'days' => $days,
            ];
            $weekStart->addWeek();
        }

        return view('front.pages.profile', [
            'player' => $player,
            'badges' => $player->account?->badges ?? collect(),
            'activityWeeks' => $activityWeeks,
            'activeDayCount' => $activeDates->count(),
            'playerStats' => [
                'sessions' => number_format((int) $player->sessions),
                'playTime' => number_format(intdiv((int) $player->play_time, 3600)).' hrs',
                'afkTime' => number_format(intdiv((int) $player->afk_time, 3600)).' hrs',
                'blocksPlaced' => number_format((int) $player->blocks_placed),
                'blocksDestroyed' => number_format((int) $player->blocks_destroyed),
                'blocksTravelled' => number_format((int) $player->blocks_travelled),
            ],
        ]);
    }
}
