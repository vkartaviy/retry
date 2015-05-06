<?php

namespace Retry\Test\Policy;

use Retry\Policy\SimpleRetryPolicy;
use Retry\RetryContextInterface;

class SimpleRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleRetryPolicy
     */
    private $policy;
    /**
     * @var RetryContextInterface
     */
    private $context;

    protected function setUp()
    {
        $this->policy = new SimpleRetryPolicy();
        $this->context = $this->policy->open();
    }

    protected function tearDown()
    {
        $this->policy = null;
        $this->context = null;
    }

    public function testCanRetryIfNoException()
    {
        $this->assertTrue($this->policy->canRetry($this->context));
    }

    public function testEmptyExceptionsNeverRetry()
    {
        $this->policy->setRetryableExceptions(array());
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testRetryLimitInitialState()
    {
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->setMaxAttempts(0);
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testRetryLimitSubsequentState()
    {
        $this->policy->setMaxAttempts(2);
        $this->assertTrue($this->policy->canRetry($this->context));
        $this->policy->registerException($this->context, new \RuntimeException());
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

