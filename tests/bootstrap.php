<?php

declare(strict_types=1);

use Jff\Assignment\Test\TestFramework\NotATestScenarioException;
use Jff\Assignment\Test\TestFramework\TestResult;
use Jff\Assignment\Test\TestFramework\TestScenarioInterface;

require "vendor/autoload.php";

function get_fully_qualified_class_name_from_file(string $filePath): string {
    $filePointer = fopen($filePath, 'r');
    $class = $namespace = $buffer = '';
    while (!$class) {
        if (feof($filePointer)) break;

        $buffer .= fread($filePointer, 512);
        if (preg_match('/class\s+(\w+)(.*)?\{?/', $buffer, $classMatches)) {
            $class = $classMatches[1];
        }
        if (preg_match('/namespace\s+([\w\\\\]+);/', $buffer, $namespaceMatches)) {
            $namespace = $namespaceMatches[1];
        }
    }

    if (!$namespace || !$class) {
        throw new NotATestScenarioException();
    }
    return "$namespace\\$class";
}

/**
 * @return string[]
 */
function review_directory(RecursiveDirectoryIterator $directoryIterator): array {
    $testScenarios = [];
    foreach ($directoryIterator as $fileInfo) {
        if ($fileInfo->isFile() && preg_match('/.*Test\.php$/', $fileInfo->getFilename()) != false) {
            try {
                $filePath = "{$fileInfo->getPath()}/{$fileInfo->getFilename()}";
                $testScenarioClassName = get_fully_qualified_class_name_from_file($filePath);
                $testScenarios[] = $testScenarioClassName;
            } catch (NotATestScenarioException $exception) {
                // ignore this case
            }
        }

        if ($fileInfo->isDir() && $fileInfo->getFilename() !== '.' && $fileInfo->getFilename() !== '..') {
            $testScenarios = array_merge(
                $testScenarios,
                review_directory($directoryIterator->getChildren())
            );
        }
    }

    return $testScenarios;
}

function run_tests(TestScenarioInterface ...$testScenarios): TestResult {
    $testResult = new TestResult(0, 0);
    foreach ($testScenarios as $testScenario) {
        try {
            $testResult = $testResult->combineWith($testScenario->run());
        } catch (Throwable $exception) {
            print('Not able to run tests');
        }
    }

    return $testResult;
}

printf("\nRunning all tests\n");

$testResult = run_tests(
    ...array_map(
        fn (string $testFQCN): TestScenarioInterface => new $testFQCN(),
        review_directory(new RecursiveDirectoryIterator("tests"))
    )
);

printf(
    "\n%s: %d out of %d tests are passes\n",
   $testResult->getTotalFailedTests() === 0
        ? 'Success'
        : 'Failed',
    $testResult->getTotalTests() - $testResult->getTotalFailedTests(),
    $testResult->getTotalTests()
);

