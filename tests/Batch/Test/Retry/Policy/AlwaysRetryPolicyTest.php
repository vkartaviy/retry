<?php

namespace Batch\Test\Retry\Policy;

use Batch\Retry\Policy\AlwaysRetryPolicy;
use Batch\Retry\RetryContext;

class AlwaysRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AlwaysRetryPolicy
     */
    private $policy;
    /**
     * @var RetryContext
     */
    private $context;

    protected function setUp()
    {
        $this->policy = new AlwaysRetryPolicy();
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
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($this->context));
    }

    public function testRetryCount()
    {
        $this->policy->registerException($this->context, new \RuntimeException('foo'));
        $this->assertEquals(1, $this->context->getRetryCount());
        $this->assertEquals('foo', $this->context->getLastException()->getMessage());
    }
}

