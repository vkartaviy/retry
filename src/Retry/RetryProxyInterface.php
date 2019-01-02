<?php

declare(strict_types=1);

namespace Retry;

interface RetryProxyInterface
{
    function call(callable $action, array $arguments = []): mixed;
}
