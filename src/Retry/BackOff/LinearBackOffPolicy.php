<?php

declare(strict_types=1);

namespace Retry\BackOff;

use Retry\RetryContextInterface;

/**
 * Implementation of {@link BackOffPolicyInterface} that linearly increases the back-off period for each retry attempt in a given set.
 */
class LinearBackOffPolicy extends AbstractBackOffPolicy
{
    /**
     * The default initial interval value - 100 ms.
     *
     * @var int
     */
    const DEFAULT_INITIAL_INTERVAL = 1000;

    /**
     * The default maximum back-off time (30 seconds).
     *
     * @var int
     */
    const DEFAULT_MAX_INTERVAL = 30000;

    /**
     * The default delta value (1 second).
     *
     * @var float
     */
    const DEFAULT_DELTA_INTERVAL = 1000;

    /**
     * The initial sleep interval.
     *
     * @var int
     */
    private $initialInterval;

    /**
     * The maximum value of the back-off period in milliseconds.
     *
     * @var int
     */
    private $maxInterval;

    /**
     * The value to linearly increment the seed with for each retry attempt.
     *
     * @var float
     */
    private $deltaInterval;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**
     * @param int $initialInterval The initial sleep interval value. Default is 100 ms.
     *                             Cannot be set to a value less than one.
     * @param float $deltaInterval The delta value. Default is 1000.
     * @param int $maxInterval     The maximum back off period. Default is 30000 (30 seconds).
     *                             The value will be reset to 1 if this method is called with a value less than 1.
     *                             Set this to avoid infinite waits if backing-off a large number of times.
     */
    public function __construct(?int $initialInterval = null, ?float $deltaInterval = null, ?int $maxInterval = null)
    {
        if ($initialInterval === null) {
            $initialInterval = self::DEFAULT_INITIAL_INTERVAL;
        }

        if ($deltaInterval === null) {
            $deltaInterval = self::DEFAULT_DELTA_INTERVAL;
        }

        if ($maxInterval === null) {
            $maxInterval = self::DEFAULT_MAX_INTERVAL;
        }

        $this->setInitialInterval($initialInterval);
        $this->setDeltaInterval($deltaInterval);
        $this->setMaxInterval($maxInterval);

        $this->sleeper = new DefaultSleeper();
    }

    /**
     * The initial period to sleep on the first back-off.
     *
     * @return int The initial interval
     */
    public function getInitialInterval(): int
    {
        return $this->initialInterval;
    }

    /**
     * Set the initial sleep interval value. Default is 1000 millisecond.
     * Cannot be set to a value less than one.
     *
     * @param int $initialInterval
     * @return void
     */
    public function setInitialInterval(int $initialInterval): void
    {
        $this->initialInterval = max(1, (int) $initialInterval);
    }

    /**
     * The delta to use to generate the next back-off interval from the last.
     *
     * @return int The delta in use
     */
    public function getDeltaInterval(): int
    {
        return $this->deltaInterval;
    }

    /**
     * Set the delta interval value. Default is 1000.
     *
     * @param float $delta
     * @return void
     */
    public function setDeltaInterval(float $delta): void
    {
        $this->deltaInterval = max(1, (int) $delta);
    }

    /**
     * The maximum interval to sleep for. Defaults to 30 seconds.
     *
     * @return int The maximum interval.
     */
    public function getMaxInterval(): int
    {
        return $this->maxInterval;
    }

    /**
     * Setter for maximum back-off period. Default is 30000 (30 seconds).
     * The value will be reset to 1 if this method is called with a value less than 1.
     * Set this to avoid infinite waits if backing off a large number of times.
     *
     * @param int $maxInterval
     * @return void
     */
    public function setMaxInterval(int $maxInterval): void
    {
        $this->maxInterval = max(1, (int) $maxInterval);
    }

    public function setSleeper(SleeperInterface $sleeper): void
    {
        $this->sleeper = $sleeper;
    }

    public function start(?RetryContextInterface $context = null)
    {
        return new LinearBackOffContext($this->initialInterval, $this->deltaInterval, $this->maxInterval);
    }

    /**
     * @param BackOffContextInterface|LinearBackOffContext $context
     */
    public function backOff(?BackOffContextInterface $context = null): void
    {
        if (!$context instanceof LinearBackOffContext) {
            throw new \InvalidArgumentException('Context is expected to be an instanceof LinearBackOffContext.');
        }

        $this->sleeper->sleep($context->getIntervalAndIncrement());
    }
}
