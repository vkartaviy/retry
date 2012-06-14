<?php

namespace Batch\Test\Retry\Policy;

use Batch\Retry\Policy\NeverRetryPolicy;
use Batch\Retry\RetryContext;

class NeverRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NeverRetryPolicy
     */
    private $policy;
    /**
     * @var RetryContext
     */
    private $context;

    protected function setUp()
    {
        $this->policy = new NeverRetryPolicy();
        $this->context = new RetryContext();
        $this->policy->start($this->context);
    }

    protected function tearDown()
    {
        $this->policy = null;
        $this->context = null;
    }

    public function testSimpleOperations()
    {
        // We can retry until the first exception is registered...
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testRetryCount()
    {
        $this->policy->registerException($this->context, new \RuntimeException('foo'));
        $this->assertEquals(1, $this->context->getRetryCount());
        $this->assertEquals('foo', $this->context->getLastException()->getMessage());
    }
}

