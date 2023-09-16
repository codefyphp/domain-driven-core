<?php

use Codefy\CommandBus\Container;
use Codefy\CommandBus\Containers\InjectorContainer;
use Codefy\Domain\Aggregate\AggregateRepository;
use Codefy\Domain\EventSourcing\EventStore;
use Codefy\Domain\EventSourcing\InMemoryEventStore;
use Codefy\Domain\EventSourcing\Projection;
use Codefy\EventBus\CommandEventBus;
use Codefy\EventBus\DomainEventPublisher;
use Codefy\EventBus\EventBus;
use Codefy\EventBus\NullPublisher;
use Codefy\Tests\Domain\InMemoryPostProjection;
use Codefy\Tests\Domain\PostProjection;
use Codefy\Tests\Domain\PostRepository;
use Qubus\Injector\Injector;

return [
    'container' => [
        Injector::STANDARD_ALIASES => [
            Container::class => InjectorContainer::class,
            PostProjection::class => InMemoryPostProjection::class,
            AggregateRepository::class => PostRepository::class,
            EventStore::class => InMemoryEventStore::class,
            EventBus::class => CommandEventBus::class,
            Projection::class => InMemoryPostProjection::class,
            DomainEventPublisher::class => NullPublisher::class,
        ]
    ]
];
