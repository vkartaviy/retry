<?php

namespace Batch\Test\Retry\BackOff\Fixtures;

use Batch\Retry\BackOff\SleeperInterface;

class DummySleeper implements SleeperInterface
{
    private $backOffs;

    /**
     * Public getter for the last back-off value.
     *
     * @return int The last back-off value
     */
    public function getLastBackOff()
    {
        return end($this->backOffs);
    }

    public function getBackOffs()
    {
        return $this->backOffs;
    }

    public function sleep($backOffPeriod)
    {
        $this->backOffs[] = $backOffPeriod;
    }
}
