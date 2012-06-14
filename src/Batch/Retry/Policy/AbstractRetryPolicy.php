<?php

namespace Batch\Retry\Policy;

use Batch\Retry\RetryContext;

abstract class AbstractRetryPolicy implements RetryPolicyInterface
{
    public function start(RetryContext $context)
    {
    }

    public function registerException(RetryContext $context, \Exception $exception)
    {
        $context->registerException($exception);
    }
}
