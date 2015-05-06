Batch
=====

The library for repeatable and retryable operations.

[![Build Status](https://secure.travis-ci.org/vkartaviy/retry.png?branch=master)](http://travis-ci.org/vkartaviy/retry)


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