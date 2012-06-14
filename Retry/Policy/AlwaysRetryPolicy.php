<?php

namespace Batch\Retry\Policy;

use Batch\Retry\RetryContext;

/**
 * A {@link RetryPolicyInterface} that always permits a retry. Can also be used as a base
 * class for other policies, e.g. for test purposes as a stub.
 */
class AlwaysRetryPolicy extends AbstractRetryPolicy
{
    public function canRetry(RetryContext $context)
    {
        return true;
    }
}
