[![Build Status](https://secure.travis-ci.org/vkartaviy/batch.png?branch=master)](http://travis-ci.org/vkartaviy/batch)

Batch
=====

The library for repeatable and retryable operations.

```php
<?php

use Batch\Retry\RetryProxy;
use Batch\Retry\Policy\SimpleRetryPolicy;
use Batch\Retry\BackOff\ExponentialBackOffPolicy;

$retryPolicy = new SimpleRetryPolicy(3);
$backOffPolicy = new ExponentialBackOffPolicy();

$proxy = new RetryProxy($retryPolicy, $backOffPolicy);
$result = $proxy->call(function() {
    // call external service and return result
});
```