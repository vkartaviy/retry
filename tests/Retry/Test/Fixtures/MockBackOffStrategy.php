<?php

namespace Retry\Test\Fixtures;

use Retry\BackOff\BackOffPolicyInterface;
use Retry\BackOff\BackOffContextInterface;
use Retry\RetryContextInterface;

class MockBackOffStrategy implements BackOffPolicyInterface
{
    public $backOffCalls;
    public $initCalls;

    public function start(RetryContextInterface $context = null)
    {
        $this->initCalls++;
    }

    public function backOff(BackOffContextInterface $context = null)
    {
        $this->backOffCalls++;
    }
}
