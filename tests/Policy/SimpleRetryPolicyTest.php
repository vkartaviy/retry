<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\Policy;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\Policy\SimpleRetryPolicy;
use Keboola\Retry\RetryContextInterface;

class SimpleRetryPolicyTest extends TestCase
{
    /**
     * @var SimpleRetryPolicy
     */
    private $policy;

    /**
     * @var RetryContextInterface
     */
    private $context;

    protected function setUp(): void
    {
        $this->policy = new SimpleRetryPolicy();
        $this->context = $this->policy->open();
    }

    public function testCanRetryIfNoException(): void
    {
        $this->assertTrue($this->policy->canRetry($this->context));
    }

    public function testEmptyExceptionsNeverRetry(): void
    {
        $this->policy->setRetryableExceptions(array());
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertFalse($this->policy->canRetry($this->context));
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

    public function testRetryCount(): void
    {
        $this->policy->registerException($this->context, new \RuntimeException('foo'));
        $this->assertEquals(1, $this->context->getRetryCount());
        $this->assertEquals('foo', $this->context->getLastException()->getMessage());
    }
}
