<?php

declare(strict_types=1);

namespace Keboola\Retry\Policy;

use Keboola\Retry\RetryContextInterface;

abstract class AbstractRetryPolicy implements RetryPolicyInterface
{
    /**
     * @inheritdoc
     */
    public function open(): RetryContextInterface
    {
        return new RetryContext();
    }

    /**
     * @inheritdoc
     */
    public function registerException(RetryContextInterface $context, \Throwable $exception): void
    {
        $context->registerException($exception);
    }
}
