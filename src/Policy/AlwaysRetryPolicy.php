<?php

declare(strict_types=1);

namespace Keboola\Retry\Policy;

use Keboola\Retry\RetryContextInterface;

/**
 * A {@link RetryPolicyInterface} that always permits a retry.
 * Can also be used as a base class for other policies, e.g. for test purposes as a stub.
 */
class AlwaysRetryPolicy extends AbstractRetryPolicy
{
    public function canRetry(RetryContextInterface $context): bool
    {
        return true;
    }
}
