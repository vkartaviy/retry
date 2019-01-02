<?php

declare(strict_types=1);

namespace Retry\BackOff;

class ExponentialBackOffContext implements BackOffContextInterface
{
    private $seed;
    private $multiplier;
    private $max;

    private $interval;

    public function __construct($seed, $multiplier, $max)
    {
        $this->seed       = max(1, (int) $seed);
        $this->multiplier = max(1, (float) $multiplier);
        $this->max        = max(1, (int) $max);

        $this->interval = $this->seed;
    }

    public function getIntervalAndIncrement()
    {
        $interval = $this->interval;

        if ($interval > $this->max) {
            $interval = $this->max;
        } else {
            $this->interval = $this->getNextInterval();
        }

        return $interval;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function resetInterval(): void
    {
        $this->interval = $this->seed;
    }

    public function getNextInterval()
    {
        return $this->interval * $this->multiplier;
    }

    public function getMultiplier()
    {
        return $this->multiplier;
    }
}
