<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\Domain\Aggregate\AggregateRoot;
use Codefy\Domain\Aggregate\EventSourcedAggregate;

final class CoreRegressionAlternateAggregate extends EventSourcedAggregate implements AggregateRoot
{
    public static function create(CoreRegressionAlternateId $aggregateId): self
    {
        return self::root($aggregateId);
    }
}
