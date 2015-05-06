<?php

namespace Retry\Test\Fixtures;

class MockRetryClass
{
    public $attempts;
    public $attemptsBeforeSuccess;
    public $exceptionToThrow;

    public function __construct($attemptsBeforeSuccess, \Exception $exceptionToThrow = null)
    {
        $this->attemptsBeforeSuccess = (int) $attemptsBeforeSuccess;
        $this->exceptionToThrow = $exceptionToThrow;
    }

    public function action()
    {
        if (++$this->attempts < $this->attemptsBeforeSuccess) {
            throw $this->exceptionToThrow ?: new \Exception();
        }
    }
}
