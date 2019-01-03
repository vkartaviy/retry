<?php

declare(strict_types=1);

namespace Retry;

use Monolog\Logger;
use Retry\Policy\RetryPolicyInterface;
use Retry\Policy\SimpleRetryPolicy;
use Retry\BackOff\BackOffPolicyInterface;
use Retry\BackOff\ExponentialBackOffPolicy;

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
     * @var Logger
     */
    private $logger;

    public function __construct(
        ?RetryPolicyInterface $retryPolicy = null,
        ?BackOffPolicyInterface $backOffPolicy = null
    ) {
        if ($retryPolicy === null) {
            $retryPolicy = new SimpleRetryPolicy();
        }

        if ($backOffPolicy === null) {
            $backOffPolicy = new ExponentialBackOffPolicy();
        }

        $this->retryPolicy   = $retryPolicy;
        $this->backOffPolicy = $backOffPolicy;
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

    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }
}
