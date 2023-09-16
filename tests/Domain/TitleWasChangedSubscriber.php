<?php

declare(strict_types=1);

namespace Codefy\Tests\Domain;

use Codefy\EventBus\DomainEventSubscriber;
use Codefy\Traits\SubscriberAware;

final class TitleWasChangedSubscriber implements DomainEventSubscriber
{
    use SubscriberAware;

    protected array $eventType = [
        TitleWasChanged::class,
    ];

    public function __construct(public readonly PostProjection $projection)
    {
    }

    public function handle(TitleWasChanged $event): void
    {
        $this->projection->projectWhenTitleWasChanged(event: $event);
    }
}
