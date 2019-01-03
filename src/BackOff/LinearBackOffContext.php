<?php

declare(strict_types=1);

namespace Retry\BackOff;

class LinearBackOffContext implements BackOffContextInterface
{
    /** @var int */
    private $seed;

    /** @var int */
    private $delta;

    /** @var int */
    private $max;

    /** @var int */
    private $interval;

    public function __construct(int $seed, int $delta, int $max)
    {
        $this->seed  = max(1, $seed);
        $this->delta = max(1, $delta);
        $this->max   = max(1, $max);

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
        return $this->interval + $this->delta;
    }

    public function getDelta(): int
    {
        return $this->delta;
    }
}
