<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

class TimeoutRetryContext extends RetryContext
{
    /** @var int  */
    private $timeout;

    /** @var mixed  */
    private $start;

    public function __construct(int $timeout)
    {
        $this->timeout = $timeout;
        $this->start   = microtime(true);
    }

    public function isAlive(): bool
    {
        return (microtime(true) - $this->start) * 1000 <= $this->timeout;
    }

    public static function cast(RetryContextInterface $context): TimeoutRetryContext
    {
        if (!$context instanceof TimeoutRetryContext) {
            throw new \InvalidArgumentException('Context is expected to be an instanceof TimeoutRetryContext.');
        }

        return $context;
    }
}
