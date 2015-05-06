<?php

namespace Retry\Test\Retry\BackOff;

use Retry\BackOff\BackOffContextInterface;
use Retry\BackOff\ExponentialRandomBackOffPolicy;
use Retry\Test\BackOff\Fixtures\DummySleeper;

class ExponentialRandomBackOffPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExponentialRandomBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp()
    {
        $this->policy  = new ExponentialRandomBackOffPolicy();
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
        $seed = $this->policy->getInitialInterval();
        $multiplier = $this->policy->getMultiplier();

        $context = $this->policy->start();
        $this->backOff($context);

        $this->assertEquals($this->randomize($seed, $multiplier), $this->sleeper->getLastBackOff());
    }

    public function testMaximumBackOff()
    {
        $maxInterval = 50;
        $multiplier = $this->policy->getMultiplier();

        $this->policy->setMaxInterval($maxInterval);

        $context = $this->policy->start();
        $this->backOff($context);

        $this->assertEquals($this->randomize($maxInterval, $multiplier), $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff()
    {
        $seed       = 40;
        $multiplier = 1.2;

        $this->policy->setInitialInterval($seed);
        $this->policy->setMultiplier($multiplier);

        $context = $this->policy->start();

        for ($x = 0; $x < 5; $x++) {
            $this->backOff($context);
            $this->assertEquals($this->randomize($seed, $multiplier), $this->sleeper->getLastBackOff());

            $seed *= $multiplier;
        }
    }

    private function backOff(BackOffContextInterface $context)
    {
        mt_srand(1);
        $this->policy->backOff($context);
    }

    private function randomize($seed, $multiplier)
    {
        mt_srand(1);
        $random = mt_rand(0, mt_getrandmax()) / mt_getrandmax();

        return $seed * (1 + $random * ($multiplier - 1));
    }
}

