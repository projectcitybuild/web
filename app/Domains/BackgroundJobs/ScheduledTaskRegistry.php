<?php

namespace App\Domains\BackgroundJobs;

use DateTimeZone;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Carbon;
use RuntimeException;

final class ScheduledTaskRegistry
{
    public function __construct(
        private Schedule $schedule,
        private Container $container,
    ) {}

    /**
     * @return list<array{
     *     id: string,
     *     name: string,
     *     subtitle: string,
     *     expression: string,
     *     frequency: string,
     *     timezone: string,
     *     nextRunAt: string,
     *     secondsUntilNextRun: int
     * }>
     */
    public function all(): array
    {
        $now = now();

        return array_map(function (array $identifiedEvent) use ($now): array {
            $event = $identifiedEvent['event'];
            $nextRun = $this->nextRunDate($event, $now);

            return [
                'id' => $identifiedEvent['id'],
                'name' => $event->attributes['admin_title'] ?? $event->getSummaryForDisplay(),
                'subtitle' => $event->attributes['admin_subtitle'] ?? $event->command ?? $event->getSummaryForDisplay(),
                'expression' => $event->expression,
                'frequency' => $this->frequency($event->expression),
                'timezone' => $this->timezone($event),
                'nextRunAt' => $nextRun->toISOString(),
                'secondsUntilNextRun' => max(0, $nextRun->getTimestamp() - $now->getTimestamp()),
            ];
        }, $this->identifiedEvents());
    }

    public function find(string $id): ?Event
    {
        foreach ($this->identifiedEvents() as $identifiedEvent) {
            if ($identifiedEvent['id'] === $id) {
                return $identifiedEvent['event'];
            }
        }

        return null;
    }

    public function run(string $id): void
    {
        $event = $this->find($id);

        if ($event === null) {
            throw new RuntimeException('The scheduled task is no longer available.');
        }

        $event->runInBackground = false;
        $event->run($this->container);
    }

    /**
     * @return list<array{id: string, event: Event}>
     */
    private function identifiedEvents(): array
    {
        $occurrences = [];
        $identifiedEvents = [];

        foreach ($this->schedule->events() as $event) {
            $fingerprint = hash('sha256', implode("\0", [
                $event::class,
                $event->command ?? '',
                $event->getSummaryForDisplay(),
                $event->expression,
                $this->timezone($event),
                $event->description ?? '',
            ]));
            $occurrences[$fingerprint] = ($occurrences[$fingerprint] ?? 0) + 1;

            $identifiedEvents[] = [
                'id' => hash('sha256', $fingerprint."\0".$occurrences[$fingerprint]),
                'event' => $event,
            ];
        }

        return $identifiedEvents;
    }

    private function nextRunDate(Event $event, Carbon $now): Carbon
    {
        $nextRun = $event->nextRunDate($now);

        if (! $event->isRepeatable()) {
            return $nextRun;
        }

        $timezone = $this->timezone($event);
        $localNow = $now->copy()->setTimezone($timezone);
        $previousDueDate = $event->nextRunDate($localNow, -1, true);

        if (! $localNow->copy()->startOfMinute()->equalTo($previousDueDate)) {
            return $nextRun;
        }

        return $localNow
            ->endOfSecond()
            ->ceilSeconds((int) $event->repeatSeconds);
    }

    private function timezone(Event $event): string
    {
        if ($event->timezone instanceof DateTimeZone) {
            return $event->timezone->getName();
        }

        return $event->timezone ?? (string) config('app.timezone');
    }

    private function frequency(string $expression): string
    {
        $fields = preg_split('/\s+/', trim($expression));

        if (count($fields) !== 5) {
            return 'Cron: '.$expression;
        }

        [$minute, $hour, $dayOfMonth, $month, $dayOfWeek] = $fields;

        if ($minute === '*' && $hour === '*' && $dayOfMonth === '*' && $month === '*' && $dayOfWeek === '*') {
            return 'Every minute';
        }

        if (preg_match('/^\*\/(\d+)$/', $minute, $matches) === 1
            && $hour === '*'
            && $dayOfMonth === '*'
            && $month === '*'
            && $dayOfWeek === '*') {
            return 'Every '.$matches[1].' minutes';
        }

        if ($minute === '0' && $hour === '*' && $dayOfMonth === '*' && $month === '*' && $dayOfWeek === '*') {
            return 'Every hour';
        }

        if (ctype_digit($minute) && $hour === '*' && $dayOfMonth === '*' && $month === '*' && $dayOfWeek === '*') {
            return 'Hourly at minute '.$minute;
        }

        if (ctype_digit($minute)
            && ctype_digit($hour)
            && $dayOfMonth === '*'
            && $month === '*'
            && $dayOfWeek === '*') {
            return sprintf('Daily at %02d:%02d', (int) $hour, (int) $minute);
        }

        return 'Cron: '.$expression;
    }
}
