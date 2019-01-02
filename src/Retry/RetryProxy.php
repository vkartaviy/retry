<?php

declare(strict_types=1);

namespace Retry;

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
     * @param Policy\RetryPolicyInterface|null $retryPolicy
     * @param BackOff\BackOffPolicyInterface|null $backOffPolicy
     */
    public function __construct(RetryPolicyInterface $retryPolicy = null, BackOffPolicyInterface $backOffPolicy = null)
    {
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
     *
     * @param callable $action
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    public function call(callable $action, array $arguments = [])
    {
        $retryContext   = $this->retryPolicy->open();
        $backOffContext = $this->backOffPolicy->start($retryContext);

        while ($this->retryPolicy->canRetry($retryContext)) {
            try {
                return call_user_func_array($action, $arguments);
            } catch (\Exception $thrownException) {
                try {
                    $this->retryPolicy->registerException($retryContext, $thrownException);
                } catch (\Exception $policyException) {
                    throw new TerminatedRetryException('Terminated retry after error in policy.');
                }
            }

            if ($this->retryPolicy->canRetry($retryContext)) {
                $this->backOffPolicy->backOff($backOffContext);
            }
        };

        if ($lastException = $retryContext->getLastException()) {
            throw $lastException;
        }

        throw new RetryException('Action call is failed.');
    }
}
