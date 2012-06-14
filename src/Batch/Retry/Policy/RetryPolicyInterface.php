<?php

namespace Batch\Retry\Policy;

use Batch\Retry\RetryContext;

interface RetryPolicyInterface
{
    /**
     * @param RetryContext $context The current status object.
     * @return void
     */
    function start(RetryContext $context);

    /**
     * @param RetryContext $context The current status object
     * @return boolean Returns TRUE if the operation can proceed
     */
    function canRetry(RetryContext $context);

    /**
     * Called once per retry attempt, after the callback fails.
     *
     * @param RetryContext $context   The current status object
     * @param \Exception   $exception The throwed exception
     * @return void
     */
    function registerException(RetryContext $context, \Exception $exception);
}
