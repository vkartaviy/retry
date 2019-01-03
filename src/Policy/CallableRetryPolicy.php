<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

/**
 * Retry policy that retries a fixed number of times using a callback method to decide whether or not the Exception
 * should be retried.
 * The number of attempts includes the initial try.
 */
class CallableRetryPolicy extends AbstractRetryPolicy
{
    /**
     * The default limit to the number of attempts for a new policy.
     *
     * @var int
     */
    public const DEFAULT_MAX_ATTEMPTS = 5;

    /**
     * The maximum number of retry attempts before failure.
     *
     * @var int
     */
    private $maxAttempts;

    /** @var  callable */
    private $shouldRetryForException;

    /**
     * @param callable   $shouldRetry Method that accepts Throwable and returns bool, whether an Exception should be
     *                   retried or not.  If not provided, defaults to AlwaysRetryPolicy equivalent
     * @param int        $maxAttempts The number of attempts before a retry becomes impossible.
     */
    public function __construct(?callable $shouldRetry = null, ?int $maxAttempts = null)
    {
        if ($maxAttempts === null) {
            $maxAttempts = self::DEFAULT_MAX_ATTEMPTS;
        }

        $this->maxAttempts = $maxAttempts;

        if ($shouldRetry === null) {
            $shouldRetry = function (\Throwable $e): bool {
                return true;
            };
        }

        $this->shouldRetryForException = $shouldRetry;
    }

    /**
     * The maximum number of retry attempts before failure.
     *
     * @return int The maximum number of attempts
     */
    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    /**
     * Setter for retry attempts.
     *
     * @param int $maxAttempts The number of attempts before a retry becomes impossible.
     * @return void
     */
    public function setMaxAttempts(int $maxAttempts): void
    {
        $this->maxAttempts = $maxAttempts;
    }

    public function setShouldRetryMethod(callable $shouldRetry): void
    {
        $this->shouldRetryForException = $shouldRetry;
    }

    public function canRetry(RetryContextInterface $context): bool
    {
        $e = $context->getLastException();

        $shouldRetry = !$e || call_user_func($this->shouldRetryForException, $e);

        return $shouldRetry && $context->getRetryCount() < $this->maxAttempts;
    }

    /*
    private function shouldRetryForException
    (\Throwable $e): bool
    {
        foreach ($this->retryableExceptions as $class) {
            if (is_a($e, $class)) {
                return true;
            }
        }

        return false;
    }
    */
}
