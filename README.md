Retry
=====

The library for repeatable and retryable operations.  
(Forked from https://github.com/vkartaviy/retry)

[![Build Status](https://travis-ci.com/keboola/retry.svg?branch=master)](https://travis-ci.com/keboola/retry)


Here is a simple example:

```php
<?php

use Retry\RetryProxy;
use Retry\Policy\SimpleRetryPolicy;
use Retry\BackOff\ExponentialBackOffPolicy;

$retryPolicy = new SimpleRetryPolicy(3);
$backOffPolicy = new ExponentialBackOffPolicy();

$proxy = new RetryProxy($retryPolicy, $backOffPolicy);
$result = $proxy->call(function() {
    // call external service and return result
});
```

If you want to supply your own retry decider method you can by using the CallableRetryPolicy

```php
<?php

use Retry\RetryProxy;
use Retry\Policy\SimpleRetryPolicy;
use Retry\BackOff\ExponentialBackOffPolicy;

$retryPolicy = new CallableRetryPolicy(function (\Throwable $e) {
    if ($e->getCode() === 200) {
        return false;
    } 
    return true;
});
$proxy = new RetryProxy($retryPolicy, new ExponentialBackOffPolicy());
$result = $proxy->call(function() {
   // call external service and return result
});
```
