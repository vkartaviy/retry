<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

/**
 * A {@link RetryPolicyInterface} that always permits a retry.
 * Can also be used as a base class for other policies, e.g. for test purposes as a stub.
 */
class AlwaysRetryPolicy extends AbstractRetryPolicy
{
    public function canRetry(RetryContextInterface $context)
    {
        return true;
    }
}
