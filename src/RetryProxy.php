<?php

declare(strict_types=1);

namespace Retry;

use Psr\Log\NullLogger;
use Retry\Policy\RetryPolicyInterface;
use Retry\Policy\SimpleRetryPolicy;
use Retry\BackOff\BackOffPolicyInterface;
use Retry\BackOff\ExponentialBackOffPolicy;
use PSR\Log\LoggerInterface;

class RetryProxy implements RetryProxyInterface
{
    /**
     * @var RetryPolicyInterface
     */
    private $retryPolicy;

    /**
     * @var BackOffPolicyInterface
     */
    private $backOffPolicy;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ?RetryPolicyInterface $retryPolicy = null,
        ?BackOffPolicyInterface $backOffPolicy = null,
        ?LoggerInterface $logger = null
    ) {
        if ($retryPolicy === null) {
            $retryPolicy = new SimpleRetryPolicy();
        }

        if ($backOffPolicy === null) {
            $backOffPolicy = new ExponentialBackOffPolicy();
        }

        if ($logger === null) {
            $logger = new NullLogger();
        }

        $this->retryPolicy   = $retryPolicy;
        $this->backOffPolicy = $backOffPolicy;
        $this->logger = $logger;
    }

    /**
     * Executing the action until it either succeeds or the policy dictates that we stop,
     * in which case the most recent exception thrown by the action will be rethrown.
     */
    public function call(callable $action, array $arguments = []): ?mixed
    {
        $retryContext   = $this->retryPolicy->open();
        $backOffContext = $this->backOffPolicy->start($retryContext);

        while ($this->retryPolicy->canRetry($retryContext)) {
            try {
                return call_user_func_array($action, $arguments);
            } catch (\Throwable $thrownException) {
                try {
                    $this->retryPolicy->registerException($retryContext, $thrownException);
                } catch (\Throwable $policyException) {
                    throw new TerminatedRetryException('Terminated retry after error in policy.');
                }
            }

            if ($this->retryPolicy->canRetry($retryContext)) {
                if (isset($this->logger)) {
                    $this->logger->info(
                        sprintf(
                            '%s. Retrying... [%dx]',
                            $thrownException->getMessage(),
                            $retryContext->getRetryCount()
                        )
                    );
                }
                $this->backOffPolicy->backOff($backOffContext);
            }
        };

        $lastException = $retryContext->getLastException();
        if ($lastException) {
            throw $lastException;
        }

        throw new RetryException('Action call is failed.');
    }
}
