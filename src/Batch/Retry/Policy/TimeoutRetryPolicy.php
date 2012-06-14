<?php

namespace Batch\Retry\Policy;

use Batch\Retry\RetryContext;

/**
 * A {@link RetryPolicyInterface} that allows a retry only if it hasn't timed out.
 */
class TimeoutRetryPolicy extends AbstractRetryPolicy
{
    /**
     * Default value for timeout (milliseconds).
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 1000;

    /**
     * The value of the timeout.
     *
     * @var int
     */
    private $timeout;

    /**
     * @param int $timeout The timeout in milliseconds. Default is 1000 ms.
     */
    public function __construct($timeout = self::DEFAULT_TIMEOUT)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * The value of the timeout.
     *
     * @return int The timeout in milliseconds
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Setter for timeout in milliseconds. Default is 1000 ms.
     *
     * @param timeout
     * @return void
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function start(RetryContext $context)
    {
        $context['start'] = microtime(true);
    }

    public function canRetry(RetryContext $context)
    {
        return (microtime(true) - $context['start']) * 1000 <= $this->timeout;
    }
}
