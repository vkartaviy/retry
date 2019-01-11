<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\BackOff;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\BackOff\BackOffContextInterface;
use Keboola\Retry\BackOff\ExponentialRandomBackOffPolicy;
use Keboola\Retry\Test\BackOff\Fixtures\DummySleeper;

class ExponentialRandomBackOffPolicyTest extends TestCase
{
    /**
     * @var ExponentialRandomBackOffPolicy
     */
    private $policy;

    /**
     * @var DummySleeper
     */
    private $sleeper;

    protected function setUp(): void
    {
        $this->policy  = new ExponentialRandomBackOffPolicy();
        $this->sleeper = new DummySleeper();
        $this->policy->setSleeper($this->sleeper);
    }

    public function testSingleBackOff(): void
    {
        $seed = $this->policy->getInitialInterval();
        $multiplier = $this->policy->getMultiplier();

        $context = $this->policy->start();
        $this->backOff($context);

        $this->assertEquals($this->randomize($seed, $multiplier), $this->sleeper->getLastBackOff());
    }

    public function testMaximumBackOff(): void
    {
        $maxInterval = 50;
        $multiplier = $this->policy->getMultiplier();

        $this->policy->setMaxInterval($maxInterval);

        $context = $this->policy->start();
        $this->backOff($context);

        $this->assertEquals($this->randomize($maxInterval, $multiplier), $this->sleeper->getLastBackOff());
    }

    public function testMultiBackOff(): void
    {
        $seed       = 40;
        $multiplier = 1.2;

        $this->policy->setInitialInterval($seed);
        $this->policy->setMultiplier($multiplier);

        $context = $this->policy->start();

        for ($x = 0; $x < 5; $x++) {
            $this->backOff($context);
            $this->assertEquals($this->randomize($seed, $multiplier), $this->sleeper->getLastBackOff());

            $seed = (int) ($seed * $multiplier);
        }
    }

    private function backOff(BackOffContextInterface $context): void
    {
        mt_srand(1);
        $this->policy->backOff($context);
    }

    private function randomize(int $seed, float $multiplier): int
    {
        mt_srand(1);
        $random = mt_rand(0, mt_getrandmax()) / mt_getrandmax();

        return (int) ($seed * (1 + $random * ($multiplier - 1)));
    }
}
