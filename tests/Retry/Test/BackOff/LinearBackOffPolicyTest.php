<?php

namespace Retry\Test\Retry\BackOff;

use Retry\BackOff\LinearBackOffPolicy;
use Retry\Test\BackOff\Fixtures\DummySleeper;

class LinearBackOffPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinearBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp()
    {
        $this->policy  = new LinearBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    protected function tearDown()
    {
        $this->policy  = null;
        $this->sleeper = null;
    }

    public function testSingleBackOff()
    {
        $context = $this->policy->start();
        $this->policy->backOff($context);

        $this->assertEquals(LinearBackOffPolicy::DEFAULT_INITIAL_INTERVAL, $this->sleeper->getLastBackOff());
    }

    public function testMaximumBackOff()
    {
        $this->policy->setMaxInterval(50);

        $context = $this->policy->start();
        $this->policy->backOff($context);

        $this->assertEquals(50, $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff()
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

