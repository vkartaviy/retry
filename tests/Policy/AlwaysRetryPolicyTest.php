<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\Policy;

use PHPUnit\Framework\TestCase;
use Keboola\Retry\Policy\AlwaysRetryPolicy;
use Keboola\Retry\RetryContextInterface;

class AlwaysRetryPolicyTest extends TestCase
{
    /**
     * @var AlwaysRetryPolicy
     */
    private $policy;

    /**
     * @var RetryContextInterface
     */
    private $context;

    protected function setUp(): void
    {
        $this->policy = new AlwaysRetryPolicy();
        $this->context = $this->policy->open();
    }

    public function testSimpleOperations(): void
    {
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($this->context));
    }

    public function testRetryCount(): void
    {
        $this->policy->registerException($this->context, new \RuntimeException('foo'));
        $this->assertEquals(1, $this->context->getRetryCount());
        $this->assertEquals('foo', $this->context->getLastException()->getMessage());
    }
}
