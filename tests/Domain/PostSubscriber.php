<?php

/**
 * CodefyPHP
 *
 * @link       https://github.com/codefyphp/codefy
 * @copyright  2022 Joshua Parker <josh@joshuaparker.blog>
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

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

    public function handle(TitleWasChanged|PostWasCreated $event)
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
