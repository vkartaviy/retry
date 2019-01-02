<?php

declare(strict_types=1);

namespace Retry\Test\BackOff;

use PHPUnit\Framework\TestCase;
use Retry\BackOff\ExponentialBackOffPolicy;
use Retry\Test\BackOff\Fixtures\DummySleeper;

class ExponentialBackOffPolicyTest extends TestCase
{
    /**
     * @var ExponentialBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp(): void
    {
        $this->policy  = new ExponentialBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSingleBackOff(): void
    {
        $context = $this->policy->start();
        $this->policy->backOff($context);

        $this->assertEquals(ExponentialBackOffPolicy::DEFAULT_INITIAL_INTERVAL, $this->sleeper->getLastBackOff());
    }

    public function testMaximumBackOff(): void
    {
        $this->policy->setMaxInterval(50);

        $context = $this->policy->start();
        $this->policy->backOff($context);

        $this->assertEquals(50, $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff(): void
    {
        $seed       = 40;
        $multiplier = 1.2;

        $this->policy->setInitialInterval($seed);
        $this->policy->setMultiplier($multiplier);

        $context = $this->policy->start();

        for ($x = 0; $x < 5; $x++) {
            $this->policy->backOff($context);
            $this->assertEquals($seed, $this->sleeper->getLastBackOff());

            $seed *= $multiplier;
        }
    }
}
