<?php

namespace Retry\Test\Policy;

use Retry\Policy\AlwaysRetryPolicy;
use Retry\RetryContextInterface;

class AlwaysRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AlwaysRetryPolicy
     */
    private $policy;
    /**
     * @var RetryContextInterface
     */
    private $context;

    protected function setUp()
    {
        $this->policy = new AlwaysRetryPolicy();
        $this->context = $this->policy->open();
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

