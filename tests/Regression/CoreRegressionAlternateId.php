<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\Domain\Aggregate\AggregateId;
use Qubus\ValueObjects\Identity\Uuid;

final class CoreRegressionAlternateId extends Uuid implements AggregateId
{
    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function aggregateClassName(): string
    {
        return CoreRegressionAlternateAggregate::class;
    }
}
