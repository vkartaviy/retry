<?php

declare(strict_types=1);

namespace Retry\Policy;

use Retry\RetryContextInterface;

class NeverRetryContext extends RetryContext
{
    private $finished = false;

    public function isFinished()
    {
        return $this->finished;
    }

    public function setFinished()
    {
        $this->finished = true;
    }

    /**
     * @param RetryContextInterface $context
     * @return NeverRetryContext
     */
    public static function cast(RetryContextInterface $context)
    {
        if (!$context instanceof NeverRetryContext) {
            throw new \InvalidArgumentException('Context is expected to be an instanceof NeverRetryContext.');
        }

        return $context;
    }
}
