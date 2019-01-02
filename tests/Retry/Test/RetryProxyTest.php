<?php

declare(strict_types=1);

namespace Test\Retry;

use Retry\RetryProxy;
use Retry\Policy\SimpleRetryPolicy;
use Retry\Policy\NeverRetryPolicy;
use Retry\BackOff\NoBackOffPolicy;
use Retry\Test\Fixtures\MockRetryClass;
use Retry\Test\Fixtures\MockBackOffStrategy;

class RetryProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessfulRetry()
    {
        for ($x = 1; $x <= 10; $x++) {
            $action = new MockRetryClass($x);
            $proxy = new RetryProxy(new SimpleRetryPolicy($x), new NoBackOffPolicy());
            $proxy->call(array($action, 'action'));
            $this->assertEquals($x, $action->attempts);
        }
    }

    public function testAlwaysTryAtLeastOnce()
    {
        $action = new MockRetryClass(1);
        $proxy = new RetryProxy(new NeverRetryPolicy());
        $proxy->call(array($action, 'action'));
        $this->assertEquals(1, $action->attempts);
    }

    public function testNoSuccessRetry()
    {
        $action = new MockRetryClass(PHP_INT_MAX, new \InvalidArgumentException());
        $proxy = new RetryProxy(new SimpleRetryPolicy(2));
        try {
            $proxy->call(array($action, 'action'));
            $this->fail('Expected InvalidArgumentException.');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(2, $action->attempts);
            return;
        }
        $this->fail('Expected InvalidArgumentException.');
    }

    public function testSetExceptions()
    {
        $action = new MockRetryClass(3);
        $proxy = new RetryProxy(new SimpleRetryPolicy(3, array('RuntimeException')));
        try {
            $proxy->call(array($action, 'action'));
        } catch (\Exception $e) {
            $this->assertEquals(1, $action->attempts);
        }
        $action->exceptionToThrow = new \RuntimeException();
        $proxy->call(array($action, 'action'));
        $this->assertEquals(3, $action->attempts);
    }

    public function testBackOffInvoked()
    {
        for ($x = 1; $x <= 10; $x++) {
            $action = new MockRetryClass($x);
            $backOff = new MockBackOffStrategy();
            $proxy = new RetryProxy(new SimpleRetryPolicy($x), $backOff);
            $proxy->call(array($action, 'action'));
            $this->assertEquals($x, $action->attempts);
            $this->assertEquals(1, $backOff->initCalls);
            $this->assertEquals($x - 1, $backOff->backOffCalls);
        }
    }

    public function testRethrowError()
    {
        $proxy = new RetryProxy(new NeverRetryPolicy());
        try {
            $proxy->call(function(){
                throw new \ErrorException('Realllly bad!');
            });
            $this->fail('Expected Error');
        } catch (\ErrorException $e) {
            $this->assertEquals('Realllly bad!', $e->getMessage());
        }
    }
}
