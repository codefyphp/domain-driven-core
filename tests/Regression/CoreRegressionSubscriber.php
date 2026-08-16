<?php

declare(strict_types=1);

namespace Codefy\Tests\Regression;

use Codefy\Domain\EventSourcing\DomainEvent;
use Codefy\EventBus\DomainEventSubscriber;

final class CoreRegressionSubscriber implements DomainEventSubscriber
{
    /** @var list<DomainEvent> */
    public array $events = [];

    public function isSubscribedTo(DomainEvent $event): bool
    {
        return $event instanceof CoreRegressionCounterIncremented;
    }

    public function handle(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
