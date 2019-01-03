<?php

declare(strict_types=1);

namespace Retry\BackOff;

use Retry\RetryContextInterface;

/**
 * Implementation of {@link BackOffPolicyInterface}
 * that exponentially increases the back-off period for each retry attempt in a given set.
 */
class ExponentialBackOffPolicy extends AbstractBackOffPolicy
{
    /**
     * The default initial interval value - 100 ms.
     * Coupled with the default multiplier value this gives a useful initial spread of pauses for 1-5 retries.
     *
     * @var int
     */
    public const DEFAULT_INITIAL_INTERVAL = 100;

    /**
     * The default maximum back-off time (30 seconds).
     *
     * @var int
     */
    public const DEFAULT_MAX_INTERVAL = 30000;

    /**
     * The default multiplier value - 2 (100% increase per back-off).
     *
     * @var float
     */
    public const DEFAULT_MULTIPLIER = 2;

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
     * The value to increment the exp seed with for each retry attempt.
     *
     * @var float
     */
    private $multiplier;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**
     * @param int $initialInterval The initial sleep interval value. Default is 100 ms.
     *                             Cannot be set to a value less than one.
     * @param float $multiplier    The multiplier value. Default is 2.
     *                             Hint: Do not use values much in excess of 1.0
     *                             (or the back-off will get very long very fast).
     * @param int $maxInterval     The maximum back off period. Default is 30000 (30 seconds).
     *                             The value will be reset to 1 if this method is called with a value less than 1.
     *                             Set this to avoid infinite waits if backing-off a large number of times
     *                             (or if the multiplier is set too high).
     */
    public function __construct(?int $initialInterval = null, ?float $multiplier = null, ?int $maxInterval = null)
    {
        if ($initialInterval === null) {
            $initialInterval = self::DEFAULT_INITIAL_INTERVAL;
        }

        if ($multiplier === null) {
            $multiplier = self::DEFAULT_MULTIPLIER;
        }

        if ($maxInterval === null) {
            $maxInterval = self::DEFAULT_MAX_INTERVAL;
        }

        $this->setInitialInterval($initialInterval);
        $this->setMultiplier($multiplier);
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
     * Set the initial sleep interval value. Default is 100 millisecond.
     * Cannot be set to a value less than one.
     *
     * @param int $initialInterval
     * @return void
     */
    public function setInitialInterval(int $initialInterval): void
    {
        $this->initialInterval = max(1, $initialInterval);
    }

    /**
     * The multiplier to use to generate the next back-off interval from the last.
     *
     * @return float The multiplier in use
     */
    public function getMultiplier(): float
    {
        return $this->multiplier;
    }

    /**
     * Set the multiplier value. Default is 2.0.
     *
     * Hint: do not use values much in excess of 1.0 (or the back-off will get very long very fast).
     *
     * @param float $multiplier
     * @return void
     */
    public function setMultiplier(float $multiplier): void
    {
        $this->multiplier = max(1, $multiplier);
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
     * Set this to avoid infinite waits if backing off a large number of times (or if the multiplier is set too high).
     *
     * @param int $maxInterval
     * @return void
     */
    public function setMaxInterval(int $maxInterval): void
    {
        $this->maxInterval = max(1, $maxInterval);
    }

    public function setSleeper(SleeperInterface $sleeper): void
    {
        $this->sleeper = $sleeper;
    }

    public function start(?RetryContextInterface $context = null): BackOffContextInterface
    {
        return new ExponentialBackOffContext($this->initialInterval, $this->multiplier, $this->maxInterval);
    }

    /**
     * @param BackOffContextInterface|ExponentialBackOffContext $context
     */
    public function backOff(?BackOffContextInterface $context = null): void
    {
        if (!$context instanceof ExponentialBackOffContext) {
            throw new \InvalidArgumentException('Context is expected to be an instanceof ExponentialBackOffContext.');
        }

        $this->sleeper->sleep($context->getIntervalAndIncrement());
    }
}
