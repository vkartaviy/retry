<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\BackOff;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\BackOff\FixedBackOffPolicy;
use Keboola\Retry\Test\BackOff\Fixtures\DummySleeper;

class FixedBackOffPolicyTest extends TestCase
{
    /**
     * @var FixedBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp(): void
    {
        $this->policy  = new FixedBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSetBackOffPeriodNegative(): void
    {
        $this->policy->setBackOffPeriod(-1000);
        $this->policy->backOff();

        $this->assertEquals(1, count($this->sleeper->getBackOffs()));
        $this->assertEquals(1, $this->sleeper->getLastBackOff());
    }

    public function testSingleBackOff(): void
    {
        $this->policy->setBackOffPeriod(50);
        $this->policy->backOff();

        $this->assertEquals(1, count($this->sleeper->getBackOffs()));
        $this->assertEquals(50, $this->sleeper->getLastBackOff());
    }

    public function testManyBackOffCalls(): void
    {
        $this->policy->setBackOffPeriod(50);
        $this->policy->start();

        for ($x = 0; $x < 10; $x++) {
            $this->policy->backOff();
            $this->assertEquals(50, $this->sleeper->getLastBackOff());
        }

        $this->assertEquals(10, count($this->sleeper->getBackOffs()));
    }
}
