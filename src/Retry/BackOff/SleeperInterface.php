<?php

namespace Retry\BackOff;

interface SleeperInterface
{
    /**
     * Pause for the specified period using whatever means available.
     *
     * @param int $period
     */
    function sleep($period);
}
