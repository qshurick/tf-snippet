<?php

declare(strict_types=1);

namespace Jff\Assignment\Test\TestFramework;

use RuntimeException;

final class TestFailedException extends RuntimeException
{
    public static function withReason(string $reason): self
    {
        return new self($reason);
    }
}
