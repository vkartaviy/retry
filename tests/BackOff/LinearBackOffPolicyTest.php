<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\BackOff;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\BackOff\LinearBackOffPolicy;
use Keboola\Retry\Test\BackOff\Fixtures\DummySleeper;

class LinearBackOffPolicyTest extends TestCase
{
    /**
     * @var LinearBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp(): void
    {
        $this->policy  = new LinearBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSingleBackOff(): void
    {
        $context = $this->policy->start();
        $this->policy->backOff($context);

        $this->assertEquals(LinearBackOffPolicy::DEFAULT_INITIAL_INTERVAL, $this->sleeper->getLastBackOff());
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
        $seed  = 100;
        $delta = 10;

        $this->policy->setInitialInterval($seed);
        $this->policy->setDeltaInterval($delta);

        $context = $this->policy->start();

        for ($x = 0; $x < 5; $x++) {
            $this->policy->backOff($context);
            $this->assertEquals($seed, $this->sleeper->getLastBackOff());

            $seed += $delta;
        }
    }
}
