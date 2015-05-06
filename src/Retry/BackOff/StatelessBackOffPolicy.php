<?php

namespace Retry\BackOff;

/**
 * Simple base class for {@link BackOffPolicyInterface} implementations that maintain no state across invocations.
 */
abstract class StatelessBackOffPolicy extends AbstractBackOffPolicy
{
    /**
     * Delegates directly to the {@link doBackOff()} method without passing on the {@link BackOffContext} argument
     * which is not needed for stateless implementations.
     *
     * @param BackOffContextInterface $context
     */
    public function backOff(BackOffContextInterface $context = null)
    {
        $this->doBackOff();
    }

    protected abstract function doBackOff();
}
