<?php

declare(strict_types=1);

namespace Retry;

interface RetryProxyInterface
{
    /**
     * @param callable $action
     * @param array $arguments
     * @return mixed
     */
    public function call(callable $action, array $arguments = []);

    /**
     * @return int number of times the action was tried
     */
    public function getTryCount(): int;
}
