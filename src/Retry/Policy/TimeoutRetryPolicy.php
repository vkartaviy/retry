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
    public const DEFAULT_TIMEOUT = 1000;

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
     * @param int
     * @return void
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = (int) $timeout;
    }

    public function open(): RetryContextInterface
    {
        return new TimeoutRetryContext($this->timeout);
    }

    public function canRetry(RetryContextInterface $context): bool
    {
        $context = TimeoutRetryContext::cast($context);

        return $context->isAlive();
    }
}
