<?php

namespace Retry;

interface RetryProxyInterface
{
    function call(callable $action, array $arguments = []);
}
