<?php

namespace Retry\BackOff;

use Retry\RetryContextInterface;

/**
 * Implementation of {@link ExponentialBackOffPolicy} that chooses a random multiple of the interval.
 * The random multiple is selected based on how many iterations have occurred.
 *
 * Example:
 *   initialInterval = 50
 *   multiplier      = 2.0
 *   maxInterval     = 3000
 *
 * {@link ExponentialBackOffPolicy} yields:           [50, 100, 200, 400, 800]
 *
 * {@link ExponentialRandomBackOffPolicy} may yield   [50, 100, 100, 100, 600]
 *                                               or   [50, 100, 150, 400, 800]
 */
class ExponentialRandomBackOffPolicy extends ExponentialBackOffPolicy
{
    public function start(RetryContextInterface $context = null)
    {
        return new ExponentialRandomBackOffContext($this->getInitialInterval(), $this->getMultiplier(), $this->getMaxInterval());
    }
}
