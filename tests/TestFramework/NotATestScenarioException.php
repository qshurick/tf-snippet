<?php

declare(strict_types=1);

namespace Jff\Assignment\Test\TestFramework;


use RuntimeException;

final class NotATestScenarioException extends RuntimeException
{
    public static function fromFullyQualifiedClassName(string $fullyQualifiedClassName): self
    {
        return new self(
            sprintf('%s is not a test scenario', $fullyQualifiedClassName)
        );
    }
}
