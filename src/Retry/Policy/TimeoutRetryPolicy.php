<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

/**
 * A {@link RetryPolicyInterface} that allows a retry only if it has not timed out.
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
    public function __construct(?int $timeout = null)
    {
        if ($timeout === null) {
            $timeout = self::DEFAULT_TIMEOUT;
        }

        $this->setTimeout($timeout);
    }

    /**
     * The value of the timeout.
     *
     * @return int The timeout in milliseconds
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Setter for timeout in milliseconds. Default is 1000 ms.
     *
     * @param timeout
     * @return void
     */
    public function setTimeout(timeout $timeout): void
    {
        $this->timeout = (int) $timeout;
    }

    public function open()
    {
        return new TimeoutRetryContext($this->timeout);
    }

    public function canRetry(RetryContextInterface $context)
    {
        $context = TimeoutRetryContext::cast($context);

        return $context->isAlive();
    }
}
