<?php

namespace Batch\Test\Retry\Fixtures;

use Batch\Retry\BackOff\BackOffPolicyInterface;
use Batch\Retry\BackOff\BackOffContext;

class MockBackOffStrategy implements BackOffPolicyInterface
{
    public $backOffCalls;
    public $initCalls;

    public function start(BackOffContext $context)
    {
        $this->initCalls++;
    }

    public function backOff(BackOffContext $context)
    {
        $this->backOffCalls++;
    }
}
