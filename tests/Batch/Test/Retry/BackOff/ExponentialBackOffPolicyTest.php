<?php

namespace Batch\Test\Retry\Retry\BackOff;

use Batch\Retry\BackOff\ExponentialBackOffPolicy;
use Batch\Retry\BackOff\BackOffContext;
use Batch\Test\Retry\BackOff\Fixtures\DummySleeper;

class ExponentialBackOffPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExponentialBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    /**
     * @var BackOffContext
     */
    private $context;

    protected function setUp()
    {
        $this->policy = new ExponentialBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
        $this->context = new BackOffContext();
    }

    protected function tearDown()
    {
        $this->policy = null;
        $this->sleeper = null;
        $this->context = null;
    }

    public function testSingleBackOff()
    {
        $this->policy->start($this->context);
        $this->policy->backOff($this->context);

        $this->assertEquals(ExponentialBackOffPolicy::DEFAULT_INITIAL_INTERVAL, $this->sleeper->getLastBackOff());
    }

    public function testMaximumBackOff()
    {
        $this->policy->setMaxInterval(50);

        $this->policy->start($this->context);
        $this->policy->backOff($this->context);

        $this->assertEquals(50, $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff()
    {
        $seed = 40;
        $multiplier = 1.2;
        $this->policy->setInitialInterval($seed);
        $this->policy->setMultiplier($multiplier);

        $this->policy->start($this->context);

        for ($x = 0; $x < 5; $x++) {
            $this->policy->backOff($this->context);
            $this->assertEquals($seed, $this->sleeper->getLastBackOff());
            $seed *= $multiplier;
        }
    }
}

