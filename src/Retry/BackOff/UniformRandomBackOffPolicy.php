<?php

declare(strict_types=1);

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
    public function __construct(?int $minBackOffPeriod = null, ?int $maxBackOffPeriod = null)
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
    public function setMinBackOffPeriod(int $backOffPeriod): void
    {
        $this->minBackOffPeriod = max(1, (int) $backOffPeriod);
    }

    /**
     * The minimum back off period in milliseconds.
     *
     * @return int
     */
    public function getMinBackOffPeriod(): int
    {
        return $this->minBackOffPeriod;
    }

    /**
     * Set the maximum back off period in milliseconds. Cannot be &lt; 1. Default value is 1500ms.
     *
     * @param int $backOffPeriod
     */
    public function setMaxBackOffPeriod(int $backOffPeriod): void
    {
        $this->maxBackOffPeriod = max(1, (int) $backOffPeriod);
    }

    /**
     * The maximum back off period in milliseconds.
     *
     * @return int
     */
    public function getMaxBackOffPeriod(): int
    {
        return $this->maxBackOffPeriod;
    }

    public function setSleeper(SleeperInterface $sleeper): void
    {
        $this->sleeper = $sleeper;
    }

    protected function doBackOff(): void
    {
        if ($this->maxBackOffPeriod === $this->minBackOffPeriod) {
            $period = 0;
        } else {
            $period = mt_rand(0, $this->maxBackOffPeriod - $this->minBackOffPeriod);
        }

        $this->sleeper->sleep($this->minBackOffPeriod + $period);
    }
}
