<?php

namespace Retry\BackOff;

class ExponentialBackOffContext implements BackOffContextInterface
{
    private $interval;
    private $multiplier;
    private $maxInterval;

    public function __construct($interval, $multiplier, $maxInterval)
    {
        $this->interval    = max(1, (int) $interval);
        $this->multiplier  = max(1, (float) $multiplier);
        $this->maxInterval = max(1, (int) $maxInterval);
    }

    public function getIntervalAndIncrement()
    {
        $interval = $this->interval;

        if ($interval > $this->maxInterval) {
            $interval = $this->maxInterval;
        } else {
            $this->interval = $this->getNextInterval();
        }

        return $interval;
    }

    public function getInterval()
    {
        return $this->interval;
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