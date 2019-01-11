<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\BackOff\Fixtures;

use Keboola\Retry\BackOff\SleeperInterface;

class DummySleeper implements SleeperInterface
{
    /** @var  array */
    private $backOffs;

    /**
     * Public getter for the last back-off value.
     *
     * @return int The last back-off value
     */
    public function getLastBackOff(): int
    {
        return end($this->backOffs);
    }

    public function getBackOffs(): array
    {
        return $this->backOffs;
    }

    public function sleep(int $backOffPeriod): void
    {
        $this->backOffs[] = $backOffPeriod;
    }
}
