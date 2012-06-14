<?php

namespace Batch\Test\Retry\Policy;

use Batch\Retry\Policy\TimeoutRetryPolicy;
use Batch\Retry\RetryContext;

 */
class TimeoutRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeoutRetryPolicy
     */
    private $policy;
    /**
     * @var RetryContext
     */
    private $context;

    protected function setUp()
    {
        $this->policy = new TimeoutRetryPolicy();
        $this->context = new RetryContext();
    }

    protected function tearDown()
    {
        $this->policy = null;
        $this->context = null;
    }

    public function testTimeoutPreventsRetry()
    {
        $this->policy->setTimeout(100);
        $this->policy->start($this->context);
        $this->policy->registerException($this->context, new \RuntimeException());
        $this->assertTrue($this->policy->canRetry($this->context));
        usleep(50 * 1000);
        $this->assertTrue($this->policy->canRetry($this->context));
        usleep(50 * 1000);
        $this->assertFalse($this->policy->canRetry($this->context));
    }

    public function testRetryCount()
    {
        $this->policy->start($this->context);
        $this->policy->registerException($this->context, new \RuntimeException('foo'));
        $this->assertEquals(1, count($this->context->getRetryCount()));
        $this->assertEquals('foo', $this->context->getLastException()->getMessage());
    }
}

