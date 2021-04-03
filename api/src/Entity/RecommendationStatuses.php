<?php

namespace App\Entity;

use Assert\Assert;

class RecommendationStatuses
{
    public const NONE = 'none';
    public const OUT_OF_RANGE = 'out_of_range';
    public const ANALOGS_IN_DEMAND = 'analogs_in_demand';
    public const PRICE_NOT_FIT = 'price_not_fit';
    public const IN_DEMAND = 'in_demand';
    public const GUESSED = 'guessed';

    public const STATUSES = [
        self::NONE,
        self::OUT_OF_RANGE,
        self::ANALOGS_IN_DEMAND,
        self::PRICE_NOT_FIT,
        self::IN_DEMAND,
        self::GUESSED,
    ];

    public static function validate(string $status): void
    {
        Assert::that($status)->inArray(self::STATUSES);
    }
}
