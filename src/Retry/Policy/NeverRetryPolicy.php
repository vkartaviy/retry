<?php

namespace Retry\Policy;

use Retry\RetryContextInterface;

/**
 * A {@link RetryPolicyInterface} that allows the first attempt but never permits a retry.
 * Also be used as a base class for other policies, e.g. for test purposes as a stub.
 */
class NeverRetryPolicy extends AbstractRetryPolicy
{
    public function open()
    {
        return new NeverRetryContext();
    }

    public function canRetry(RetryContextInterface $context)
    {
        $context = NeverRetryContext::cast($context);

        return !$context->isFinished();
    }

    public function registerException(RetryContextInterface $context, \Exception $exception)
    {
        $context = NeverRetryContext::cast($context);
        $context->setFinished();

        parent::registerException($context, $exception);
    }
}
