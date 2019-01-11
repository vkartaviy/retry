<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\Policy;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\Policy\NeverRetryPolicy;
use Keboola\Retry\RetryContextInterface;

class NeverRetryPolicyTest extends TestCase
{
    /**
     * @var NeverRetryPolicy
     */
    private $policy;

    /**
     * @var RetryContextInterface
     */
    private $context;

    protected function setUp(): void
    {
        $this->policy = new NeverRetryPolicy();
        $this->context = $this->policy->open();
    }

    public function testSimpleOperations(): void
    {
        // We can retry until the first exception is registered...
        $this->assertTrue($this->policy->canRetry($this->context));
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
