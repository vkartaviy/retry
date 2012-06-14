<?php

namespace Batch\Retry\BackOff;

/**
 * Implementation of {@link BackOffPolicyInterface} that increases the back-off period
 * for each retry attempt in a given set.
 */
class ExponentialBackOffPolicy extends AbstractBackOffPolicy
{
    /**
     * The default initial interval value - 100 ms. Coupled with the
     * default multiplier value this gives a useful initial spread of pauses
     * for 1-5 retries.
     *
     * @var int
     */
    const DEFAULT_INITIAL_INTERVAL = 100;

    /**
     * The default maximum back-off time (30 seconds).
     *
     * @var int
     */
    const DEFAULT_MAX_INTERVAL = 30000;

    /**
     * The default multiplier value - 2 (100% increase per back-off).
     *
     * @var float
     */
    const DEFAULT_MULTIPLIER = 2.0;

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
     *                             Hint: Do not use values much in excess of 1.0 (or the back-off will get very long very fast).
     * @param int $maxInterval     The maximum back off period. Default is 30000 (30 seconds).
     *                             The value will be reset to 1 if this method is called with a value less than 1.
     *                             Set this to avoid infinite waits if backing-off a large number of times (or if the multiplier is set too high).
     */
    public function __construct($initialInterval = self::DEFAULT_INITIAL_INTERVAL, $multiplier = self::DEFAULT_MULTIPLIER, $maxInterval = self::DEFAULT_MAX_INTERVAL)
    {
        $this->initialInterval = max(1, (int) $initialInterval);
        $this->multiplier = max(1.0, (float) $multiplier);
        $this->maxInterval = max(1, (int) $maxInterval);
        $this->sleeper = new DefaultSleeper();
    }

    /**
     * The initial period to sleep on the first back-off.
     *
     * @return int The initial interval
     */
    public function getInitialInterval()
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
    public function setInitialInterval($initialInterval)
    {
        $this->initialInterval = max(1, (int) $initialInterval);
    }

    /**
     * The multiplier to use to generate the next back-off interval from the
     * last.
     *
     * @return float The multiplier in use
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * Set the multiplier value. Default is 2.0. Hint: do not use
     * values much in excess of 1.0 (or the back-off will get very long very
     * fast).
     *
     * @param float $multiplier
     * @return void
     */
    public function setMultiplier($multiplier)
    {
        $this->multiplier = max(1, (float) $multiplier);
    }

    /**
     * The maximum interval to sleep for. Defaults to 30 seconds.
     *
     * @return int The maximum interval.
     */
    public function getMaxInterval()
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
    public function setMaxInterval($maxInterval)
    {
        $this->maxInterval = max(1, (int) $maxInterval);
    }

    /**
     * @param \Batch\Retry\BackOff\SleeperInterface $sleeper
     * @return void
     */
    public function setSleeper(SleeperInterface $sleeper)
    {
        $this->sleeper = $sleeper;
    }

    public function start(BackOffContext $context)
    {
        $context['interval'] = $this->initialInterval;
    }

    public function backOff(BackOffContext $context)
    {
        $period = $context['interval'];

        if ($period >= $this->maxInterval) {
            $period = $this->maxInterval;
        } else {
            $context['interval'] *= $this->multiplier;
        }

        $this->sleeper->sleep($period);
    }
}
