<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\Domain\Aggregate\AggregateRoot;
use Codefy\Domain\Aggregate\EventSourcedAggregate;
use Codefy\Tests\Domain\PostId;

final class CoreRegressionCounter extends EventSourcedAggregate implements AggregateRoot
{
    private int $count = 0;

    public static function create(PostId $aggregateId): self
    {
        return self::root($aggregateId);
    }

    public function increment(): void
    {
        $this->recordApplyAndPublishThat(
            CoreRegressionCounterIncremented::occur($this->aggregateId(), [])
        );
    }

    public function countValue(): int
    {
        return $this->count;
    }

    protected function whenCoreRegressionCounterIncremented(CoreRegressionCounterIncremented $event): void
    {
        $this->count++;
    }
}
