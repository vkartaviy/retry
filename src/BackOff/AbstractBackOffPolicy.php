<?php

declare(strict_types=1);

namespace Keboola\Retry\BackOff;

use Keboola\Retry\RetryContextInterface;

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
