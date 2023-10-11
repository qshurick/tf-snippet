<?php

declare(strict_types=1);

namespace Jff\Assignment\Test;

use Jff\Assignment\Test\TestFramework\AbstractTestScenario;

final class DummyTestScenarioTest extends AbstractTestScenario
{
    protected function getTestScenario(): array
    {
        return [
            'Dummy test' => [$this, 'should_be_called'],
            'Dummy test with assertion' => [$this, 'should_have_assertion'],
            'Dummy test that always fails' => [$this, 'should_fail'],
        ];
    }

    public function should_be_called(): void
    {
    }

    public function should_have_assertion(): void
    {
        self::assert(true, 'Always true');
    }

    public function should_fail(): void
    {
        self::fail('Should always fail');
    }
}
