<?php

namespace Domain\BuilderRankApplications\Entities;

enum ApplicationStatus: int
{
    case IN_PROGRESS = 1;
    case APPROVED = 2;
    case DENIED = 3;
}
