<?php

declare(strict_types=1);

namespace Retry\Test\Fixtures;

use Retry\BackOff\BackOffPolicyInterface;
use Retry\BackOff\BackOffContextInterface;
use Retry\RetryContextInterface;

class MockBackOffStrategy implements BackOffPolicyInterface
{
    /** @var  int */
    public $backOffCalls;

    /** @var  int */
    public $initCalls;

    public function start(?RetryContextInterface $context = null): ?BackOffContextInterface
    {
        $this->initCalls++;
    }

    public function backOff(?BackOffContextInterface $context = null): void
    {
        $this->backOffCalls++;
    }
}
