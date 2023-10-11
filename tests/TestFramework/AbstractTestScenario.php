<?php

declare(strict_types=1);

namespace Jff\Assignment\Test\TestFramework;

use Throwable;

abstract class AbstractTestScenario implements TestScenarioInterface
{
    /**
     * @return callable[]
     */
    abstract protected function getTestScenario(): array;

    final protected static function fail(string $reason): void
    {
        throw TestFailedException::withReason($reason);
    }

    final protected static function assert(bool $testCondition, string $reason): void
    {
        if (!$testCondition) {
            self::fail($reason);
        }
    }

    public function run(): TestResult
    {
        $testScenarios = $this->getTestScenario();
        $testResult = new TestResult(0, 0);

        printf("Running test %s\n", get_class($this));

        foreach ($testScenarios as $scenarioName => $testScenario) {
            $testScenarioResult = $this->runScenario($scenarioName, $testScenario);
            $testResult = $testResult->combineWith($testScenarioResult);
        }

        $haveAllTestsPassSuccessfully = $testResult->getTotalFailedTests() === 0;
        printf(
            "\n%s: %d out of %d tests are passes\n",
            $haveAllTestsPassSuccessfully
                ? 'Success'
                : 'Failed',
            $testResult->getTotalTests() - $testResult->getTotalFailedTests(),
            $testResult->getTotalTests()
        );

        return $testResult;
    }

    private function runScenario($scenarioName, callable $testScenario): TestResult
    {
        $testResult = new TestResult(1, 0);
        $logEntry = sprintf("\t%s ", $scenarioName);

        try {
            $testScenario();
            $logEntry .= "PASS\n";
        } catch (Throwable $exception) {
            $logEntry .= "FAILED\n";
            $logEntry .= "\t\t{$exception->getMessage()}\n";
            $testResult = $testResult->combineWith(new TestResult(0, 1));
        } finally {
            printf('%s', $logEntry);
        }

        return $testResult;
    }
}
