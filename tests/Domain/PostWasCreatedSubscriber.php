<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\EventBus\DomainEventSubscriber;
use Codefy\Traits\SubscriberAware;

final class PostWasCreatedSubscriber implements DomainEventSubscriber
{
    use SubscriberAware;

    protected array $eventType = [
        PostWasCreated::class,
    ];

    public function __construct(public readonly PostProjection $projection)
    {
    }

    public function handle(PostWasCreated $event): void
    {
        $this->projection->projectWhenPostWasCreated(event: $event);
    }
}
