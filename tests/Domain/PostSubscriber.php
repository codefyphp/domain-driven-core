<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\EventBus\DomainEventSubscriber;
use Codefy\Traits\SubscriberAware;

final class PostSubscriber implements DomainEventSubscriber
{
    use SubscriberAware;

    protected array $eventType = [
        PostWasCreated::class,
        TitleWasChanged::class,
    ];

    public function __construct(public readonly PostProjection $projection)
    {
    }

    public function handle(TitleWasChanged|PostWasCreated $event): void
    {
        match (true) {
            get_class($event) === PostWasCreated::class => $this->projection->projectWhenPostWasCreated(
                event: $event
            ),
            get_class($event) === TitleWasChanged::class => $this->projection->projectWhenTitleWasChanged(
                event: $event
            ),
            default => 'event not subscribed'
        };
    }
}
