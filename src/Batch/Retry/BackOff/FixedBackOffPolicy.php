<?php

namespace Batch\Retry\BackOff;

/**
 * Implementation of {@link BackOffPolicyInterface} that pauses for a fixed period of time before continuing.
 */
class FixedBackOffPolicy extends StatelessBackOffPolicy
{
    /**
     * Default back off period - 1000 ms.
     */
    const DEFAULT_BACK_OFF_PERIOD = 1000;

    /**
     * The back off period in milliseconds.
     */
    private $backOffPeriod;

    /**
     * @var SleeperInterface
     */
    private $sleeper;

    /**
     * @param int $backOffPeriod The back-off period in milliseconds. Cannot be &lt; 1. Default value is 1000 ms.
     */
    public function __construct($backOffPeriod = self::DEFAULT_BACK_OFF_PERIOD)
    {
        $this->backOffPeriod = max(1, (int) $backOffPeriod);
        $this->sleeper = new DefaultSleeper();
    }

    /**
     * The back-off period in milliseconds.
     *
     * @return int The back-off period
     */
    public function getBackOffPeriod()
    {
        return $this->backOffPeriod;
    }

    /**
     * Set the back off period in milliseconds. Cannot be &lt; 1. Default value is 1000 ms.
     *
     * @param int $backOffPeriod
     * @return void
     */
    public function setBackOffPeriod($backOffPeriod)
    {
        $this->backOffPeriod = max(1, (int) $backOffPeriod);
    }

    /**
     * Public setter for the {@link SleeperInterface} strategy.
     *
     * @param SleeperInterface $sleeper The sleeper to set. Defaults to {@link DefaultSleeper}.
     * @return void
     */
    public function setSleeper(SleeperInterface $sleeper)
    {
        $this->sleeper = $sleeper;
    }

    protected function doBackOff()
    {
        $this->sleeper->sleep($this->backOffPeriod);
    }
}
