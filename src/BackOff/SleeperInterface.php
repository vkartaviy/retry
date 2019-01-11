<?php

declare(strict_types=1);

namespace Keboola\Retry\BackOff;

interface SleeperInterface
{
    /**
     * Pause for the specified period using whatever means available.
     *
     * @param int $period
     */
    public function sleep(int $period): void;
}
