<?php

namespace Domain\Donations\Entities;

enum DonationType
{
    case ONE_OFF;
    case SUBSCRIPTION;
}
