<?php

declare(strict_types=1);

namespace Jff\Assignment\Test\TestFramework;

interface TestScenarioInterface
{
    public function run(): TestResult;
}
