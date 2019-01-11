<?php

declare(strict_types=1);

namespace Keboola\Retry\Policy;

use Keboola\Retry\RetryContextInterface;

/**
 * Simple retry policy that retries a fixed number of times for a set of named
 * exceptions (and subclasses). The number of attempts includes the initial try.
 */
class SimpleRetryPolicy extends AbstractRetryPolicy
{
    /**
     * The default limit to the number of attempts for a new policy.
     *
     * @var int
     */
    public const DEFAULT_MAX_ATTEMPTS = 3;

    /**
     * The maximum number of retry attempts before failure.
     *
     * @var int
     */
    private $maxAttempts;

    /**
     * The list of retryable exceptions
     *
     * @var array
     */
    private $retryableExceptions = ['Exception'];

    /**
     * @param int        $maxAttempts The number of attempts before a retry becomes impossible.
     * @param array|null $retryableExceptions
     */
    public function __construct(?int $maxAttempts = null, ?array $retryableExceptions = null)
    {
        if ($maxAttempts === null) {
            $maxAttempts = self::DEFAULT_MAX_ATTEMPTS;
        }

        $this->maxAttempts = $maxAttempts;

        if ($retryableExceptions) {
            $this->retryableExceptions = $retryableExceptions;
        }
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

    /**
     * @param array $retryableExceptions
     * @return void
     */
    public function setRetryableExceptions(array $retryableExceptions): void
    {
        $this->retryableExceptions = $retryableExceptions;
    }

    public function canRetry(RetryContextInterface $context): bool
    {
        $e = $context->getLastException();

        return (!$e || $this->shouldRetryForException($e)) && $context->getRetryCount() < $this->maxAttempts;
    }

    private function shouldRetryForException(\Throwable $e): bool
    {
        foreach ($this->retryableExceptions as $class) {
            if (is_a($e, $class)) {
                return true;
            }
        }

        return false;
    }
}
