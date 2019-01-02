<?php

declare(strict_types=1);

namespace Retry\BackOff;

class ExponentialBackOffContext implements BackOffContextInterface
{
    /** @var int */
    private $seed;

    /** @var float */
    private $multiplier;

    /** @var int */
    private $max;

    /** @var int */
    private $interval;

    public function __construct(int $seed, float $multiplier, int $max)
    {
        $this->seed       = max(1, $seed);
        $this->multiplier = max(1, $multiplier);
        $this->max        = max(1, $max);

        $this->interval = $this->seed;
    }

    public function getIntervalAndIncrement(): int
    {
        $interval = $this->interval;

        if ($interval > $this->max) {
            $interval = $this->max;
        } else {
            $this->interval = $this->getNextInterval();
        }

        return $interval;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function resetInterval(): void
    {
        $this->interval = $this->seed;
    }

    public function getNextInterval(): int
    {
        return (int) $this->interval * $this->multiplier;
    }

    public function getMultiplier(): float
    {
        return $this->multiplier;
    }
}
