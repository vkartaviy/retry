<?php

declare(strict_types=1);

namespace Keboola\Retry\Test\Fixtures;

class MockRetryClass
{
    /** @var  int */
    public $attempts;

    /** @var  int */
    public $attemptsBeforeSuccess;

    /** @var  \Throwable|null */
    public $exceptionToThrow;

    public function __construct(int $attemptsBeforeSuccess, ?\Throwable $exceptionToThrow = null)
    {
        $this->attemptsBeforeSuccess = $attemptsBeforeSuccess;
        $this->exceptionToThrow = $exceptionToThrow;
    }

    public function action(): void
    {
        if (++$this->attempts < $this->attemptsBeforeSuccess) {
            throw $this->exceptionToThrow ?: new \Exception();
        }
    }
}
