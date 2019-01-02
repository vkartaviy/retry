<?php

declare(strict_types=1);

namespace Retry\BackOff;

class DefaultSleeper implements SleeperInterface
{
    /**
     * Pause for the specified period using whatever means available.
     *
     * @param int $period
     */
    public function sleep($period)
    {
        usleep($period * 1000);
    }
}
