<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\Projection;
use Codefy\Traits\EventSourcedRepositoryAware;

class EventSourcedPostRepository implements AggregateRepository
{
    use EventSourcedRepositoryAware;

    public function __construct(protected EventStore $eventStore, protected Projection $projection)
    {
    }
}
