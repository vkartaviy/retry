<?php

declare(strict_types=1);

namespace Retry;

interface RetryProxyInterface
{
    public function call(callable $action, array $arguments = []): mixed;
}
