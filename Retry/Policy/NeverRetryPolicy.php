<?php

namespace Batch\Retry\Policy;

use Batch\Retry\RetryContext;

/**
 * A {@link RetryPolicyInterface} that allows the first attempt but never permits a retry.
 * Also be used as a base class for other policies, e.g. for test purposes as a stub.
 */
class NeverRetryPolicy extends AbstractRetryPolicy
{
    public function start(RetryContext $context)
    {
        $context['finished'] = false;
    }

    public function canRetry(RetryContext $context)
    {
        return !$context['finished'];
    }

    public function registerException(RetryContext $context, \Exception $exception)
    {
        parent::registerException($context, $exception);

        $context['finished'] = true;
    }
}
