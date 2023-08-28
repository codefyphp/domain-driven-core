<?php

declare(strict_types=1);

namespace Codefy\Tests;

use Qubus\Exception\Data\TypeException;
use Qubus\ValueObjects\StringLiteral\StringLiteral;

final class Content extends StringLiteral
{
    /**
     * @throws TypeException
     */
    public static function fromString(string $content): self
    {
        return new self(value: $content);
    }
}
