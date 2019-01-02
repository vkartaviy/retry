<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

abstract class AbstractRetryPolicy implements RetryPolicyInterface
{
    /**
     * @inheritdoc
     */
    public function open()
    {
        return new RetryContext();
    }

    /**
     * @inheritdoc
     */
    public function registerException(RetryContextInterface $context, \Exception $exception)
    {
        $context->registerException($exception);
    }
}
