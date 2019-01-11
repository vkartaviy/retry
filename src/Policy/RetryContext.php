<?php

declare(strict_types=1);

namespace Keboola\Retry\Policy;

use Keboola\Retry\RetryContextInterface;

class RetryContext implements RetryContextInterface
{
    /**
     * @var integer
     */
    private $retryCount = 0;

    /**
     * @var \Throwable
     */
    private $lastException;

    /**
     * @inheritdoc
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * @inheritdoc
     */
    public function registerException(\Throwable $exception): void
    {
        $this->lastException = $exception;
        $this->retryCount++;
    }

    /**
     * @inheritdoc
     */
    public function getLastException(): ?\Throwable
    {
        return $this->lastException;
    }
}
