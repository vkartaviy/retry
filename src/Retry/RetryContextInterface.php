<?php

declare(strict_types=1);

namespace Retry;

/**
 * Low-level access to ongoing retry operation.
 */
interface RetryContextInterface
{
    /**
     * Counts the number of retry attempts.
     *
     * @return int
     */
    public function getRetryCount(): int;

    /**
     * Set the exception for the public interface and increment retries counter.
     *
     * All {@link RetryPolicyInterface} implementations should use this method when they register the exception.
     * It should only be called once per retry attempt because it increments a counter.
     *
     * @param \Throwable $exception The exception that caused the current retry attempt to fail.
     * @return void
     */
    public function registerException(\Throwable $exception): void;

    /**
     * Accessor for the exception object that caused the current retry.
     *
     * @return \Throwable The last exception that caused a retry, or possibly null.
     */
    public function getLastException(): ?\Throwable;
}
