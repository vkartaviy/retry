<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\Policy;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\Policy\CallableRetryPolicy;
use Keboola\Retry\RetryContextInterface;

class CallableRetryPolicyTest extends TestCase
{
    /**
     * @var CallableRetryPolicy
     */
    private $policy;

    /**
     * @var RetryContextInterface
     */
    private $context;

    protected function setUp(): void
    {
        $this->policy = new CallableRetryPolicy();
        $this->context = $this->policy->open();
    }

    public function testCanRetryIfNoException(): void
    {
        $this->assertTrue($this->policy->canRetry($this->context));
    }

    public function testDefaultAlwaysRetry(): void
    {
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($this->context));
    }

    public function testRetryLimitInitialState(): void
    {
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->setMaxAttempts(0);
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testRetryLimitSubsequentState(): void
    {
        $this->policy->setMaxAttempts(2);
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testSimpleShouldRetryCallback(): void
    {
        $this->policy->setShouldRetryMethod(function (\Throwable $e) {
            if ($e instanceof \RuntimeException) {
                return true;
            }
            return false;
        });

        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \InvalidArgumentException());
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testSettingMethodInConstructor(): void
    {
        $policy = new CallableRetryPolicy(function (\Throwable $e) {
            if ($e instanceof \RuntimeException) {
                return true;
            }
            return false;
        });
        $context = $policy->open();

        $policy->registerException($context, new \RuntimeException());
        $this->assertTrue($policy->canRetry($context));
        $policy->registerException($context, new \InvalidArgumentException());
        $this->assertFalse($policy->canRetry($context));
    }

    public function testRetryCount(): void
    {
        $this->policy->registerException($this->context, new \RuntimeException('foo'));
        $this->assertEquals(1, $this->context->getRetryCount());
        $this->assertEquals('foo', $this->context->getLastException()->getMessage());
    }
}
