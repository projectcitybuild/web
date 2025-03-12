<?php

namespace App\Core\Domains\Discord\Data;

class DiscordPoll
{
    /**
     * @param array<DiscordPollMedia> $answers
     *
     * @see https://discord.com/developers/docs/resources/poll#poll-create-request-object
     */
    public function __construct(
        public DiscordPollMedia $question,
        public array $answers,
        public int $durationInHours = 24,
        public bool $allowMultiselect = false,
    ) {}

    public function toJson(): array
    {
        return [
            'poll' => [
                'question'=> $this->question->toJson(),
                'answers' => array_map(fn ($answer) => $answer->toJson(), $this->answers),
                'duration' => $this->durationInHours,
                'allow_multiselect' => $this->allowMultiselect,
            ],
        ];
    }
}
