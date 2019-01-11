<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\BackOff;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\BackOff\UniformRandomBackOffPolicy;
use Keboola\Retry\Test\BackOff\Fixtures\DummySleeper;

class UniformRandomBackOffPolicyTest extends TestCase
{
    /**
     * @var UniformRandomBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp(): void
    {
        $this->policy  = new UniformRandomBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSingleBackOff(): void
    {
        $min = $this->policy->getMinBackOffPeriod();
        $max = $this->policy->getMaxBackOffPeriod();

        $this->backOff();

        $this->assertEquals($this->randomize($min, $max), $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff(): void
    {
        $min = $this->policy->getMinBackOffPeriod();
        $max = $this->policy->getMaxBackOffPeriod();

        for ($x = 0; $x < 5; $x++) {
            $this->backOff();
            $this->assertEquals($this->randomize($min, $max), $this->sleeper->getLastBackOff());
        }
    }

    private function backOff(): void
    {
        mt_srand(1);
        $this->policy->backOff();
    }

    private function randomize(int $min, int $max): int
    {
        mt_srand(1);

        if ($min === $max) {
            return $min;
        }

        return $min + mt_rand(0, $max - $min);
    }
}
