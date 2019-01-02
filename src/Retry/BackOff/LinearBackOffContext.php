<?php

declare(strict_types=1);

namespace Retry\BackOff;

class LinearBackOffContext implements BackOffContextInterface
{
    private $seed;
    private $delta;
    private $max;

    private $interval;

    public function __construct($seed, $delta, $max)
    {
        $this->seed  = max(1, (int) $seed);
        $this->delta = max(1, (int) $delta);
        $this->max   = max(1, (int) $max);

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
        return $this->interval + $this->delta;
    }

    public function getDelta()
    {
        return $this->delta;
    }
}
