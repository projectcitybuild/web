@extends('front.layouts.root-layout')

@section('title', ($player->alias ?: 'Player').' - Project City Build')
@section('meta_title', ($player->alias ?: 'Player').' - Project City Build')
@section('meta_description', 'View '.$player->alias.'’s public Project City Build player profile, badges, stats, and server activity.')

@section('body')
    <div class="min-h-screen bg-[#1D1E25] text-[#C8CCCB]">
        <x-navbar />

        <main class="grid min-h-[calc(100vh-4rem)] pt-16 md:grid-cols-[334px_1fr] md:pt-0">
            <aside class="border-b border-[#2B2C34] bg-[#1D1E25] md:border-r md:border-b-0">
                <section class="flex items-center gap-4 px-5 py-5">
                    <div class="flex size-[88px] shrink-0 items-center justify-center rounded-lg bg-[#292A33]">
                        <img
                            src="https://minotar.net/body/{{ $player->uuid }}/100.png"
                            alt="3D Minecraft skin for {{ $player->alias }}"
                            class="max-h-[84px] max-w-full object-contain"
                        >
                    </div>
                    <div class="min-w-0">
                        <h1 class="break-words text-base font-semibold leading-tight text-white">{{ $player->alias ?: 'Unknown player' }}</h1>
                        @if ($player->nickname && $player->nickname !== $player->alias)
                            <p class="mt-1 truncate text-xs text-[#80808B]">{{ $player->nickname }}</p>
                        @endif
                        @if ($player->account)
                            <p class="mt-2 truncate text-xs text-[#80808B]">{{ $player->account->username }}</p>
                        @endif
                        <p class="mt-2 truncate font-mono text-[10px] text-[#676873]" title="{{ $player->uuid }}">{{ $player->uuid }}</p>
                    </div>
                </section>

                <section class="border-t border-[#2B2C34] px-5 py-4">
                    <div class="mb-4 flex items-center gap-2.5">
                        <span class="flex size-7 items-center justify-center rounded-md border border-[#2B2C34] text-[#80808B]" aria-hidden="true">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m12 3 2.2 4.46 4.92.72-3.56 3.47.84 4.9L12 14.24l-4.4 2.31.84-4.9-3.56-3.47 4.92-.72L12 3Z" />
                            </svg>
                        </span>
                        <h2 class="text-xs font-medium text-[#C8CCCB]">Badges</h2>
                        <span class="rounded bg-[#292A33] px-1.5 py-0.5 text-[10px] text-[#80808B]">{{ $badges->count() }}</span>
                    </div>

                    @if ($badges->isNotEmpty())
                        <ul class="flex flex-wrap gap-1.5">
                            @foreach ($badges as $badge)
                                <li class="flex items-center gap-1.5 rounded-md bg-[#3A3B44] px-2 py-1 text-[11px] text-[#DCDFDA]">
                                    @if ($badge->unicode_icon)
                                        <span aria-hidden="true">{{ $badge->unicode_icon }}</span>
                                    @endif
                                    <span>{{ $badge->display_name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-[#80808B]">No public badges yet.</p>
                    @endif
                </section>

                <section class="border-t border-[#2B2C34] px-5 py-4">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="flex size-7 items-center justify-center rounded-md border border-[#2B2C34] text-[#80808B]" aria-hidden="true">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 19V5m0 14h16M8 15l3-4 3 2 5-6" />
                            </svg>
                        </span>
                        <h2 class="text-xs font-medium text-[#C8CCCB]">Player stats</h2>
                    </div>

                    <dl class="divide-y divide-[#2B2C34]">
                        @foreach ([
                            'Total sessions' => $playerStats['sessions'],
                            'Play time' => $playerStats['playTime'],
                            'AFK time' => $playerStats['afkTime'],
                            'Blocks placed' => $playerStats['blocksPlaced'],
                            'Blocks destroyed' => $playerStats['blocksDestroyed'],
                            'Blocks travelled' => $playerStats['blocksTravelled'],
                        ] as $label => $value)
                            <div class="flex items-center justify-between gap-3 py-2.5 text-xs">
                                <dt class="text-[#80808B]">{{ $label }}</dt>
                                <dd class="text-right font-medium text-[#DCDFDA]">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section class="border-t border-[#2B2C34] px-5 py-4">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="flex size-7 items-center justify-center rounded-md border border-[#2B2C34] text-[#80808B]" aria-hidden="true">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="8.5" stroke-width="1.5" />
                                <path stroke-linecap="round" stroke-width="1.5" d="M12 7v5l3 2" />
                            </svg>
                        </span>
                        <h2 class="text-xs font-medium text-[#C8CCCB]">General</h2>
                    </div>

                    <dl class="space-y-3 text-[11px]">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-[#80808B]">Last connected</dt>
                            <dd class="text-right text-[#C8CCCB]">{{ $player->last_connected_at?->format('M j, Y') ?? 'Not recorded' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-[#80808B]">Player since</dt>
                            <dd class="text-right text-[#C8CCCB]">{{ $player->created_at?->format('M j, Y') ?? 'Not recorded' }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>

            <section class="min-w-0 px-4 py-6 md:px-7">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#2B2C34] pb-5">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Sessions</h2>
                        <p class="mt-1 text-xs text-[#80808B]">Connection activity by day</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="rounded-md border border-[#3C3D46] bg-[#292A33] px-3 py-1.5 text-xs text-[#C8CCCB]"
                            aria-label="{{ $activeDayCount }} {{ \Illuminate\Support\Str::plural('day', $activeDayCount) }} with recorded sessions in the past year"
                        >
                            Past year
                        </span>
                        <span class="text-xs text-[#80808B]">{{ number_format($activeDayCount) }} {{ \Illuminate\Support\Str::plural('day', $activeDayCount) }}</span>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-[#C8CCCB]">
                        <span class="font-semibold text-white">{{ now()->format('F, Y') }}</span>
                        <span class="ml-1 text-xs text-[#80808B]">Last 12 months</span>
                    </p>
                    <div class="rounded-md border border-[#3C3D46] bg-[#292A33] p-1 text-[11px]">
                        <span class="block rounded bg-[#EE4445] px-2 py-1 font-medium text-white">Year</span>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto pb-3" role="group" aria-label="Connection activity for the past year">
                    <div class="flex min-w-max gap-1">
                        <div class="grid grid-rows-7 gap-1 pt-5 pr-2 text-[10px] leading-3 text-[#80808B]" aria-hidden="true">
                            <span>Mon</span>
                            <span></span>
                            <span>Wed</span>
                            <span></span>
                            <span>Fri</span>
                            <span></span>
                            <span></span>
                        </div>

                        @foreach ($activityWeeks as $week)
                            <div class="flex flex-col gap-1">
                                <span class="h-4 text-[10px] leading-3 text-[#80808B]">{{ $week['monthLabel'] }}</span>
                                @foreach ($week['days'] as $day)
                                    @if ($day['isInRange'])
                                        <span
                                            @class([
                                                'size-3 rounded-[2px] border border-black/10 transition-colors',
                                                'bg-[#2ea043] hover:bg-[#3fb950]' => $day['isActive'],
                                                'bg-[#2B2C34] hover:bg-[#3A3B44]' => ! $day['isActive'],
                                            ])
                                            title="{{ $day['label'] }}{{ $day['isActive'] ? ' - Connected' : '' }}"
                                            aria-label="{{ $day['label'] }}{{ $day['isActive'] ? ', connected' : ', no connection recorded' }}"
                                            @if ($day['isActive']) data-session-active @endif
                                        ></span>
                                    @else
                                        <span class="size-3 rounded-[2px] border border-[#2B2C34] bg-[#1E1F26]" aria-hidden="true"></span>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#2B2C34] pt-4 text-[11px] text-[#80808B]">
                    <span>Historical activity is currently being updated. More data will be available soon.</span>
                    <span class="flex items-center gap-2" aria-label="Legend: no connection, connected">
                        <span>No session</span>
                        <span class="size-3 rounded-[2px] bg-[#2B2C34]" aria-hidden="true"></span>
                        <span class="size-3 rounded-[2px] bg-[#2ea043]" aria-hidden="true"></span>
                        <span>Connected</span>
                    </span>
                </div>
            </section>
        </main>
    </div>
@endsection
