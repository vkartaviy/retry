<?php

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
    function getRetryCount();

    /**
     * Set the exception for the public interface and increment retries counter.
     *
     * All {@link RetryPolicyInterface} implementations should use this method when they register the exception.
     * It should only be called once per retry attempt because it increments a counter.
     *
     * @param \Exception $exception The exception that caused the current retry attempt to fail.
     * @return void
     */
    function registerException(\Exception $exception);

    /**
     * Accessor for the exception object that caused the current retry.
     *
     * @return \Exception The last exception that caused a retry, or possibly null.
     */
    function getLastException();
}
