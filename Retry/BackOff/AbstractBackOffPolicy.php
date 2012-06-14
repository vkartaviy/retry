<?php

namespace Batch\Retry\BackOff;

abstract class AbstractBackOffPolicy implements BackOffPolicyInterface
{
    public function start(BackOffContext $context)
    {
    }
}
