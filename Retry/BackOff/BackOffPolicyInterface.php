<?php

namespace Batch\Retry\BackOff;

interface BackOffPolicyInterface
{
    /**
     * @param BackOffContext $context
     * @return void
     */
    function start(BackOffContext $context);

    /**
     * Back-off/pause in an implementation-specific fashion.
     *
     * @param BackOffContext $context
     * @return void
     */
    function backOff(BackOffContext $context);
}
