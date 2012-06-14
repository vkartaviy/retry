<?php

namespace Batch\Retry;

use Batch\Retry\Policy\RetryPolicyInterface;
use Batch\Retry\Policy\SimpleRetryPolicy;
use Batch\Retry\BackOff\BackOffPolicyInterface;
use Batch\Retry\BackOff\BackOffContext;
use Batch\Retry\BackOff\ExponentialBackOffPolicy;

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
        if (!$retryPolicy) {
            $retryPolicy = new SimpleRetryPolicy(3);
        }

        if (!$backOffPolicy) {
            $backOffPolicy = new ExponentialBackOffPolicy();
        }

        $this->retryPolicy = $retryPolicy;
        $this->backOffPolicy = $backOffPolicy;
    }

    /**
     * Executing the action until it either succeeds or the policy dictates that we stop,
     * in which case the most recent exception thrown by the action will be rethrown.
     *
     * @param callback $action
     * @param array    $arguments
     * @throws TerminatedRetryException
     * @throws \InvalidArgumentException When action is not callable
     * @throws RetryException
     * @return mixed
     */
    public function call($action, array $arguments = array())
    {
        if (!is_callable($action)) {
            throw new \InvalidArgumentException(sprintf('Action is expected to be a valid callback, %s was given.', gettype($action)));
        }

        $context = new RetryContext();
        $backOffContext = new BackOffContext();

        $this->retryPolicy->start($context);
        $this->backOffPolicy->start($backOffContext);

        while ($this->retryPolicy->canRetry($context)) {
            try {
                return call_user_func_array($action, $arguments);
            } catch (\Exception $exception) {
                try {
                    $this->retryPolicy->registerException($context, $exception);
                } catch (\Exception $e) {
                    throw new TerminatedRetryException('Terminated retry after error in policy');
                }
            }

            if ($this->retryPolicy->canRetry($context)) {
                $this->backOffPolicy->backOff($backOffContext);
            }
        };

        if ($lastException = $context->getLastException()) {
            throw $lastException;
        }

        throw new RetryException('Action call is failed.');
    }
}
