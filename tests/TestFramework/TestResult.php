<?php

declare(strict_types=1);

namespace Jff\Assignment\Test\TestFramework;

final class TestResult
{
    private int $totalTests;
    private int $totalFailedTests;

    public function __construct(int $totalTests, int $totalFailedTests)
    {
        $this->totalTests = $totalTests;
        $this->totalFailedTests = $totalFailedTests;
    }

    public function getTotalTests(): int
    {
        return $this->totalTests;
    }

    public function getTotalFailedTests(): int
    {
        return $this->totalFailedTests;
    }

    public function combineWith(TestResult $testResult): TestResult
    {
        return new self(
            $this->getTotalTests() + $testResult->getTotalTests(),
            $this->getTotalFailedTests() + $testResult->getTotalFailedTests()
        );
    }
}
