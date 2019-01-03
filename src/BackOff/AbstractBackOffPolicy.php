<?php

declare(strict_types=1);

namespace Retry\BackOff;

use Retry\RetryContextInterface;

abstract class AbstractBackOffPolicy implements BackOffPolicyInterface
{
    /**
     * @inheritdoc
     */
    public function start(?RetryContextInterface $context = null): ?BackOffContextInterface
    {
        return null;
    }
}
