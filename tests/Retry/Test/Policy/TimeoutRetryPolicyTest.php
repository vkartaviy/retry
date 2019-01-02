<?php

declare(strict_types=1);

namespace Retry\Test\Policy;

use PHPUnit\Framework\TestCase;
use Retry\Policy\TimeoutRetryPolicy;

class TimeoutRetryPolicyTest extends TestCase
{
    /**
     * @var TimeoutRetryPolicy
     */
    private $policy;

    protected function setUp(): void
    {
        $this->policy = new TimeoutRetryPolicy();
    }

    public function testTimeoutPreventsRetry(): void
    {
        $this->policy->setTimeout(100);

        $context = $this->policy->open();

        $this->policy->registerException($context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($context));
        usleep(50 * 1000);
        $this->assertTrue($this->policy->canRetry($context));
        usleep(50 * 1000);
        $this->assertFalse($this->policy->canRetry($context));
    }

    public function testRetryCount(): void
    {
        $context = $this->policy->open();

        $this->policy->registerException($context, new \RuntimeException('foo'));
        $this->assertEquals(1, $context->getRetryCount());
        $lastException = $context->getLastException();
        $this->assertNotNull($lastException);
        $this->assertEquals('foo', $lastException->getMessage());
    }
}
