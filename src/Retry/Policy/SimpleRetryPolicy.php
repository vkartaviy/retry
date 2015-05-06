<?php

namespace Retry\Policy;

use Retry\RetryContextInterface;

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
    const DEFAULT_MAX_ATTEMPTS = 3;

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
    public function __construct($maxAttempts = null, array $retryableExceptions = null)
    {
        if ($maxAttempts === null) {
            $maxAttempts = self::DEFAULT_MAX_ATTEMPTS;
        }

        $this->maxAttempts = (int) $maxAttempts;

        if ($retryableExceptions) {
            $this->retryableExceptions = $retryableExceptions;
        }
    }

    /**
     * The maximum number of retry attempts before failure.
     *
     * @return int The maximum number of attempts
     */
    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }

    /**
     * Setter for retry attempts.
     *
     * @param int $maxAttempts The number of attempts before a retry becomes impossible.
     * @return void
     */
    public function setMaxAttempts($maxAttempts)
    {
        $this->maxAttempts = (int) $maxAttempts;
    }

    /**
     * @param array $retryableExceptions
     * @return void
     */
    public function setRetryableExceptions(array $retryableExceptions)
    {
        $this->retryableExceptions = $retryableExceptions;
    }

    public function canRetry(RetryContextInterface $context)
    {
        $e = $context->getLastException();

        return (!$e || $this->shouldRetryForException($e)) && $context->getRetryCount() < $this->maxAttempts;
    }

    private function shouldRetryForException(\Exception $e)
    {
        foreach ($this->retryableExceptions as $class) {
            if (is_a($e, $class)) {
                return true;
            }
        }

        return false;
    }
}
