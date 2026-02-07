<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Qubus\ValueObjects\StringLiteral\StringLiteral;

final class Content extends StringLiteral
{
    public static function fromString(string $content): self
    {
        return new self(value: $content);
    }
}
