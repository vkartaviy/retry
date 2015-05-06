<?php

namespace Retry\Policy;

use Retry\RetryContextInterface;

class TimeoutRetryContext extends RetryContext
{
    private $timeout;
    private $start;

    public function __construct($timeout)
    {
        $this->timeout = (int) $timeout;
        $this->start   = microtime(true);
    }

    public function isAlive()
    {
        return (microtime(true) - $this->start) * 1000 <= $this->timeout;
    }

    /**
     * @param RetryContextInterface $context
     * @return TimeoutRetryContext
     */
    public static function cast(RetryContextInterface $context)
    {
        if (!$context instanceof TimeoutRetryContext) {
            throw new \InvalidArgumentException('Context is expected to be an instanceof TimeoutRetryContext.');
        }

        return $context;
    }
}