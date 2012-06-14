<?php

namespace Batch\Retry;

interface RetryProxyInterface
{
    function call($action, array $arguments = array());
}
