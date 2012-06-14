<?php

namespace Batch\Retry\BackOff;

class BackOffContext extends \ArrayObject
{
    public function __construct()
    {
    }

    public function __toString()
    {
        return get_class($this) . '@' . spl_object_hash($this) . ' ' . json_encode($this);
    }
}
