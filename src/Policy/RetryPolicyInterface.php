<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

interface RetryPolicyInterface
{
    /**
     * Acquire resources needed for the retry operation.
     *
     * @return RetryContextInterface
     */
    public function open(): RetryContextInterface;

    /**
     * @param RetryContextInterface $context The current status object
     * @return boolean Returns TRUE if the operation can proceed
     */
    public function canRetry(RetryContextInterface $context): bool;

    /**
     * Called once per retry attempt, after the callback fails.
     *
     * @param RetryContextInterface $context The current status object
     * @param \Throwable $exception The thrown exception
     * @return void
     */
    public function registerException(RetryContextInterface $context, \Throwable $exception): void;
}
