<?php

namespace Domain\BuilderRankApplications\Entities;

enum BuilderRank: int
{
    case NONE = 0;
    case INTERN = 1;
    case BUILDER = 2;
    case PLANNER = 3;

    public function humanReadable(): string
    {
        return match ($this) {
            self::NONE => "I don't have a builder rank yet",
            self::INTERN => "Intern",
            self::BUILDER => "Builder",
            self::PLANNER => "Planner",
        };
    }
}
