<?php

namespace Retry\BackOff;

use Retry\RetryContextInterface;

abstract class AbstractBackOffPolicy implements BackOffPolicyInterface
{
    /**
     * @inheritdoc
     */
    public function start(RetryContextInterface $context = null)
    {
    }
}
