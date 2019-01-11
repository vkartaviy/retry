<?php

declare(strict_types=1);

namespace Keboola\Retry;

interface RetryProxyInterface
{
    /**
     * @param callable $action
     * @param array $arguments
     * @return mixed
     */
    public function call(callable $action, array $arguments = []);
}
