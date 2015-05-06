<?php

namespace Retry\Policy;

use Retry\RetryContextInterface;

class RetryContext implements RetryContextInterface
{
    /**
     * @var integer
     */
    private $retryCount = 0;

    /**
     * @var \Exception
     */
    private $lastException;

    /**
     * @inheritdoc
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * @inheritdoc
     */
    public function registerException(\Exception $exception)
    {
        $this->lastException = $exception;
        $this->retryCount++;
    }

    /**
     * @inheritdoc
     */
    public function getLastException()
    {
        return $this->lastException;
    }
}
