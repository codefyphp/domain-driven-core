<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Qubus\ValueObjects\StringLiteral\StringLiteral;

final class Title extends StringLiteral
{
    public static function fromString(string $title): self
    {
        return new self(value: $title);
    }
}
