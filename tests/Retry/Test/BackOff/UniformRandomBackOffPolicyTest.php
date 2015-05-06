<?php

namespace Retry\Test\Retry\BackOff;

use Retry\BackOff\UniformRandomBackOffPolicy;
use Retry\Test\BackOff\Fixtures\DummySleeper;

class UniformRandomBackOffPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UniformRandomBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp()
    {
        $this->policy  = new UniformRandomBackOffPolicy();
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
        $min = $this->policy->getMinBackOffPeriod();
        $max = $this->policy->getMaxBackOffPeriod();

        $this->backOff();

        $this->assertEquals($this->randomize($min, $max), $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff()
    {
        $min = $this->policy->getMinBackOffPeriod();
        $max = $this->policy->getMaxBackOffPeriod();

        for ($x = 0; $x < 5; $x++) {
            $this->backOff();
            $this->assertEquals($this->randomize($min, $max), $this->sleeper->getLastBackOff());
        }
    }

    private function backOff()
    {
        mt_srand(1);
        $this->policy->backOff();
    }

    private function randomize($min, $max)
    {
        mt_srand(1);

        if ($min == $max) {
            return $min;
        }

        return $min + mt_rand(0, $max - $min);
    }
}

