<?php

declare(strict_types=1);

namespace Retry\Test\Policy;

use Retry\Policy\TimeoutRetryPolicy;

class TimeoutRetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeoutRetryPolicy
     */
    private $policy;

    protected function setUp()
    {
        $this->policy = new TimeoutRetryPolicy();
    }

    protected function tearDown()
    {
        $this->policy = null;
    }

    public function testTimeoutPreventsRetry()
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

    public function testRetryCount()
    {
        $context = $this->policy->open();

        $this->policy->registerException($context, new \RuntimeException('foo'));
        $this->assertEquals(1, $context->getRetryCount());
        $this->assertEquals('foo', $context->getLastException()->getMessage());
    }
}

