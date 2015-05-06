<?php

namespace Retry\BackOff;

/**
 * Implementation of {@link BackOffPolicyInterface} that pauses for a random period of time before continuing.
 */
class UniformRandomBackOffPolicy extends StatelessBackOffPolicy
{
    /**
     * Default min back off period (500ms).
     *
     * @var int
     */
    const DEFAULT_BACK_OFF_MIN_PERIOD = 500;

    /**
     * Default max back off period (1500ms).
     *
     * @var int
     */
    const DEFAULT_BACK_OFF_MAX_PERIOD = 1500;

    /**
     * The minimum back off period in milliseconds.
     *
     * @var int
     */
    private $minBackOffPeriod;

    /**
     * The maximum back off period in milliseconds.
     *
     * @var int
     */
    private $maxBackOffPeriod;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**ExponentialRandomBackOffPolicyTest.php
     * @param int $minBackOffPeriod The minimum back off period in milliseconds.
     * @param int $maxBackOffPeriod The maximum back off period in milliseconds.
     */
    public function __construct($minBackOffPeriod = null, $maxBackOffPeriod = null)
    {
        if ($minBackOffPeriod === null) {
            $minBackOffPeriod = self::DEFAULT_BACK_OFF_MIN_PERIOD;
        }

        if ($maxBackOffPeriod === null) {
            $maxBackOffPeriod = self::DEFAULT_BACK_OFF_MAX_PERIOD;
        }

        $this->setMinBackOffPeriod($minBackOffPeriod);
        $this->setMaxBackOffPeriod($maxBackOffPeriod);

        $this->sleeper = new DefaultSleeper();
    }

    /**
     * Set the minimum back off period in milliseconds. Cannot be &lt; 1. Default value is 500ms.
     *
     * @param int $backOffPeriod
     */
    public function setMinBackOffPeriod($backOffPeriod)
    {
        $this->minBackOffPeriod = max(1, (int) $backOffPeriod);
    }

    /**
     * The minimum back off period in milliseconds.
     *
     * @return int
     */
    public function getMinBackOffPeriod()
    {
        return $this->minBackOffPeriod;
    }

    /**
     * Set the maximum back off period in milliseconds. Cannot be &lt; 1. Default value is 1500ms.
     *
     * @param int $backOffPeriod
     */
    public function setMaxBackOffPeriod($backOffPeriod)
    {
        $this->maxBackOffPeriod = max(1, (int) $backOffPeriod);
    }

    /**
     * The maximum back off period in milliseconds.
     *
     * @return int
     */
    public function getMaxBackOffPeriod()
    {
        return $this->maxBackOffPeriod;
    }

    /**
     * @param \Retry\BackOff\SleeperInterface $sleeper
     * @return void
     */
    public function setSleeper(SleeperInterface $sleeper)
    {
        $this->sleeper = $sleeper;
    }

    protected function doBackOff()
    {
        if ($this->maxBackOffPeriod == $this->minBackOffPeriod) {
            $period = 0;
        } else {
            $period = mt_rand(0, $this->maxBackOffPeriod - $this->minBackOffPeriod);
        }

        $this->sleeper->sleep($this->minBackOffPeriod + $period);
    }
}