<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\Fixtures;

use Keboola\Retry\BackOff\BackOffPolicyInterface;
use Keboola\Retry\BackOff\BackOffContextInterface;
use Keboola\Retry\RetryContextInterface;

class MockBackOffStrategy implements BackOffPolicyInterface
{
    /** @var  int */
    public $backOffCalls;

    /** @var  int */
    public $initCalls;

    public function start(?RetryContextInterface $context = null): ?BackOffContextInterface
    {
        $this->initCalls++;
        return null;
    }

    public function backOff(?BackOffContextInterface $context = null): void
    {
        $this->backOffCalls++;
    }
}
