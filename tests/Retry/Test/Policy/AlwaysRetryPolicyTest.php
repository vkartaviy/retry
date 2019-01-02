<?php

declare(strict_types=1);

namespace Retry\Test\Policy;

use PHPUnit\Framework\TestCase;
use Retry\Policy\AlwaysRetryPolicy;
use Retry\RetryContextInterface;

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
        $message = $this->context->getLastException() ? $this->context->getLastException()->getMessage() : '';
        $this->assertEquals('foo', $message);
    }
}
