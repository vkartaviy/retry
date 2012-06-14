<?php

namespace Batch\Retry\BackOff;

/**
 * Simple base class for {@link BackOffPolicyInterface} implementations that maintain no
 * state across invocations.
 */
abstract class StatelessBackOffPolicy extends AbstractBackOffPolicy
{
    /**
     * Delegates directly to the {@link doBackOff()} method without passing on
     * the {@link BackOffContext} argument which is not needed for stateless
     * implementations.
     *
     * @param BackOffContext $context
     * @return void
     */
    public function backOff(BackOffContext $context)
    {
        $this->doBackOff();
    }

    protected abstract function doBackOff();
}
