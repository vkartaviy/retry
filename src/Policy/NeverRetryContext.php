<?php

declare(strict_types=1);

namespace Keboola\Retry\Policy;

use Keboola\Retry\RetryContextInterface;

class NeverRetryContext extends RetryContext
{
    /** @var bool  */
    private $finished = false;

    public function isFinished(): bool
    {
        return $this->finished;
    }

    public function setFinished(): void
    {
        $this->finished = true;
    }

    public static function cast(RetryContextInterface $context): NeverRetryContext
    {
        if (!$context instanceof NeverRetryContext) {
            throw new \InvalidArgumentException('Context is expected to be an instanceof NeverRetryContext.');
        }

        return $context;
    }
}
