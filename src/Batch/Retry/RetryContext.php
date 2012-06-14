<?php

namespace Batch\Retry;

class RetryContext extends \ArrayObject
{
    public function __construct()
    {
        parent::__construct(array(
            'retry_count' => 0,
            'last_exception' => null
        ));
    }

    public function getRetryCount()
    {
        return $this['retry_count'];
    }

    /**
     * Set the exception for the public interface and increment retries counter.<br/>
     *
     * All {@link RetryPolicyInterface} implementations should use this method when they
     * register the exception. It should only be called once per retry attempt
     * because it increments a counter.<br/>
     *
     * Use of this method is not enforced by the framework - it is a service
     * provider contract for authors of policies.
     *
     * @param \Exception $exception The exception that caused the current retry attempt to fail.
     * @return void
     */
    public function registerException(\Exception $exception)
    {
        $this['last_exception'] = $exception;
        $this['retry_count']++;
    }

    public function getLastException()
    {
        return $this['last_exception'];
    }

    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this) . ' ' . json_encode($this);
    }
}
