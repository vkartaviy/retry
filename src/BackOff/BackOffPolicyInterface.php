<?php

declare(strict_types=1);

namespace Keboola\Retry\BackOff;

use Keboola\Retry\RetryContextInterface;

/**
 * Strategy interface to control back off between attempts in a single retry operation.
 *
 * For each block of retry operations the {@link start} method is called and implementations can return
 * an implementation-specific {@link BackOffContextInterface} that can be used to track state through subsequent
 * back off invocations.
 *
 * Each back off process is handled via a call to {@link backOff} method.
 * The {@link RetryProxy} will pass in the corresponding {@link BackOffContextInterface} object created by the call to
 * {@link start}.
 */
interface BackOffPolicyInterface
{
    /**
     * Start a new block of back off operations. Implementations can choose to
     * pause when this method is called, but normally it returns immediately.
     *
     * @param RetryContextInterface $context The current retry context, which might contain information
     *                                       that we can use to decide how to proceed.
     * @return null|BackOffContextInterface
     */
    public function start(?RetryContextInterface $context = null): ?BackOffContextInterface;

    /**
     * Back-off/pause in an implementation-specific fashion. The passed in
     * {@link BackOffContextInterface} corresponds to the one created by the call to
     * {@link start} method for a given retry operation set.
     *
     * @param BackOffContextInterface $context
     * @return void
     */
    public function backOff(?BackOffContextInterface $context = null): void;
}
